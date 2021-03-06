<?php

namespace Armezit\GetCandy\PurchaseLimit\Models;

use Armezit\GetCandy\PurchaseLimit\Database\Factories\PurchaseLimitFactory;
use GetCandy\Models\Customer;
use GetCandy\Models\CustomerGroup;
use GetCandy\Models\Product;
use GetCandy\Models\ProductVariant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseLimit extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'product_id',
        'product_variant_id',
        'customer_group_id',
        'customer_id',
        'period',
        'max_quantity',
        'max_total',
        'starts_at',
        'ends_at',
    ];

    /**
     * Return a new factory instance for the model.
     *
     * @return PurchaseLimitFactory
     */
    protected static function newFactory(): PurchaseLimitFactory
    {
        return PurchaseLimitFactory::new();
    }

    /**
     * Get the product that owns the item.
     */
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    /**
     * Get the product variant that owns the item.
     */
    public function productVariant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    /**
     * Get the customer that owns the item.
     */
    public function customerGroup()
    {
        return $this->belongsTo(CustomerGroup::class, 'customer_group_id');
    }

    /**
     * Get the customer that owns the item.
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

}
