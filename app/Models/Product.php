<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'price',
        'stock',
        'product_copy_id',
        'is_published',
        'short_description',
        'long_description',
        'category_id',
        'base_title',
        'base_slug',
        'weight',
        'height',
        'width',
        'depth',
        'image_1',
        'image_2',
        'image_3',
        'image_4',
        'created_by',
        'updated_by',
        'deleted_by'
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
    
    public function category()
    {
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }

    public function productCopy()
    {
        return $this->belongsTo(ProductCopy::class, 'product_copy_id');
    }

}
