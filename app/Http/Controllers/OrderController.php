<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\Customer;
use Illuminate\Http\Request;
use Mollie\Api\MollieApiClient;

class OrderController extends Controller
{

	protected MollieApiClient $mollie;

	public function __construct(MollieApiClient $mollie)
	{
		$this->middleware(['auth', 'role:admin']);
		$this->mollie = $mollie;
		$this->mollie->setApiKey(config('mollie.key'));
	}

		public function index() 
		{
				$orders = Order::with(['items', 'customer'])->paginate(10);

				return view('orders.index', ['orders' => $orders]);
		}

		public function show(string $id)
		{
				$order = Order::with(['items', 'customer'])->findOrFail($id);
				$items = $order->items()->paginate(10);
				$order->setRelation('items', $items);
        return view('orders.show', ['order' => $order]);
		}

		public function create()
		{
			$products = Product::with('category')->orderBy('title', 'asc')->get();

			return view('orders.create', ['products' => $products]);
		}

		public function store(Request $request)
		{
			// Validatie (geïnspireerd op CheckoutController)
			$data = $request->validate([
				// Productregels
				'items'                 => 'required|array',
				'items.*.qty'           => 'nullable|integer|min:0',

				// Korting
				'discount_value'        => 'nullable|numeric|min:0',
				'discount_type'         => 'nullable|in:amount,percent',

				// Factuurgegevens
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

				// Verzendadres (alleen indien aangevinkt)
				'alt-shipping'             => 'nullable|boolean',
				'shipping_first_name'      => 'required_with:alt-shipping|string|nullable',
				'shipping_last_name'       => 'required_with:alt-shipping|string|nullable',
				'shipping_street'          => 'required_with:alt-shipping|string|nullable',
				'shipping_housenumber'     => 'required_with:alt-shipping|numeric|nullable',
				'shipping_housenumber-add' => 'nullable|string',
				'shipping_postal-zip-code' => 'required_with:alt-shipping|string|nullable',
				'shipping_city'            => 'required_with:alt-shipping|string|nullable',
				'shipping_country'         => 'required_with:alt-shipping|string|nullable',
				'shipping_phone'           => 'nullable|string',
				'shipping_company'         => 'nullable|string',

				// MyParcel widget output (JSON string)
				'myparcel_delivery_options' => 'nullable|string',
			]);

			$rawItems = $data['items'] ?? [];
			$productIds = array_keys($rawItems);

			// Haal alle betrokken producten op
			$products = Product::whereIn('id', $productIds)->get()->keyBy('id');

			$lines = [];
			$totalBeforeDiscount = 0;

			foreach ($rawItems as $productId => $itemData) {
					$qty = (int)($itemData['qty'] ?? 0);
					if ($qty < 1) {
							continue;
					}
					if (!isset($products[$productId])) {
							continue;
					}
					$product = $products[$productId];
					$unitPrice = (float)$product->price;
					$lineSubtotal = $unitPrice * $qty;
					$totalBeforeDiscount += $lineSubtotal;

					$lines[] = [
							'product_id' => $product->id,
							'title'      => $product->title,
							'qty'        => $qty,
							'unit_price' => $unitPrice,
							'subtotal'   => $lineSubtotal,
					];
			}

			if (empty($lines)) {
					return back()
							->withErrors(['items' => 'Vul bij minstens één product een hoeveelheid van meer dan 1 in.'])
							->withInput();
			}

			// Korting berekenen
			$discountValue = (float)($data['discount_value'] ?? 0);
			$discountType  = $data['discount_type'] ?? null;
			$discountAmount = 0;

			if ($discountValue > 0 && $discountType) {
					if ($discountType === 'percent') {
							$discountAmount = $totalBeforeDiscount * ($discountValue / 100);
					} else { // amount
							$discountAmount = $discountValue;
					}
			}

			// Niet onder nul
			if ($discountAmount > $totalBeforeDiscount) {
					$discountAmount = $totalBeforeDiscount;
			}

			$totalAfterDiscount = $totalBeforeDiscount - $discountAmount;

			// Customer upsert (zoals CheckoutController)
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

			$customer = Customer::updateOrCreate(
				['billing_email' => $request->input('billing_email')],
				$customerData
			);

			// Order opslaan
			$order = $customer->orders()->create([
				'status'               => 'pending',
				'payment_status'       => 'pending',
				'total'                => $totalAfterDiscount,
				'discount_type'        => $discountType,
				'discount_value'       => $discountValue,
				'discount_price_total' => $discountAmount,
			]);

			// Order items
			foreach ($lines as $line) {
				$order->items()->create([
					'product_id'   => $line['product_id'],
					'product_name' => $line['title'],
					'quantity'     => $line['qty'],
					'unit_price'   => $line['unit_price'],
					'subtotal'     => $line['subtotal'],
				]);
			}

			// MyParcel verwerking (validatie + opslaan)
			$deliveryJson = $request->input('myparcel_delivery_options');
			$delivery     = json_decode($deliveryJson ?? '[]', true) ?: [];
			$isPickup = (bool)($delivery['isPickup'] ?? false) || (strtolower((string)($delivery['deliveryType'] ?? '')) === 'pickup');

			if ($isPickup) {
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
					return back()->withInput()->withErrors([
						'myparcel_delivery_options' => 'Kies eerst een volledig afhaalpunt (straat, huisnummer, postcode en plaats).'
					]);
				}
			}

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

			// Maak een Mollie-betaallink (betaling nog niet voltooid)
			$webhookUrl = match (config('app.env')) {
				'production' => env('WEBHOOK_URL_PRODUCTION'),
				'staging'    => env('WEBHOOK_URL_STAGING'),
				default      => env('WEBHOOK_URL_LOCAL')
			};

			try {
				$payment = $this->mollie->payments->create([
					'amount' => [
						'currency' => 'EUR',
						'value'    => number_format($totalAfterDiscount, 2, '.', ''),
					],
					'description' => 'Bestelling #'.$order->id,
					'redirectUrl' => route('payment.success', ['order' => $order->id]),
					'webhookUrl'  => $webhookUrl,
					'metadata'    => ['order_id' => $order->id],
				]);

				$order->update([
					'mollie_payment_id' => $payment->id,
					'payment_link'      => $payment->getCheckoutUrl(),
				]);
			} catch (\Throwable $e) {
				// Laat order staan maar zonder link
			}

			return back()
				->with('success', 'Bestelling is geplaatst.')
				->with('payment_link', $order->payment_link)
				->with('chosen_items', $lines)
				->with('total_before_discount', $totalBeforeDiscount)
				->with('discount_amount', $discountAmount)
				->with('discount_value', $discountValue)
				->with('discount_type', $discountType)
				->with('total_after_discount', $totalAfterDiscount);

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
