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
            abort(403, 'Je hebt geen toegang tot deze factuur.');
        }

        // Check if invoice path is set
        if (empty($order->invoice_pdf_path)) {
            abort(404, 'Factuur niet gevonden.');
        }

        return $this->streamInvoiceDownload($order);
    }

    /**
     * Zelfde streaming aanpak als admin controller om host issues te vermijden.
     */
    private function streamInvoiceDownload(Order $order)
    {
        $relativePath = trim($order->invoice_pdf_path);
        if (str_contains($relativePath, '..')) {
            abort(400, 'Ongeldig pad.');
        }

        $disk = Storage::disk('public');
        if (!$disk->exists($relativePath)) {
            abort(404, 'Factuurbestand ontbreekt.');
        }

        $fullPath = method_exists($disk, 'path') ? $disk->path($relativePath) : storage_path('app/public/'.$relativePath);
        if (!is_file($fullPath) || !is_readable($fullPath)) {
            abort(500, 'Factuur kan niet worden gelezen.');
        }

        while (ob_get_level() > 0) { @ob_end_clean(); }

        $fileSize = @filesize($fullPath) ?: null;
        $downloadName = 'factuur_'.$order->id.'.pdf';
        $headers = [
            'Content-Type' => 'application/pdf',
            'Cache-Control' => 'private, no-store, max-age=0, must-revalidate',
            'Pragma' => 'public',
        ];
        if ($fileSize) { $headers['Content-Length'] = (string) $fileSize; }

        return response()->streamDownload(function () use ($fullPath) {
            $h = fopen($fullPath, 'rb');
            if ($h === false) { return; }
            try {
                while (!feof($h)) {
                    echo fread($h, 8192);
                    flush();
                }
            } finally { fclose($h); }
        }, $downloadName, $headers);
    }
}
