<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
		public function index() 
		{
				$orders = Order::with(['items', 'customer'])->paginate(10);

				return view('orders.index', ['orders' => $orders]);
		}

		public function show(String $id)
		{
				$order = Order::with(['items', 'customer'])->findOrFail($id);
				$items = $order->items()->paginate(10);
				$order->setRelation('items', $items);
        return view('orders.show', ['order' => $order]);
		}

		public function showMyOrders()
		{
			$user = auth()->user();
			$orders = Order::where('customer_id', $user->id)->with(['items', 'customer'])->paginate(10);

			return view('orders.customerOrders', ['orders' => $orders]);
		}

		public function showMyOrder(String $id)
		{
				$user = auth()->user();
				$order = Order::where('customer_id', $user->id)->with(['items', 'customer'])->findOrFail($id);
				$items = $order->items()->paginate(10);
				$order->setRelation('items', $items);
				return view('orders.show', ['order' => $order]);
		}
}
