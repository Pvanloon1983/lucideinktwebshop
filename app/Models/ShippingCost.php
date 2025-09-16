<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ShippingCost extends Model
{
  use HasFactory, SoftDeletes;

  protected $fillable = [
    'amount',
    'country',
    'is_published',
    'created_by',
    'updated_by',
    'deleted_by'
  ];

  public function creator(): BelongsTo
  {
    return $this->belongsTo(User::class, 'created_by');
  }
  public function updater(): BelongsTo
  {
    return $this->belongsTo(User::class, 'updated_by');
  }

  public function orders(): HasMany
  {
    return $this->hasMany(Order::class, 'shipping_cost_id');
  }

}
