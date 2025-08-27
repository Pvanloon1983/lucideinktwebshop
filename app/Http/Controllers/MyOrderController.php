<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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

    public function download_invoice($id)
    {
        $order = Order::findOrFail($id); // Force fresh fetch from DB

        // Security check: only the owner of the order OR admins can download
        $user = auth()->user();
        $customer = Customer::where('billing_email', $user->email)->first();

        $isAdmin = $user->role === 'admin';
        $isCustomer = $customer && $order->customer_id === $customer->id;
        if (!$isAdmin && !$isCustomer) {
            return redirect()->route('dashboard');
        }

        // Check if invoice path is set
        if (empty($order->invoice_pdf_path)) {
            abort(404, 'Factuur niet gevonden.');
        }

        $disk = Storage::disk('public');
        $path = $order->invoice_pdf_path;
        if (!$disk->exists($path)) {
            abort(404, 'Factuurbestand ontbreekt.');
        }
        return $disk->download($path, 'factuur_'.$order->id.'.pdf');
    }
}
