<?php

namespace App\Models;

use App\Models\Order;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    use SoftDeletes;

    protected $fillable = [
        // Billing fields
        'billing_first_name',
        'billing_last_name',
        'billing_email',
        'billing_company',
        'billing_street',
        'billing_house_number',
        'billing_house_number_addition',
        'billing_postal_code',
        'billing_city',
        'billing_country',
        'billing_phone',

        // Shipping fields (optional)
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
    ];

    /**
     * Get the orders for the customer.
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
