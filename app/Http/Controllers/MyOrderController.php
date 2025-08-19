<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Customer;
use Illuminate\Http\Request;

class MyOrderController extends Controller
{

    public function __construct()
    {
        $this->middleware(['auth', 'role:admin,user']);
    }

    public function showMyOrders()
    {
           $user = auth()->user();
            $customer = Customer::where('billing_email', $user->email)->first();
            if (!$customer) {
                // Return empty paginator if no customer found
                $orders = Order::whereRaw('1=0')->paginate(10);
                return view('orders.customerOrders', ['orders' => $orders]);
            }
            $orders = Order::where('customer_id', $customer->id)->with(['items', 'customer'])->paginate(10);
            return view('orders.customerOrders', ['orders' => $orders]);
    }

    public function showMyOrder(string $id)
    {
            $user = auth()->user();
            $customer = Customer::where('billing_email', $user->email)->first();
            if (!$customer) {
                // Return empty paginator if no customer found
                $orders = Order::whereRaw('1=0')->paginate(10);
                return view('orders.customerOrders', ['orders' => $orders]);
            }
            $order = Order::where('customer_id', $customer->id)->with(['items', 'customer'])->findOrFail($id);
            $items = $order->items()->paginate(10);
            $order->setRelation('items', $items);
            return view('orders.customerOrderShow', ['order' => $order]);
    }
}
