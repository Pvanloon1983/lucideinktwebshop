<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DiscountCode extends Model
{
  use SoftDeletes;

  protected $fillable = [
    'code',
    'description',
    'discount_type',
    'discount',
    'expiration_date',
    'usage_limit',
    'usage_limit_per_customer',
    'is_published'
  ];

}
