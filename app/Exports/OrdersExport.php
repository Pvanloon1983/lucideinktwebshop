<?php

namespace App\Exports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class OrdersExport implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $orders = Order::with(['items', 'customer'])
            ->orderBy('created_at', 'desc')
            ->get();

        $rows = collect();
        foreach ($orders as $order) {
            foreach ($order->items as $item) {
                $rows->push([
                    'order_id' => $order->id,
                    'customer_id' => $order->customer_id,
                    'customer_name' => optional($order->customer)->billing_first_name . ' ' . optional($order->customer)->billing_last_name,
                    'customer_email' => optional($order->customer)->billing_email,
                    'status' => $order->status,
                    'total' => $order->total,
                    'total_after_discount' => $order->total_after_discount,
                    'discount_code' => $order->discount_code_checkout,
                    'discount_type' => $order->discount_type,
                    'discount_value' => $order->discount_value,
                    'discount_price_total' => $order->discount_price_total,
                    'paid_at' => $order->paid_at,
                    'created_at' => $order->created_at,
                    'product_id' => $item->product_id,
                    'product_name' => $item->product_name,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'subtotal' => $item->subtotal,
                ]);
            }
        }
        return $rows;
    }

    public function headings(): array
    {
        return [
            'Order ID',
            'Customer ID',
            'Customer Name',
            'Customer Email',
            'Order Status',
            'Order Total',
            'Order Total After Discount',
            'Discount Code',
            'Discount Type',
            'Discount Value',
            'Discount Price Total',
            'Paid At',
            'Order Created At',
            'Product ID',
            'Product Name',
            'Quantity',
            'Unit Price',
            'Subtotal',
        ];
    }
}
