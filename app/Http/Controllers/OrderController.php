<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\Customer;
use Illuminate\Http\Request;

class OrderController extends Controller
{

    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
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

			// Validatie
			$data = $request->validate([
					'items'                 => 'required|array',
					'items.*.qty'           => 'nullable|integer|min:0',
					'discount_value'        => 'nullable|numeric|min:0',
					'discount_type'         => 'nullable|in:amount,percent',
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
							->withErrors(['items' => 'Vul bij minstens één product een hoeveelheid meer dan 1 in.'])
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

			// (Optioneel) hier order + order_items opslaan
			/*
			$order = Order::create([
					'customer_id'           => optional(Customer::where('billing_email', $request->billing_email)->first())->id,
					'total_before_discount' => $totalBeforeDiscount,
					'discount_amount'       => $discountAmount,
					'total_after_discount'  => $totalAfterDiscount,
					// ... overige velden ...
			]);

			foreach ($lines as $line) {
					$order->items()->create([
							'product_id' => $line['product_id'],
							'qty'        => $line['qty'],
							'price'      => $line['unit_price'],
							'subtotal'   => $line['subtotal'],
					]);
			}
			*/

			return back()
					->with('success', 'Bestelling berekend.')
					->with('chosen_items', $lines)
					->with('total_before_discount', $totalBeforeDiscount)
					->with('discount_amount', $discountAmount)
					->with('discount_value', $discountValue)
					->with('discount_type', $discountType)
					->with('total_after_discount', $totalAfterDiscount);

		}
}
