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
    ];

    public function getStatusLabelAttribute()
    {
        $labels = [
            'pending' => 'In afwachting',
            'shipped' => 'Verzonden',
            'cancelled' => 'Geannuleerd',
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
