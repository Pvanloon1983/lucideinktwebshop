<?php

namespace App\Http\Controllers;

use App\Models\Order;
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
			return view('orders.create');
		}
}
