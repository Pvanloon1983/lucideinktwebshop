<?php

namespace App\Models;

use App\Models\Customer;
use App\Models\OrderItem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'customer_id',
        'mollie_payment_id',
        'payment_link',
        'total',
        'status',
        'payment_status',
        'paid_at',
        'invoice_pdf_path',
        'shipping_cost_id',
        'shipping_cost_amount',
        'total_before',

        // Shipping fields
        'shipping_first_name',
        'shipping_last_name',
        'shipping_company',
        'shipping_street',
        'shipping_house_number',
        'shipping_house_number_addition',
        'shipping_postal_code',
        'shipping_city',
        'shipping_country',
        'shipping_phone',

        'myparcel_consignment_id',
        'myparcel_track_trace_url',
        'myparcel_label_link',
        'myparcel_barcode',

        'myparcel_package_type_id',
        'myparcel_only_recipient',
        'myparcel_signature',
        'myparcel_insurance_amount',

        'myparcel_delivery_json',
        'myparcel_is_pickup',
        'myparcel_carrier',
        'myparcel_delivery_type',

        // discounts
        'total_after_discount',
        'discount_type',
        'discount_value',
        'discount_price_total',
        'discount_code_checkout'
        ];
    
    public function getStatusLabelAttribute()
    {
        $labels = [
            'pending' => 'In afwachting',
            'shipped' => 'Verzonden',
            'cancelled' => 'Geannuleerd',
            'completed' => 'Afgerond',
            // Add more as needed
        ];
        return $labels[$this->status] ?? ucfirst($this->status);
    }

    public function getPaymentStatusLabelAttribute()
    {
        $labels = [
            'pending' => 'In afwachting',
            'paid' => 'Betaald',
            'failed' => 'Mislukt',
            'refunded' => 'Terugbetaald',
            // Add more as needed
        ];
        return $labels[$this->payment_status] ?? ucfirst($this->payment_status);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function shipping_cost(): BelongsTo
    {
      return $this->belongsTo(ShippingCost::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
