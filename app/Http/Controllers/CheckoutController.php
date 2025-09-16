<?php

namespace App\Http\Controllers;

use App\Mail\OrderPaidMail;
use App\Mail\WelcomeMail;
use Carbon\Carbon;
use App\Models\{Customer, DiscountCode, Order, Product, User};
use App\Services\MyParcelService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{DB, Hash, Log, Mail, Storage};
use Illuminate\Validation\ValidationException;
use Mollie\Api\MollieApiClient;

class CheckoutController extends Controller
{
  protected MollieApiClient $mollie;

  public function __construct(MollieApiClient $mollie)
  {
    $this->mollie = $mollie;
    $this->mollie->setApiKey(config('mollie.key'));
  }

  /* ------------------ Checkout Flow ------------------ */

  public function create()
  {
    $cart = session('cart', []);
    if (empty($cart)) {
      return redirect('/winkel')->with('error', 'Je winkelwagen is leeg.');
    }
    $totaalZonderVerzendkosten = collect($cart)->sum(fn($item) => $item['price'] * $item['quantity']);
    return view('checkout.index', compact('cart', 'totaalZonderVerzendkosten'));
  }

  public function store(Request $request)
  {

  $cart = session('cart', []);
  if (empty($cart)) {
    return redirect('/winkel')->with('error', 'Je winkelwagen is leeg.');
  }

  $this->normalizeCheckboxes($request);

  try {
    $this->validateCheckout($request);

    // Extra validatie: check of er een geldige shipping_cost bestaat
    $country = $request->boolean('alt-shipping')
      ? $request->input('shipping_country', 'NL')
      : $request->input('billing_country', 'NL');
    $shippingCost = \App\Models\ShippingCost::where('country', $country)
      ->where('is_published', 1)
      ->orderByDesc('amount')
      ->first();
    if (!$shippingCost) {
      throw ValidationException::withMessages([
        $request->boolean('alt-shipping') ? 'shipping_country' : 'billing_country' => 'Geen verzendkosten beschikbaar voor het gekozen land.'
      ]);
    }
  } catch (ValidationException $e) {
    // Redirect back with validation errors, do NOT create order or redirect to payment
     return redirect()->route('storeCheckout')->withErrors($e->validator)->withInput();
  }

    // Only proceed if validation passes
    $user = $this->createUserIfNeeded($request);
    $customer = $this->createOrUpdateCustomer($request);

    [$discountCode, $discountType, $discountValue, $discountAmount, $totalBefore, $totalAfter] =
      $this->calculateDiscount($cart);

    $order = $this->createOrderWithItems(
      $cart,
      $customer,
      $request,
      $discountCode,
      $discountType,
      $discountValue,
      $discountAmount,
      $totalBefore,
      $totalAfter
    );

    $this->validateAndSaveMyParcel($order, $request);

    // In de store() methode, gebruik het totaal inclusief verzendkosten voor de betaling
    $amountToPay = $order->total_with_shipping ?? $order->total_after_discount;
    return $this->createMolliePayment($order, $amountToPay);
  }

  public function paymentSuccess(Request $request)
  {
    $order = Order::find($request->query('order'));
    if (!$order) {
      return $this->checkoutError('Order niet gevonden.');
    }
    if (!$order->mollie_payment_id) {
      return $this->checkoutError('Geen betaling gevonden bij deze order.');
    }

    $payment = $this->mollie->payments->get($order->mollie_payment_id);

    if ($payment->isPaid()) {
      return $this->handlePaidOrder($order);
    }

    if ($payment->isOpen() || $payment->isPending()) {
      return $this->checkoutInfo('Je betaling is nog niet afgerond.');
    }

    $order->update(['status' => 'cancelled', 'payment_status' => 'failed']);
    return $this->checkoutError('Je betaling is mislukt of geannuleerd.');
  }

  public function checkoutSuccess()
  {
    $orderId = session('checkout_success_order_id');
    if (!$orderId) return redirect()->route('home');

    $order = Order::with('items')->find($orderId);

    $delivery = json_decode($order->myparcel_delivery_json, true);
    $pickupLocation = '';
    if (!empty($delivery['deliveryType']) && strtolower($delivery['deliveryType']) === 'pickup') {
        $pickupLocation = $delivery['pickup'] ?? $delivery['pickupLocation'] ?? null;
    }

    return view('checkout.success', 
    ['success' => true, 
      'order' => $order, 
      'pickupLocation' => $pickupLocation, 
      'delivery' => $delivery
    ]);
  }

  public function paymentWebhook(Request $request)
  {
    $paymentId = $request->input('id');
    if (!$paymentId) return response()->json(['error' => 'No payment id'], 400);

    $payment = $this->mollie->payments->get($paymentId);
    $order = Order::find($payment->metadata->order_id ?? null);

    if ($order) {
      $this->updateOrderPaymentStatus($order, $payment);
    }

    return response()->json(['status' => 'ok']);
  }

  /* ------------------ Discount ------------------ */

  public function applyDiscountCode(Request $request)
  {
    $validated = $request->validate([
      'code' => 'required|string|max:255',
      'billing_email' => 'required|email|max:255',
    ]);
    $discount = DiscountCode::where('code', $validated['code'])->first();

    if (!$discount) {
      return response()->json(['success' => false, 'message' => 'Code bestaat niet.']);
    }

    // Check if code is published
    if ($discount->is_published === 0) {
      return response()->json(['success' => false, 'message' => 'Deze kortingscode is niet actief.']);
    }

    // Check expiration date
    if ($discount->expiration_date && Carbon::parse($discount->expiration_date)->lt(now()->startOfDay())) {
      return response()->json(['success' => false, 'message' => 'Deze kortingscode is verlopen.']);
    }

    // Check usage_limit (total times code can be used)
    $totalUsed = Order::where('discount_code_checkout', $discount->code)->count();
    if ($discount->usage_limit && $totalUsed >= $discount->usage_limit) {
      return response()->json(['success' => false, 'message' => 'Deze kortingscode is niet meer geldig.']);
    }

    // Check usage_limit_per_customer (per email)
    $customerUsed = Order::where('discount_code_checkout', $discount->code)
      ->whereHas('customer', function($q) use ($validated) {
        $q->where('billing_email', $validated['billing_email']);
      })
      ->count();
    if ($discount->usage_limit_per_customer && $customerUsed >= $discount->usage_limit_per_customer) {
      return response()->json(['success' => false, 'message' => 'Je hebt deze kortingscode al te vaak gebruikt.']);
    }

    session(['discount_code' => $discount->code]);

    $cart = session('cart', []);
    $total = collect($cart)->sum(fn($item) => $item['price'] * $item['quantity']);
    $discountAmount = $discount->discount_type === 'percent'
      ? $total * ($discount->discount / 100)
      : $discount->discount;

    return response()->json([
      'success' => true,
      'discount' => $discount,
      'discount_amount' => round($discountAmount, 2),
      'total' => round($total, 2),
      'new_total' => max(0, round($total - $discountAmount, 2)),
    ]);
  }

  public function removeDiscountCode()
  {
    session()->forget('discount_code');
    $cart = session('cart', []);
    $total = collect($cart)->sum(fn($item) => $item['price'] * $item['quantity']);

    return response()->json([
      'success' => true,
      'discount_amount' => 0,
      'total' => round($total, 2),
      'new_total' => round($total, 2),
    ]);
  }

  /* ------------------ Private Helpers ------------------ */

  private function normalizeCheckboxes(Request $request): void
  {
    $request->merge(['alt-shipping' => $request->has('alt-shipping') ? 1 : 0]);
  }

  private function validateCheckout(Request $request): void
  {
    $request->validate([
      'billing_email' => 'required|email',
      'billing_first_name' => 'required|string',
      'billing_last_name' => 'required|string',
      'billing_street' => 'required|string',
      'billing_house_number' => 'required|numeric',
      'billing_house_number-add' => 'nullable|string',
      'billing_postal_code' => 'required|string',
      'billing_city' => 'required|string',
      'billing_country' => 'required|string',
      'password' => 'nullable|string|min:8|confirmed',
      'shipping_first_name' => 'required_if:alt-shipping,1|string|nullable',
      'shipping_last_name' => 'required_if:alt-shipping,1|string|nullable',
      'shipping_street' => 'required_if:alt-shipping,1|string|nullable',
      'shipping_house_number' => 'required_if:alt-shipping,1|numeric|nullable',
      'shipping_house_number-add' => 'nullable|string',
      'shipping_postal_code' => 'required_if:alt-shipping,1|string|nullable',
      'shipping_city' => 'required_if:alt-shipping,1|string|nullable',
      'shipping_country' => 'required_if:alt-shipping,1|string|nullable',
    ], [
      'billing_email.required' => 'Het e-mailadres is verplicht.',
      'billing_email.email' => 'Voer een geldig e-mailadres in.',
      'billing_first_name.required' => 'De voornaam is verplicht.',
      'billing_last_name.required' => 'De achternaam is verplicht.',
      'billing_street.required' => 'De straat is verplicht.',
      'billing_house_number.required' => 'Het huisnummer is verplicht.',
      'billing_house_number.numeric' => 'Het huisnummer moet een getal zijn.',
      'billing_postal_code.required' => 'De postcode is verplicht.',
      'billing_city.required' => 'De plaats is verplicht.',
      'billing_country.required' => 'Het land is verplicht.',
      'password.min' => 'Het wachtwoord moet minimaal 8 tekens bevatten.',
      'password.confirmed' => 'De wachtwoorden komen niet overeen.',
      'shipping_first_name.required_if' => 'De voornaam is verplicht.',
      'shipping_last_name.required_if' => 'De achternaam is verplicht.',
      'shipping_street.required_if' => 'De straat is verplicht.',
      'shipping_house_number.required_if' => 'Het huisnummer is verplicht.',
      'shipping_house_number.numeric' => 'Het huisnummer moet een getal zijn.',
      'shipping_postal_code.required_if' => 'De postcode is verplicht.',
      'shipping_city.required_if' => 'De plaats is verplicht.',
      'shipping_country.required_if' => 'Het land is verplicht.',
    ]);
  }

  private function createUserIfNeeded(Request $request): ?User
  {
    if (User::where('email', $request->billing_email)->exists() || !$request->filled('password')) {
      return null;
    }

    $user = User::create([
      'first_name' => $request->billing_first_name,
      'last_name' => $request->billing_last_name,
      'email' => $request->billing_email,
      'password' => Hash::make($request->password),
    ]);

    event(new Registered($user));
    Mail::to($user->email)->send(new WelcomeMail($user));

    return $user;
  }

  private function createOrUpdateCustomer(Request $request): Customer
  {
    return Customer::updateOrCreate(
      ['billing_email' => $request->billing_email],
      $request->only([
        'billing_first_name', 'billing_last_name', 'billing_email', 'billing_company',
        'billing_street', 'billing_house_number', 'billing_house_number-add',
        'billing_postal_code', 'billing_city', 'billing_country', 'billing_phone'
      ])
    );
  }

  private function calculateDiscount(array $cart): array
  {
    $totalBefore = collect($cart)->sum(fn($item) => $item['price'] * $item['quantity']);
    $discountCode = session('discount_code');
    $discountType = $discountValue = $discountAmount = 0;

    if ($discountCode && ($discount = DiscountCode::where('code', $discountCode)->first())) {
      $discountType = $discount->discount_type;
      $discountValue = $discount->discount;

      $discountAmount = $discountType === 'percent'
        ? round($totalBefore * ($discountValue / 100), 2)
        : round($discountValue, 2);

      $discountAmount = min($discountAmount, $totalBefore);
    }

    $totalAfter = round($totalBefore - $discountAmount, 2);

    return [$discountCode, $discountType, $discountValue, $discountAmount, round($totalBefore, 2), $totalAfter];
  }

  private function createOrderWithItems(
    array $cart,
    Customer $customer,
    Request $request,
    ?string $discountCode,
    ?string $discountType,
    float|int $discountValue,
    float $discountAmount,
    float $totalBefore,
    float $totalAfter
  ): Order {
    return DB::transaction(function () use (
      $cart, $customer, $request, $discountCode, $discountType,
      $discountValue, $discountAmount, $totalBefore, $totalAfter
    ) {
      // Zoek juiste shipping cost op basis van gekozen land
      $country = $request->boolean('alt-shipping')
        ? $request->input('shipping_country', 'NL')
        : $request->input('billing_country', 'NL');
      $shippingCost = \App\Models\ShippingCost::where('country', $country)
        ->where('is_published', 1)
        ->orderByDesc('amount')
        ->first();

      $shippingAmount = $shippingCost?->amount ?? 0;
      $totalWithShipping = $totalAfter + $shippingAmount;
      $order = $customer->orders()->create([
        'total_before' => $totalBefore, // vóór korting en verzendkosten
        'total' => $totalWithShipping, // altijd na korting + verzendkosten
        'status' => 'pending',
        'shipping_first_name' => $request->input('alt-shipping') ? $request->shipping_first_name : $request->billing_first_name,
        'shipping_last_name'  => $request->input('alt-shipping') ? $request->shipping_last_name  : $request->billing_last_name,
        'shipping_company'    => $request->input('alt-shipping') ? $request->shipping_company    : $request->billing_company,
        'shipping_street'     => $request->input('alt-shipping') ? $request->shipping_street     : $request->billing_street,
        'shipping_house_number' => $request->input('alt-shipping') ? $request->shipping_house_number : $request->billing_house_number,
        'shipping_postal_code'  => $request->input('alt-shipping') ? $request->shipping_postal_code : $request->billing_postal_code,
        'shipping_city'       => $request->input('alt-shipping') ? $request->shipping_city : $request->billing_city,
        'shipping_country'    => $request->input('alt-shipping') ? $request->shipping_country : $request->billing_country,
        'discount_type'       => $discountType,
        'discount_value'      => $discountValue,
        'discount_price_total' => $discountAmount,
        'total_after_discount' => $totalAfter,
        'discount_code_checkout' => $discountCode,
        'shipping_cost_id'    => $shippingCost?->id,
        'shipping_cost'       => $shippingAmount,
        'shipping_cost_amount'=> $shippingAmount,
        'total_with_shipping' => $totalWithShipping,
      ]);

      foreach ($cart as $item) {
        $product = Product::lockForUpdate()->find($item['product_id']);
        if (!$product || $product->stock < $item['quantity']) {
          throw ValidationException::withMessages([
            'stock' => "Niet voldoende voorraad voor {$item['name']}"
          ]);
        }

        $product->decrement('stock', $item['quantity']);
        $order->items()->create([
          'product_id' => $product->id,
          'product_name' => $item['name'],
          'product_copy_id' => $item['product_copy_id'] ?? null,
          'quantity' => $item['quantity'],
          'unit_price' => $item['price'],
          'subtotal' => $item['price'] * $item['quantity'],
        ]);
      }

      return $order;
    });
  }

  private function validateAndSaveMyParcel(Order $order, Request $request): void
  {
    $deliveryJson = $request->input('myparcel_delivery_options');
    if (!$deliveryJson) {
      throw ValidationException::withMessages([
        'myparcel_delivery_options' => 'Kies een bezorgoptie voordat je de bestelling plaatst.'
      ]);
    }

    $delivery = json_decode($deliveryJson, true) ?? [];
    $isPickup = strtolower($delivery['deliveryType'] ?? '') === 'pickup';

    if ($isPickup && empty($delivery['pickup'] ?? $delivery['pickupLocation'])) {
      throw ValidationException::withMessages([
        'myparcel_delivery_options' => 'Kies eerst een volledig afhaalpunt.'
      ]);
    }

    // ✅ Build full address (billing or shipping fallback)
    $address = [
      'cc'        => $order->shipping_country ?? $order->billing_country,
      'city'      => $order->shipping_city ?? $order->billing_city,
      // ✅ normalize: strip spaces + uppercase
      'postalCode'=> strtoupper(preg_replace('/\s+/', '', $order->shipping_postal_code ?? $order->billing_postal_code)),
      'street'    => $order->shipping_street ?? $order->billing_street,
      'number'    => $order->shipping_house_number ?? $order->billing_house_number,
      'addition'  => $order->shipping_house_number_addition ?? $order->billing_house_number_addition ?? '',
      'name'      => trim(($order->shipping_first_name ?? $order->billing_first_name) . ' ' .
        ($order->shipping_last_name ?? $order->billing_last_name)),
      'company'   => $order->shipping_company ?? $order->billing_company,
      'email'     => $order->customer->billing_email,
      'phone'     => $order->shipping_phone ?? $order->billing_phone,
    ];

    // Save delivery options on order
    $order->update([
      'myparcel_delivery_json'   => $deliveryJson,
      'myparcel_is_pickup'       => $isPickup,
      'myparcel_carrier'         => $delivery['carrier'] ?? 'postnl',
      'myparcel_delivery_type'   => $delivery['deliveryType'] ?? null,
      'myparcel_package_type_id' => $this->mapPackageTypeId($delivery['packageType'] ?? null),
    ]);

    Log::debug('MyParcel address debug', [
      'postal' => $address['postalCode'],
      'cc'     => $address['cc'],
      'street' => $address['street'],
      'number' => $address['number'],
    ]);

    try {
      $result = app(MyParcelService::class)->createShipment([
        'order_id' => $order->id,
        'reference' => 'order-' . $order->id,
        'carrier'  => $order->myparcel_carrier ?? 'postnl',
        // ✅ correct key
        'address'  => $address,
        'delivery' => [
          'packageTypeId' => $order->myparcel_package_type_id ?: 1,
          'deliveryType'  => $delivery['deliveryType'] ?? 'standard',
          'is_pickup'     => $isPickup,
          'pickup'        => $delivery['pickup'] ?? $delivery['pickupLocation'] ?? null,
          'onlyRecipient' => (bool) data_get($delivery, 'shipmentOptions.onlyRecipient'),
          'signature'     => (bool) data_get($delivery, 'shipmentOptions.signature'),
          'insurance'     => data_get($delivery, 'shipmentOptions.insurance'),
        ],
      ]);

      if (empty($result['consignment_id'])) {
        Log::error('MyParcel consignment_id missing', ['order' => $order->id, 'result' => $result]);
        throw ValidationException::withMessages([
          'myparcel_delivery_options' => 'Verzending aanmaken bij MyParcel is mislukt. Probeer het opnieuw.'
        ]);
      }

      $order->update([
        'myparcel_consignment_id'  => $result['consignment_id'],
        'myparcel_track_trace_url' => $result['track_trace_url'] ?? null,
        'myparcel_label_link'      => $result['label_link'] ?? null,
      ]);
    } catch (\Throwable $e) {
      Log::error('Checkout MyParcel shipment create failed', [
        'order' => $order->id,
        'error' => $e->getMessage(),
      ]);
    }
  }


  private function createMolliePayment(Order $order, float $amount)
  {
    $amount = max(0, $amount);

    if ($amount <= 0) {
      $order->update(['status' => 'completed', 'payment_status' => 'paid', 'paid_at' => now()]);
      session()->forget(['cart', 'discount_code']);
      return redirect()->route('payment.success', ['order' => $order->id]);
    }

    $payment = $this->mollie->payments->create([
      'amount' => ['currency' => 'EUR', 'value' => number_format($amount, 2, '.', '')],
      'description' => 'Bestelling #' . $order->id,
      'redirectUrl' => route('payment.success', ['order' => $order->id]),
      'webhookUrl' => config('app.webhook_url'),
      'metadata' => ['order_id' => $order->id],
    ]);

    $order->update(['mollie_payment_id' => $payment->id]);
    session()->forget(['cart', 'discount_code']);

    return redirect($payment->getCheckoutUrl(), 303);
  }

  private function handlePaidOrder(Order $order)
  {
    $order->update(['status' => 'completed', 'payment_status' => 'paid', 'paid_at' => now()]);

    // Invoice
    $pdf = Pdf::loadView('invoices.order', ['order' => $order])->output();
    $path = 'invoices/factuur_' . $order->id . '.pdf';
    Storage::disk('public')->put($path, $pdf);
    $order->forceFill(['invoice_pdf_path' => $path])->save();

    Mail::to($order->customer->billing_email)->send(new OrderPaidMail($order->fresh()));

    session()->forget('cart');
    session()->flash('checkout_success_order_id', $order->id);

    return redirect()->route('checkoutSuccessPage');
  }

  private function updateOrderPaymentStatus(Order $order, $payment): void
  {
    if ($payment->isPaid()) {
      $order->update(['status' => 'completed', 'payment_status' => 'paid', 'paid_at' => now()]);
    } elseif ($payment->isOpen() || $payment->isPending()) {
      $order->update(['status' => 'pending', 'payment_status' => 'pending']);
    } elseif ($payment->isFailed() || $payment->isExpired() || $payment->isCanceled()) {
      $order->update(['status' => 'cancelled', 'payment_status' => 'failed']);
    } elseif ($payment->status === 'refunded') {
      $order->update(['status' => 'cancelled', 'payment_status' => 'refunded']);
    }
  }

  private function checkoutError(string $msg)
  {
    return view('checkout.success', ['error' => $msg, 'success' => null, 'info' => null]);
  }

  private function checkoutInfo(string $msg)
  {
    return view('checkout.success', ['info' => $msg, 'success' => null, 'error' => null]);
  }

  private function mapPackageTypeId(?string $name): int
  {
    return [
      'package' => 1,
      'mailbox' => 2,
      'letter' => 3,
      'digital_stamp' => 4,
    ][$name] ?? 1;
  }
}
