<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use App\Models\Customer;
use Illuminate\Http\Request;
use Mollie\Api\MollieApiClient;
use App\Services\MyParcelService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;

class CheckoutController extends Controller
{
    protected MollieApiClient $mollie;

    public function __construct(MollieApiClient $mollie)
    {
        $this->mollie = $mollie;
        $this->mollie->setApiKey(config('mollie.key'));
    }

    public function create()
    {
        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return redirect('/winkel')->with('error', 'Je winkelwagen is leeg.');
        }
        return view('checkout.index', ['cart' => $cart]);
    }

    public function store(Request $request)
    {
        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return redirect('/winkel')->with('error', 'Je winkelwagen is leeg.');
        }

    // Normaliseer checkbox: zet naar 1 wanneer aangevinkt, anders 0 (voorkomt 'on'/'off')
    $request->merge(['alt-shipping' => $request->has('alt-shipping') ? 1 : 0]);

        // --- Validation
    $rules = [
            // Billing
            'billing_email'            => 'required|email',
            'billing_first_name'       => 'required|string',
            'billing_last_name'        => 'required|string',
            'billing_street'           => 'required|string',
            'billing_housenumber'      => 'required|numeric',
            'billing_housenumber-add'  => 'nullable|string',
            'billing_postal-zip-code'  => 'required|string',
            'billing_city'             => 'required|string',
            'billing_country'          => 'required|string',
            'billing_phone'            => 'nullable|string',
            'billing_company'          => 'nullable|string',

            // Shipping (alleen wanneer alt-shipping actief is)
            'alt-shipping'             => 'nullable|boolean',
            'shipping_first_name'      => 'required_if:alt-shipping,1|string|nullable',
            'shipping_last_name'       => 'required_if:alt-shipping,1|string|nullable',
            'shipping_street'          => 'required_if:alt-shipping,1|string|nullable',
            'shipping_housenumber'     => 'required_if:alt-shipping,1|numeric|nullable',
            'shipping_housenumber-add' => 'nullable|string',
            'shipping_postal-zip-code' => 'required_if:alt-shipping,1|string|nullable',
            'shipping_city'            => 'required_if:alt-shipping,1|string|nullable',
            'shipping_country'         => 'required_if:alt-shipping,1|string|nullable',
            'shipping_phone'           => 'nullable|string',
            'shipping_company'         => 'nullable|string',

            // Account
            'password'                 => 'nullable|string|min:8|confirmed',
        ];

        $messages = [
            'billing_email.required'            => 'E-mailadres is verplicht.',
            'billing_email.email'               => 'Vul een geldig e-mailadres in.',
            'billing_first_name.required'       => 'Voornaam is verplicht.',
            'billing_last_name.required'        => 'Achternaam is verplicht.',
            'billing_street.required'           => 'Straatnaam is verplicht.',
            'billing_housenumber.required'      => 'Huisnummer is verplicht.',
            'billing_housenumber.numeric'       => 'Huisnummer moet een getal zijn.',
            'billing_postal-zip-code.required'  => 'Postcode is verplicht.',
            'billing_city.required'             => 'Plaats is verplicht.',
            'billing_country.required'          => 'Land is verplicht.',

            'shipping_first_name.required_with'      => 'Voornaam is verplicht.',
            'shipping_last_name.required_with'       => 'Achternaam is verplicht.',
            'shipping_street.required_with'          => 'Straatnaam is verplicht.',
            'shipping_housenumber.required_with'     => 'Huisnummer is verplicht.',
            'shipping_postal-zip-code.required_with' => 'Postcode is verplicht.',
            'shipping_city.required_with'            => 'Plaats is verplicht.',
            'shipping_country.required_with'         => 'Land is verplicht.',

            'password.min'       => 'Wachtwoord moet minimaal 8 tekens bevatten.',
            'password.confirmed' => 'Wachtwoorden komen niet overeen.',
        ];

        $request->validate($rules, $messages);

        $customerData = [
            'billing_first_name'             => $request->input('billing_first_name'),
            'billing_last_name'              => $request->input('billing_last_name'),
            'billing_email'                  => $request->input('billing_email'),
            'billing_company'                => $request->input('billing_company'),
            'billing_street'                 => $request->input('billing_street'),
            'billing_house_number'           => $request->input('billing_housenumber'),
            'billing_house_number_addition'  => $request->input('billing_housenumber-add'),
            'billing_postal_code'            => $request->input('billing_postal-zip-code'),
            'billing_city'                   => $request->input('billing_city'),
            'billing_country'                => $request->input('billing_country'),
            'billing_phone'                  => $request->input('billing_phone'),
        ];

        if ($request->boolean('alt-shipping')) {
            $customerData = array_merge($customerData, [
            'shipping_first_name'            => $request->input('shipping_first_name'),
            'shipping_last_name'             => $request->input('shipping_last_name'),
            'shipping_company'               => $request->input('shipping_company'),
            'shipping_street'                => $request->input('shipping_street'),
            'shipping_house_number'          => $request->input('shipping_housenumber'),
            'shipping_house_number_addition' => $request->input('shipping_housenumber-add'),
            'shipping_postal_code'           => $request->input('shipping_postal-zip-code'),
            'shipping_city'                  => $request->input('shipping_city'),
            'shipping_country'               => $request->input('shipping_country'),
            'shipping_phone'                 => $request->input('shipping_phone'),
            ]);
        }

        // --- Create user (optional)
        $user = User::where('email', $request->input('billing_email'))->first();
        if (!$user && $request->filled('password')) {
        $user = \App\Models\User::create([
            'first_name' => $request->input('billing_first_name'),
            'last_name'  => $request->input('billing_last_name'),
            'email'      => $request->input('billing_email'),
            'password'   => Hash::make($request->input('password')),
        ]);
        event(new Registered($user));
        }

        $customer = Customer::updateOrCreate(
            ['billing_email' => $request->input('billing_email')],
            $customerData
        );

        // --- Calculating total price
        $total = collect($cart)->sum(fn($item) => $item['price'] * $item['quantity']);

        // --- Order + items
        $order = $customer->orders()->create([
            'total'  => $total,
            'status' => 'pending',
        ]);



        $insufficientStock = [];
        foreach ($cart as $item) {
            $product = Product::find($item['product_id']);
            if ($product && $product->stock < $item['quantity']) {
            $insufficientStock[] = "{$product->title}<br>(op voorraad: {$product->stock})";
            }
        }

        if (!empty($insufficientStock)) {
            return back()->withInput()->withErrors([
                    'stock' => 'Niet voldoende voorraad:<br>' . implode('<br>', $insufficientStock)
            ]);
        }
        
        foreach ($cart as $item) {
            // Als voldoende voorraad, verlaag de voorraad
            if ($product && $product->stock >= $item['quantity']) {
                $product->stock -= $item['quantity'];
                $product->save();
            }

            $order->items()->create([
                'product_id'  => $item['product_id'],
                'product_name'=> $item['name'],
                'quantity'    => $item['quantity'],
                'unit_price'  => $item['price'],
                'subtotal'    => $item['price'] * $item['quantity'],
            ]);

            
            $order->items()->create([
                'product_id'  => $item['product_id'],
                'product_name'=> $item['name'],
                'quantity'    => $item['quantity'],
                'unit_price'  => $item['price'],
                'subtotal'    => $item['price'] * $item['quantity'],
            ]);
        }

    // --- MyParcel widget-output opslaan + server-side validatie voor pickup
        $deliveryJson = $request->input('myparcel_delivery_options');
        $delivery     = json_decode($deliveryJson ?? '[]', true) ?: [];

        // Bepaal of het om een pickup gaat
        $isPickup = (bool)($delivery['isPickup'] ?? false)
            || (strtolower((string)($delivery['deliveryType'] ?? '')) === 'pickup');

        if ($isPickup) {
            // Ondersteun zowel `pickup` als `pickupLocation` (afhankelijk van widgetversie)
            $p = $delivery['pickup'] ?? $delivery['pickupLocation'] ?? null;

            $missing = [];
            if (!is_array($p)) {
                $missing[] = 'afhaalpunt';
            } else {
                foreach (['street','number','postalCode','city'] as $key) {
                    if (empty($p[$key])) $missing[] = $key;
                }
            }

            if ($missing) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'myparcel_delivery_options' =>
                            'Kies eerst een volledig afhaalpunt (straat, huisnummer, postcode en plaats).'
                    ]);
            }
        }

    // Hierna mag je het opslaan zoals je al deed:
        $order->update([
            'myparcel_delivery_json'    => $deliveryJson,
            'myparcel_is_pickup'        => $isPickup,
            'myparcel_carrier'          => data_get($delivery, 'carrier'),
            'myparcel_delivery_type'    => data_get($delivery, 'deliveryType'),
            'myparcel_package_type_id'  => $this->mapPackageTypeId(data_get($delivery, 'packageType')),
            'myparcel_only_recipient'   => (bool) data_get($delivery, 'shipmentOptions.onlyRecipient'),
            'myparcel_signature'        => (bool) data_get($delivery, 'shipmentOptions.signature'),
            'myparcel_insurance_amount' => data_get($delivery, 'shipmentOptions.insurance'),
        ]);

        // --- Maak zending direct aan bij MyParcel (voor betaling), net als admin orders
        try {
            $useShipping = $request->boolean('alt-shipping')
                && filled($request->input('shipping_street'))
                && filled($request->input('shipping_housenumber'));

            $fullStreet = static function ($street, $nr, $add) {
                $street = trim((string) $street);
                $nr     = trim((string) $nr);
                $add    = trim((string) $add);
                if ($street === '' || $nr === '') return '';
                return trim($street.' '.$nr.($add ? ' '.$add : ''));
            };

            $address = [
                'cc'         => $useShipping ? $request->input('shipping_country') : $request->input('billing_country'),
                'name'       => $useShipping
                    ? trim($request->input('shipping_first_name').' '.$request->input('shipping_last_name'))
                    : trim($request->input('billing_first_name').' '.$request->input('billing_last_name')),
                'company'    => $useShipping ? $request->input('shipping_company') : $request->input('billing_company'),
                'email'      => $request->input('billing_email'),
                'phone'      => $useShipping ? $request->input('shipping_phone') : $request->input('billing_phone'),
                'fullStreet' => $useShipping
                    ? $fullStreet($request->input('shipping_street'), $request->input('shipping_housenumber'), $request->input('shipping_housenumber-add'))
                    : $fullStreet($request->input('billing_street'),  $request->input('billing_housenumber'),  $request->input('billing_housenumber-add')),
                'postalCode' => preg_replace('/\s+/', '', $useShipping ? $request->input('shipping_postal-zip-code') : $request->input('billing_postal-zip-code')),
                'city'       => $useShipping ? $request->input('shipping_city') : $request->input('billing_city'),
            ];

            $shippingCfg = [
                'order_id'  => $order->id,
                'reference' => 'order-'.$order->id,
                'carrier'   => $order->myparcel_carrier ?: 'postnl',
                'address'   => $address,
                'delivery'  => [
                    'packageTypeId' => $order->myparcel_package_type_id ?: 1,
                    'onlyRecipient' => (bool) $order->myparcel_only_recipient,
                    'signature'     => (bool) $order->myparcel_signature,
                    'insurance'     => $order->myparcel_insurance_amount,
                    'deliveryType'  => $delivery['deliveryType'] ?? 'standard',
                    'is_pickup'     => (bool) ($delivery['isPickup'] ?? false),
                    'pickup'        => $delivery['pickup'] ?? null,
                ],
            ];

            \Log::info('Checkout MyParcel shipment: creating concept', [
                'order' => $order->id,
                'useShipping' => $useShipping,
                'addr_cc' => $address['cc'],
                'addr_name' => $address['name'] ?? null,
                'addr_fullStreet' => $address['fullStreet'],
                'addr_postal' => $address['postalCode'],
                'addr_city' => $address['city'],
                'deliveryType' => $shippingCfg['delivery']['deliveryType'],
                'is_pickup' => $shippingCfg['delivery']['is_pickup'],
                'pickup_cc' => data_get($shippingCfg, 'delivery.pickup.cc'),
                'retail_network_id' => data_get($shippingCfg, 'delivery.pickup.retail_network_id'),
                'location_code' => data_get($shippingCfg, 'delivery.pickup.location_code'),
            ]);

            $result = app(\App\Services\MyParcelService::class)->createShipment($shippingCfg);

            $order->update([
                'myparcel_consignment_id'  => $result['consignment_id'] ?? null,
                'myparcel_track_trace_url' => $result['track_trace_url'] ?? null,
                'myparcel_label_link'      => $result['label_link'] ?? null,
            ]);
        } catch (\Throwable $e) {
            \Log::error('Checkout MyParcel shipment create failed', [
                'order' => $order->id,
                'error' => $e->getMessage(),
            ]);
        }

        // --- Mollie
        $webhookUrl = match (config('app.env')) {
            'production' => env('WEBHOOK_URL_PRODUCTION'),
            'staging'    => env('WEBHOOK_URL_STAGING'),
            default      => env('WEBHOOK_URL_LOCAL')
        };

        $payment = $this->mollie->payments->create([
            'amount' => [
                'currency' => 'EUR',
                'value'    => number_format($total, 2, '.', ''),
            ],
            'description' => 'Bestelling #'.$order->id,
            'redirectUrl' => route('payment.success', ['order' => $order->id]),
            'webhookUrl'  => $webhookUrl,
            'metadata'    => ['order_id' => $order->id],
        ]);

        $order->update(['mollie_payment_id' => $payment->id]);

        return redirect($payment->getCheckoutUrl(), 303);
    }

    public function paymentSuccess(Request $request)
    {
        $orderId = $request->query('order');
        if (!$orderId || !($order = Order::find($orderId))) {
            return view('checkout.success', ['error' => 'Order niet gevonden.', 'success' => null, 'info' => null]);
        }

        if (!$order->mollie_payment_id) {
            return view('checkout.success', ['error' => 'Geen betaling gevonden bij deze order.', 'success' => null, 'info' => null]);
        }

        $payment = $this->mollie->payments->get($order->mollie_payment_id);

        if ($payment->isPaid()) {
            $order->update([
                'status'         => 'paid',
                'payment_status' => 'paid',
                'paid_at'        => now(),
            ]);

            // Adres kiezen (shipping > billing)
            $customer    = $order->customer;
            $useShipping = filled($customer->shipping_street) && filled($customer->shipping_house_number);

            $fullStreet = static function ($street, $nr, $add) {
                $street = trim((string) $street);
                $nr     = trim((string) $nr);
                $add    = trim((string) $add);
                if ($street === '' || $nr === '') return '';
                return trim($street.' '.$nr.($add ? ' '.$add : ''));
            };

            $address = [
                'cc'         => $useShipping ? $customer->shipping_country : $customer->billing_country,
                'name'       => $useShipping
                    ? trim($customer->shipping_first_name.' '.$customer->shipping_last_name)
                    : trim($customer->billing_first_name.' '.$customer->billing_last_name),
                'company'    => $useShipping ? $customer->shipping_company : $customer->billing_company,
                'email'      => $customer->billing_email,
                'phone'      => $useShipping ? $customer->shipping_phone : $customer->billing_phone,
                'fullStreet' => $useShipping
                    ? $fullStreet($customer->shipping_street, $customer->shipping_house_number, $customer->shipping_house_number_addition)
                    : $fullStreet($customer->billing_street,  $customer->billing_house_number,  $customer->billing_house_number_addition),
                'postalCode' => preg_replace('/\s+/', '', $useShipping ? $customer->shipping_postal_code : $customer->billing_postal_code),
                'city'       => $useShipping ? $customer->shipping_city : $customer->billing_city,
            ];

            // Extra server-side safety: pickup moet een volledig afhaalpunt hebben
            $selection = json_decode($order->myparcel_delivery_json ?? '[]', true) ?: [];
            $isPickup  = (bool)($selection['isPickup'] ?? false)
                    || (strtolower((string)($selection['deliveryType'] ?? '')) === 'pickup');

            if ($isPickup) {
                $p = $selection['pickup'] ?? $selection['pickupLocation'] ?? null;
                $invalid = !is_array($p)
                    || empty($p['street'])
                    || empty($p['number'])
                    || empty($p['postalCode'])
                    || empty($p['city']);

                if ($invalid) {
                    // Laat de order op 'paid' staan, maar laat de zending (nog) niet aanmaken
                    // en toon een duidelijke melding zodat je of de klant het kan herstellen.
                    return view('checkout.success', [
                        'success' => null,
                        'info'    => null,
                        'error'   => 'Je koos voor ophalen, maar het afhaalpunt is niet compleet. Neem contact met ons op of kies opnieuw een afhaalpunt.',
                    ]);
                }
            }

            $shipping = [
            'order_id'  => $order->id,
            'reference' => 'order-'.$order->id,
            'carrier'   => $order->myparcel_carrier ?: 'postnl', // <— doorgeven
            'address'   => $address,
            'delivery'  => [
                'packageTypeId' => $order->myparcel_package_type_id ?: 1, //  1 is package
                'onlyRecipient' => (bool) $order->myparcel_only_recipient,
                'signature'     => (bool) $order->myparcel_signature,
                'insurance'     => $order->myparcel_insurance_amount,
                'deliveryType'  => $selection['deliveryType'] ?? 'standard',
                'is_pickup'     => (bool) ($selection['isPickup'] ?? false),
                'pickup'        => $selection['pickup'] ?? null,
            ],
            ];

            // >>> MyParcel zending aanmaken (zorg dat je service pickup ondersteunt – zie snippet hieronder)
            $result = app(MyParcelService::class)->createShipment($shipping);

            $order->update([
                'myparcel_consignment_id'  => $result['consignment_id'] ?? null,
                'myparcel_track_trace_url' => $result['track_trace_url'] ?? null,
                'myparcel_label_link'      => $result['label_link'] ?? null,
            ]);

            session()->forget('cart');
            return view('checkout.success', ['success' => 'Je betaling is geslaagd!', 'error' => null, 'info' => null]);
        }

        if ($payment->isOpen() || $payment->isPending()) {
            return view('checkout.success', ['info' => 'Je betaling is nog niet afgerond.', 'success' => null, 'error' => null]);
        }

        $order->update(['status' => 'cancelled', 'payment_status' => 'failed']);
        return view('checkout.success', ['error' => 'Je betaling is mislukt of geannuleerd.', 'success' => null, 'info' => null]);
    }

    public function paymentWebhook(Request $request)
    {
        $paymentId = $request->input('id');
        if (!$paymentId) return response()->json(['error' => 'No payment id'], 400);

        $payment = $this->mollie->payments->get($paymentId);
        $orderId = $payment->metadata->order_id ?? null;
        if ($orderId && ($order = Order::find($orderId))) {
            if     ($payment->isPaid())                           $order->update(['status' => 'paid',     'payment_status' => 'paid',     'paid_at' => now()]);
            elseif ($payment->isOpen() || $payment->isPending())  $order->update(['status' => 'pending',  'payment_status' => 'pending']);
            elseif ($payment->isFailed() || $payment->isExpired() || $payment->isCanceled())
                                                                  $order->update(['status' => 'cancelled','payment_status' => 'failed']);
            elseif ($payment->isRefunded())                       $order->update(['status' => 'cancelled','payment_status' => 'refunded']);
        }
        return response()->json(['status' => 'ok']);
    }

    private function mapPackageTypeId(?string $name): int
    {
        return [
            'package'       => 1,
            'mailbox'       => 2,
            'letter'        => 3,
            'digital_stamp' => 4,
            'package_small' => 1,
        ][$name] ?? 1;
    }
}
