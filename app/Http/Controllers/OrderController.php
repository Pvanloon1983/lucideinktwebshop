<?php

namespace App\Http\Controllers;

use App\Exports\OrdersExport;
use App\Mail\OrderPaidMail;
use App\Models\{Customer, Order, Product};
use App\Services\MyParcelService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Log, Mail, Storage};
use Maatwebsite\Excel\Facades\Excel;
use Mollie\Api\MollieApiClient;
use App\Http\Controllers\Concerns\streamPdf;

class OrderController extends Controller

{
    use streamPdf;

    protected MollieApiClient $mollie;

    public function __construct(MollieApiClient $mollie)
    {
        $this->middleware(['auth', 'role:admin']);
        $this->mollie = $mollie;
        $this->mollie->setApiKey(config('mollie.key'));
    }

    /**
     * Zet een string pakket type om naar het juiste MyParcel type ID
     */
    private function mapPackageTypeId($type): int
    {
        $types = [
            'package' => 1,
            'mailbox' => 2,
            'letter' => 3,
            'digital_stamp' => 4,
            'pakket' => 1,
            'brievenbuspakje' => 2,
            'brief' => 3,
            'digitale postzegel' => 4,
            1 => 1,
            2 => 2,
            3 => 3,
            4 => 4,
        ];
        return $types[$type] ?? 1;
    }

    public function index()
    {
        $this->authorize('viewAny', Order::class);

        $orders = Order::with(['items', 'customer'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('orders.index', compact('orders'));
    }

    public function show(string $id)
    {

        $order = Order::with(['items', 'customer'])->findOrFail($id);
        $this->authorize('view', $order);

        $consignmentId = $order->myparcel_consignment_id;
        $consignmentData = null;
        if ($consignmentId) {
            $consignmentData = app(MyParcelService::class)->findConsignmentById((int) $consignmentId);
        }

        $delivery = json_decode($order->myparcel_delivery_json, true);
        $pickupLocation = '';
        if (!empty($delivery['deliveryType']) && strtolower($delivery['deliveryType']) === 'pickup') {
            $pickupLocation = $delivery['pickup'] ?? $delivery['pickupLocation'] ?? null;
        }

        $items = $order->items()->paginate(10);
        $order->setRelation('items', $items);

        return view('orders.show', compact('order', 'pickupLocation'));
    }

    public function create()
    {
        $this->authorize('create', Order::class);
        $products = Product::with('category')->orderBy('title')->get();

        return view('orders.create', compact('products'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', Order::class);

        $data = $this->validateOrder($request);

        [$lines, $totalBefore] = $this->buildOrderLines($data['items']);
        if (empty($lines)) {
            return back()->withErrors(['items' => 'Vul minstens één product in met een hoeveelheid > 0.'])->withInput();
        }

        [$discountValue, $discountType, $discountAmount, $totalAfter] =
            $this->calculateDiscount($totalBefore, (float)($data['discount_value'] ?? 0.0), $data['discount_type'] ?? null);

        $customer = $this->upsertCustomer($request);

        if ($stockError = $this->checkStock($lines)) {
            return back()->withErrors(['stock' => $stockError])->withInput();
        }

        $order = $this->createOrder(
            $customer,
            $totalBefore,
            $discountType,
            $discountValue,
            $discountAmount,
            $totalAfter,
            $request
        );

        $this->createOrderItems($order, $lines);
        $this->processMyParcel($order, $request);
        $this->createMolliePayment($order, $totalAfter);

        return back()
            ->with('success', 'Bestelling is geplaatst.')
            ->with('payment_link', $order->payment_link)
            ->with('chosen_items', $lines)
            ->with('total_before_discount', $totalBefore)
            ->with('discount_amount', $discountAmount)
            ->with('discount_value', $discountValue)
            ->with('discount_type', $discountType)
            ->with('total_after_discount', $totalAfter);
    }

    public function update(Request $request, string $id)
    {
        $order = Order::findOrFail($id);
        $this->authorize('update', $order);

        $request->validate([
            'order-status' => 'required|in:pending,shipped,cancelled,paid,completed',
            'payment-status' => 'required|in:pending,paid,failed,refunded'
        ], [
            'order-status.required' => 'Selecteer een geldige status.',
            'order-status.in' => 'De gekozen status is ongeldig.',
            'payment-status.required' => 'Selecteer een geldige status.',
            'payment-status.in' => 'De gekozen status is ongeldig.',
        ]);

        $order->update([
            'status' => $request->input('order-status'),
            'payment_status' => $request->input('payment-status'),
        ]);

        return back()->with('success', 'Bestelling is bijgewerkt');
    }

    public function get()
    {
        return redirect()->route('dashboard');
    }

    public function generateInvoice(string $id)
    {
        $order = Order::findOrFail($id);

        $pdf = Pdf::loadView('invoices.order', compact('order'))->output();
        $path = "invoices/factuur_{$order->id}.pdf";

        Storage::disk('public')->put($path, $pdf);
        $order->forceFill(['invoice_pdf_path' => $path])->save();

        return back()->with('success', 'Factuur is aangemaakt');
    }

    public function download_invoice(string $id)
    {
        $order = Order::findOrFail($id);

        if (auth()->user()->role !== 'admin') {
            abort(403, 'Je hebt geen toegang tot deze factuur.');
        }
        if (empty($order->invoice_pdf_path)) {
            abort(404, 'Factuur niet gevonden.');
        }

        return $this->streamInvoice($order);
    }

    public function sendOrderEmailWithInvoice(string $id)
    {
        $order = Order::findOrFail($id);
        Mail::to($order->customer->billing_email)->send(new OrderPaidMail($order->fresh()));

        return back()->with('success', 'E-mail met factuur is verstuurd');
    }

    public function exportOrders()
    {
        $now = Carbon::now()->format('d-m-Y_H-i');
        return Excel::download(new OrdersExport, "orders-{$now}.xlsx");
    }

    /* ------------------ Private Helpers ------------------ */

    private function validateOrder(Request $request): array
    {
        $request->merge(['alt-shipping' => $request->has('alt-shipping') ? 1 : 0]);

        return $request->validate([
            'items' => 'required|array',
            'items.*.qty' => 'nullable|integer|min:0',
            'discount_value' => 'nullable|numeric|min:0',
            'discount_type' => 'nullable|in:amount,percent',
            'billing_email' => 'required|email',
            'billing_first_name' => 'required|string',
            'billing_last_name' => 'required|string',
            'billing_street' => 'required|string',
            'billing_house_number' => 'required|numeric',
            'billing_house_number-add' => 'nullable|string',
            'billing_postal_code' => 'required|string',
            'billing_city' => 'required|string',
            'billing_country' => 'required|string',
            'shipping_first_name' => 'required_if:alt-shipping,1|string|nullable',
            'shipping_last_name' => 'required_if:alt-shipping,1|string|nullable',
            'shipping_street' => 'required_if:alt-shipping,1|string|nullable',
            'shipping_house_number' => 'required_if:alt-shipping,1|numeric|nullable',
            'shipping_house_number-add' => 'nullable|string',
            'shipping_postal_code' => 'required_if:alt-shipping,1|string|nullable',
            'shipping_city' => 'required_if:alt-shipping,1|string|nullable',
            'shipping_country' => 'required_if:alt-shipping,1|string|nullable',
            'myparcel_delivery_options' => 'nullable|string',
        ], [
            'items.required' => 'Er moeten producten worden toegevoegd.',
            'items.array' => 'De productenlijst is ongeldig.',
            'items.*.qty.integer' => 'De hoeveelheid moet een geheel getal zijn.',
            'items.*.qty.min' => 'De hoeveelheid kan niet negatief zijn.',
            'discount_value.numeric' => 'De korting moet een getal zijn.',
            'discount_value.min' => 'De korting kan niet negatief zijn.',
            'discount_type.in' => 'Het kortingstype is ongeldig.',
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

    private function calculateDiscount(float $totalBefore, float $discountValue, ?string $discountType = null): array
    {
        $discountAmount = 0.0;

        if ($discountValue > 0.0 && $discountType) {
            if ($discountType === 'percent') {
                $discountAmount = (float)number_format($totalBefore * ($discountValue / 100.0), 2, '.', '');
            } else {
                $discountAmount = (float)number_format($discountValue, 2, '.', '');
            }
        }

        $discountAmount = min($discountAmount, $totalBefore);
        $totalAfter = (float)number_format($totalBefore - $discountAmount, 2, '.', '');

        return [
            (float)number_format($discountValue, 2, '.', ''),
            $discountType,
            $discountAmount,
            $totalAfter
        ];
    }

    private function upsertCustomer(Request $request): Customer
    {
        $data = [
            'billing_first_name' => $request->billing_first_name,
            'billing_last_name' => $request->billing_last_name,
            'billing_email' => $request->billing_email,
            'billing_company' => $request->billing_company,
            'billing_street' => $request->billing_street,
            'billing_house_number' => $request->billing_house_number,
            'billing_house_number_addition' => $request->input('billing_house_number-add'),
            'billing_postal_code' => $request->billing_postal_code,
            'billing_city' => $request->billing_city,
            'billing_country' => $request->billing_country,
            'billing_phone' => $request->billing_phone,
        ];

        if ($request->boolean('alt-shipping')) {
            $data = array_merge($data, [
                'shipping_first_name' => $request->shipping_first_name,
                'shipping_last_name' => $request->shipping_last_name,
                'shipping_company' => $request->shipping_company,
                'shipping_street' => $request->shipping_street,
                'shipping_house_number' => $request->shipping_house_number,
                'shipping_house_number_addition' => $request->input('shipping_house_number-add'),
                'shipping_postal_code' => $request->shipping_postal_code,
                'shipping_city' => $request->shipping_city,
                'shipping_country' => $request->shipping_country,
                'shipping_phone' => $request->shipping_phone,
            ]);
        } else {
            $data = array_merge($data, [
                'shipping_first_name' => $request->billing_first_name,
                'shipping_last_name' => $request->billing_last_name,
                'shipping_company' => $request->billing_company,
                'shipping_street' => $request->billing_street,
                'shipping_house_number' => $request->billing_house_number,
                'shipping_house_number_addition' => $request->input('billing_house_number-add'),
                'shipping_postal_code' => $request->billing_postal_code,
                'shipping_city' => $request->billing_city,
                'shipping_country' => $request->billing_country,
                'shipping_phone' => $request->billing_phone,
            ]);
        }

        return Customer::updateOrCreate(
            ['billing_email' => $request->billing_email],
            $data
        );
    }

    private function buildOrderLines(array $rawItems): array
    {
        $productIds = array_keys($rawItems);
        $products = Product::whereIn('id', $productIds)->get()->keyBy('id');

        $lines = [];
        $total = 0.0;

        foreach ($rawItems as $id => $item) {
            $qty = (int) ($item['qty'] ?? 0);
            if ($qty < 1 || !isset($products[$id])) {
                continue;
            }

            $product = $products[$id];
            $subtotal = (float)$product->price * $qty;

            $lines[] = [
                'product_id' => $product->id,
                'title' => $product->title,
                'qty' => $qty,
                'unit_price' => (float)$product->price,
                'subtotal' => $subtotal,
            ];

            $total += $subtotal;
        }

        return [$lines, (float)number_format($total, 2, '.', '')];
    }

    private function checkStock(array $lines): ?string
    {
        $errors = [];

        foreach ($lines as $line) {
            $product = Product::find($line['product_id']);
            if ($product && $product->stock < $line['qty']) {
                $errors[] = "{$product->title} (op voorraad: {$product->stock})";
            }
        }

        return $errors
            ? 'Niet voldoende voorraad:<br>'.implode('<br>', $errors)
            : null;
    }

    private function createOrder(
        Customer $customer,
        float $totalBefore,
        ?string $discountType,
        float $discountValue,
        float $discountAmount,
        float $totalAfter,
        Request $request
    ): Order {
        return $customer->orders()->create([
            'status' => 'pending',
            'payment_status' => 'pending',
            'total' => $totalBefore,
            'total_after_discount' => $totalAfter,
            'discount_type' => $discountType,
            'discount_value' => $discountValue,
            'discount_price_total' => $discountAmount,

            // Billing snapshot
            'billing_first_name' => $request->billing_first_name,
            'billing_last_name' => $request->billing_last_name,
            'billing_email' => $request->billing_email,
            'billing_company' => $request->billing_company,
            'billing_street' => $request->billing_street,
            'billing_house_number' => $request->billing_house_number,
            'billing_house_number_addition' => $request->input('billing_house_number-add'),
            'billing_postal_code' => $request->billing_postal_code,
            'billing_city' => $request->billing_city,
            'billing_country' => $request->billing_country,
            'billing_phone' => $request->billing_phone,

            // Shipping snapshot
            'shipping_first_name' => $request->boolean('alt-shipping') ? $request->shipping_first_name : $request->billing_first_name,
            'shipping_last_name' => $request->boolean('alt-shipping') ? $request->shipping_last_name : $request->billing_last_name,
            'shipping_company' => $request->boolean('alt-shipping') ? $request->shipping_company : $request->billing_company,
            'shipping_street' => $request->boolean('alt-shipping') ? $request->shipping_street : $request->billing_street,
            'shipping_house_number' => $request->boolean('alt-shipping') ? $request->shipping_house_number : $request->billing_house_number,
            'shipping_house_number_addition' => $request->boolean('alt-shipping')
                ? $request->input('shipping_house_number-add')
                : $request->input('billing_house_number-add'),
            'shipping_postal_code' => $request->boolean('alt-shipping')
                ? $request->shipping_postal_code
                : $request->billing_postal_code,
            'shipping_city' => $request->boolean('alt-shipping') ? $request->shipping_city : $request->billing_city,
            'shipping_country' => $request->boolean('alt-shipping') ? $request->shipping_country : $request->billing_country,
            'shipping_phone' => $request->boolean('alt-shipping') ? $request->shipping_phone : $request->billing_phone,
        ]);
    }

    private function createOrderItems(Order $order, array $lines): void
    {
        foreach ($lines as $line) {
            $product = Product::find($line['product_id']);
            if ($product && $product->stock >= $line['qty']) {
                $product->decrement('stock', $line['qty']);
            }

            $order->items()->create([
                'product_id' => $line['product_id'],
                'product_name' => $line['title'],
                'quantity' => $line['qty'],
                'unit_price' => $line['unit_price'],
                'subtotal' => $line['subtotal'],
            ]);
        }
    }

    /* ------------------ MyParcel ------------------ */

    private function processMyParcel(Order $order, Request $request): void
    {
        $deliveryJson = $request->input('myparcel_delivery_options');
        $delivery = json_decode($deliveryJson ?? '[]', true) ?: [];
        $isPickup = strtolower($delivery['deliveryType'] ?? '') === 'pickup';

        if ($isPickup && empty($delivery['pickup'] ?? $delivery['pickupLocation'])) {
            return;
        }

        $order->update([
            'myparcel_delivery_json' => $deliveryJson,
            'myparcel_is_pickup' => $isPickup,
            'myparcel_carrier' => $delivery['carrier'] ?? 'postnl',
            'myparcel_delivery_type' => $delivery['deliveryType'] ?? null,
            'myparcel_package_type_id' => $this->mapPackageTypeId($delivery['packageType'] ?? null),
            'myparcel_only_recipient' => (bool) data_get($delivery, 'shipmentOptions.onlyRecipient'),
            'myparcel_signature' => (bool) data_get($delivery, 'shipmentOptions.signature'),
            'myparcel_insurance_amount' => data_get($delivery, 'shipmentOptions.insurance'),
        ]);

        $address = [
            'cc' => $order->shipping_country ?? $order->billing_country,
            'city' => $order->shipping_city ?? $order->billing_city,
            'postalCode' => strtoupper(preg_replace('/\s+/', '',
                $order->shipping_postal_code ?? $order->billing_postal_code)),
            'street' => $order->shipping_street ?? $order->billing_street,
            'number' => $order->shipping_house_number ?? $order->billing_house_number,
            'addition' => $order->shipping_house_number_addition ?? $order->billing_house_number_addition ?? '',
            'name' => trim(($order->shipping_first_name ?? $order->billing_first_name).' '.
                ($order->shipping_last_name ?? $order->billing_last_name)),
            'company' => $order->shipping_company ?? $order->billing_company,
            'email' => $order->customer->billing_email,
            'phone' => $order->shipping_phone ?? $order->billing_phone,
        ];

        Log::debug('MyParcel address debug', $address);

        try {
            $result = app(MyParcelService::class)->createShipment([
                'order_id' => $order->id,
                'reference' => 'order-'.$order->id,
                'carrier' => $order->myparcel_carrier ?? 'postnl',
                'address' => $address,
                'delivery' => $delivery,
            ]);

            $order->update([
                'myparcel_consignment_id' => $result['consignment_id'] ?? null,
                'myparcel_track_trace_url' => $result['track_trace_url'] ?? null,
                'myparcel_label_link' => $result['label_link'] ?? null,
            ]);
        } catch (\Throwable $e) {
            Log::error('MyParcel shipment create failed', [
                'order' => $order->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /* ------------------ Mollie ------------------ */

    private function createMolliePayment(Order $order, float $amount): void
    {
        $webhookUrl = match (config('app.env')) {
            'production' => env('WEBHOOK_URL_PRODUCTION'),
            'staging' => env('WEBHOOK_URL_STAGING'),
            default => env('WEBHOOK_URL_LOCAL')
        };

        try {
            $payment = $this->mollie->payments->create([
                'amount' => ['currency' => 'EUR', 'value' => number_format($amount, 2, '.', '')],
                'description' => 'Bestelling #'.$order->id,
                'redirectUrl' => route('payment.success', ['order' => $order->id]),
                'webhookUrl' => $webhookUrl,
                'metadata' => ['order_id' => $order->id],
            ]);

            $order->update([
                'mollie_payment_id' => $payment->id,
                'payment_link' => $payment->getCheckoutUrl(),
            ]);
        } catch (\Throwable $e) {
            Log::error('Mollie payment create failed', ['order' => $order->id, 'error' => $e->getMessage()]);
        }
    }


// MyParcel Package update
    public function orderUpdatePackageType(Request $request, string $id)
    {
        $order = Order::findOrFail($id);
        $this->authorize('update', $order);

        $request->validate([
            'package_type' => 'required|in:1,2,3,4'
        ]);

        $oldType = $order->myparcel_package_type_id;
        $newType = (int) $request->input('package_type');

        if ($oldType === $newType) {
            return back()->with('success', 'Pakket type is ongewijzigd.');
        }

        // 🔹 Stap 1: Oude consignment verwijderen
        if ($order->myparcel_consignment_id) {
            $deleted = app(MyParcelService::class)
                ->deleteConsignmentById((int) $order->myparcel_consignment_id);

            if ($deleted) {
                Log::info("MyParcel: oude consignment {$order->myparcel_consignment_id} verwijderd", [
                    'order_id' => $order->id,
                ]);
            }
        }

        // 🔹 Stap 2: Nieuwe consignment aanmaken
        $address = [
            'cc' => $order->shipping_country ?? $order->billing_country,
            'city' => $order->shipping_city ?? $order->billing_city,
            'postalCode' => strtoupper(preg_replace('/\s+/', '',
                $order->shipping_postal_code ?? $order->billing_postal_code)),
            'street' => $order->shipping_street ?? $order->billing_street,
            'number' => $order->shipping_house_number ?? $order->billing_house_number,
            'addition' => $order->shipping_house_number_addition ?? $order->billing_house_number_addition ?? '',
            'name' => trim(($order->shipping_first_name ?? $order->billing_first_name).' '.($order->shipping_last_name ?? $order->billing_last_name)),
            'company' => $order->shipping_company ?? $order->billing_company,
            'email' => $order->customer->billing_email,
            'phone' => $order->shipping_phone ?? $order->billing_phone,
        ];

        $delivery = [
            'packageTypeId' => $newType,
            'carrier' => $order->myparcel_carrier ?? 'postnl',
            'deliveryType' => $order->myparcel_delivery_type ?? null,
            'onlyRecipient' => $order->myparcel_only_recipient ?? false,
            'signature' => $order->myparcel_signature ?? false,
            'insurance' => $order->myparcel_insurance_amount ?? null,
        ];

        try {
            $result = app(MyParcelService::class)->createShipment([
                'address' => $address,
                'delivery' => $delivery,
                'reference' => 'order-'.$order->id,
                'order_id' => $order->id,
                'carrier' => $delivery['carrier'],
            ]);

            $order->update([
                'myparcel_package_type_id' => $newType,
                'myparcel_consignment_id' => $result['consignment_id'] ?? null,
                'myparcel_track_trace_url' => $result['track_trace_url'] ?? null,
                'myparcel_label_link' => $result['label_link'] ?? null,
            ]);

            Log::info('MyParcel: nieuwe consignment aangemaakt', [
                'order_id' => $order->id,
                'consignment_id' => $result['consignment_id'] ?? null,
                'track_trace' => $result['track_trace_url'] ?? null,
            ]);
        } catch (\Throwable $e) {
            Log::error('MyParcel: aanmaken consignment mislukt', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);

            return back()->withErrors([
                'package_type' => 'Aanmaken bij MyParcel mislukt: '.$e->getMessage()
            ]);
        }

        return back()->with('success',
            'Pakket type is bijgewerkt: oude consignment verwijderd en nieuwe aangemaakt bij MyParcel.');
    }


    public function generateLabel(string $id)
    {
        $order = Order::findOrFail($id);
        $this->authorize('update', $order);

        if (!$order->myparcel_consignment_id) {
            return back()->withErrors([
                'myparcel' => 'Geen MyParcel consignment gekoppeld aan deze bestelling.'
            ]);
        }

        try {
            $result = app(MyParcelService::class)->generateLabel((int) $order->myparcel_consignment_id);

            $order->update([
                'myparcel_label_link' => $result['label_link'] ?? null,
                'myparcel_track_trace_url' => $result['track_trace_url'] ?? null,
                'myparcel_barcode' => $result['barcode'] ?? null,
            ]);

            return back()->with('success', 'Label succesvol aangemaakt en Track & Trace bijgewerkt.');
        } catch (\Throwable $e) {
            return back()->withErrors([
                'myparcel' => 'Label genereren mislukt: '.$e->getMessage(),
            ]);
        }
    }

}
