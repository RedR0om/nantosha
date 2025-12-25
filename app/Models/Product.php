<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'short_description',
        'brand_id',
        'category_id',
        'price',
        'sale_price',
        'price_per_capsule',
        'price_per_mg',
        'is_bottle_based',
        'capsules_per_bottle',
        'bottle_pricing_tiers',
        'bottles_only',
        'sku',
        'stock_quantity',
        'in_stock',
        'image',
        'images',
        'variants',
        'is_featured',
        'is_best_seller',
        'is_new_arrival',
        'is_customer_favorite',
        'sort_order',
        'supplement_facts',
        'ingredients',
        'directions',
        'warnings',
        'country_of_origin',
        'manufacturer',
        'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'price_per_capsule' => 'decimal:2',
        'price_per_mg' => 'decimal:4',
        'is_bottle_based' => 'boolean',
        'bottle_pricing_tiers' => 'array',
        'bottles_only' => 'boolean',
        'images' => 'array',
        'variants' => 'array',
        'in_stock' => 'boolean',
        'is_featured' => 'boolean',
        'is_best_seller' => 'boolean',
        'is_new_arrival' => 'boolean',
        'is_customer_favorite' => 'boolean',
        'is_active' => 'boolean',
    ];

    // Tax-inclusive prices are calculated on-demand, not automatically appended
    // This keeps JSON responses clean and allows us to use base prices on product pages

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function getCurrentPriceAttribute(): float
    {
        return $this->sale_price ?? $this->price;
    }

    public function getIsOnSaleAttribute(): bool
    {
        return $this->sale_price !== null && $this->sale_price < $this->price;
    }

    /**
     * Calculate tax-inclusive price based on dynamic settings
     */
    public function getTaxInclusivePriceAttribute(): float
    {
        return \App\Services\PriceCalculationService::calculateTaxInclusivePrice($this->price);
    }

    /**
     * Calculate tax-inclusive sale price based on dynamic settings
     */
    public function getTaxInclusiveSalePriceAttribute(): ?float
    {
        if ($this->sale_price === null) {
            return null;
        }
        return \App\Services\PriceCalculationService::calculateTaxInclusivePrice($this->sale_price);
    }

    /**
     * Get the current tax-inclusive price (sale price if available, otherwise regular price)
     */
    public function getCurrentTaxInclusivePriceAttribute(): float
    {
        $basePrice = $this->sale_price ?? $this->price;
        return \App\Services\PriceCalculationService::calculateTaxInclusivePrice($basePrice);
    }

    /**
     * Calculate tax-inclusive price for bottle pricing tiers based on dynamic settings
     */
    public function getTaxInclusiveBottlePricingTiersAttribute(): ?array
    {
        if (!$this->bottle_pricing_tiers || !is_array($this->bottle_pricing_tiers)) {
            return null;
        }

        return array_map(function ($tier) {
            $tier['price'] = \App\Services\PriceCalculationService::calculateTaxInclusivePrice($tier['price'] ?? 0);
            if (isset($tier['price_per_capsule'])) {
                $tier['price_per_capsule'] = \App\Services\PriceCalculationService::calculateTaxInclusivePrice($tier['price_per_capsule']);
            }
            return $tier;
        }, $this->bottle_pricing_tiers);
    }
}
