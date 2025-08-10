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
        'total',
        'status',
        'payment_status',
        'paid_at',

        'myparcel_consignment_id',
        'myparcel_track_trace_url',
        'myparcel_label_link',

        'myparcel_package_type_id',
        'myparcel_only_recipient',
        'myparcel_signature',
        'myparcel_insurance_amount',

        // nieuw:
        'myparcel_delivery_json',
        'myparcel_is_pickup',
        'myparcel_carrier',
        'myparcel_delivery_type',
    ];
    
    public function getStatusLabelAttribute()
    {
        $labels = [
            'pending' => 'In afwachting',
            'shipped' => 'Verzonden',
            'cancelled' => 'Geannuleerd',
            'paid' => 'Betaald',
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

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
