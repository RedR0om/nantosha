<?php

namespace App\Services;

use App\Models\Setting;

class PriceCalculationService
{
    /**
     * Calculate tax-inclusive price based on settings
     * Formula: base_price × (1 + price_increase%) × (1 + tax_rate%)
     */
    public static function calculateTaxInclusivePrice(float $basePrice): float
    {
        $taxEnabled = Setting::isEnabled('tax_enabled');
        
        if (!$taxEnabled) {
            // If tax is disabled, only apply price increase
            $priceIncrease = (float)Setting::get('price_increase_percentage', '10');
            return round($basePrice * (1 + $priceIncrease / 100), 2);
        }
        
        $priceIncrease = (float)Setting::get('price_increase_percentage', '10');
        $taxRate = (float)Setting::get('tax_rate', '10');
        
        // Apply price increase first, then tax
        $priceAfterIncrease = $basePrice * (1 + $priceIncrease / 100);
        $finalPrice = $priceAfterIncrease * (1 + $taxRate / 100);
        
        return round($finalPrice, 2);
    }

    /**
     * Calculate shipping fee based on subtotal and settings
     */
    public static function calculateShipping(float $subtotal): float
    {
        $shippingEnabled = Setting::isEnabled('shipping_enabled');
        
        if (!$shippingEnabled) {
            return 0; // Shipping included in prices
        }
        
        $freeShippingThreshold = (float)Setting::get('free_shipping_threshold', '10000');
        
        if ($subtotal >= $freeShippingThreshold) {
            return 0; // Free shipping
        }
        
        $shippingFee = (float)Setting::get('shipping_fee', '500');
        return $shippingFee;
    }

    /**
     * Get tax rate (for display purposes)
     */
    public static function getTaxRate(): float
    {
        if (!Setting::isEnabled('tax_enabled')) {
            return 0;
        }
        return (float)Setting::get('tax_rate', '10');
    }

    /**
     * Get price increase percentage (for display purposes)
     */
    public static function getPriceIncreasePercentage(): float
    {
        return (float)Setting::get('price_increase_percentage', '10');
    }

    /**
     * Check if tax is enabled
     */
    public static function isTaxEnabled(): bool
    {
        return Setting::isEnabled('tax_enabled');
    }

    /**
     * Check if shipping is enabled
     */
    public static function isShippingEnabled(): bool
    {
        return Setting::isEnabled('shipping_enabled');
    }

    /**
     * Calculate tax amount from tax-inclusive subtotal
     * This reverses the tax calculation to show the tax portion
     * 
     * @param float $taxInclusiveSubtotal The subtotal that already includes tax
     * @return float The tax amount portion
     */
    public static function calculateTaxAmount(float $taxInclusiveSubtotal): float
    {
        $taxEnabled = Setting::isEnabled('tax_enabled');
        
        if (!$taxEnabled) {
            return 0;
        }
        
        $priceIncrease = (float)Setting::get('price_increase_percentage', '10');
        $taxRate = (float)Setting::get('tax_rate', '10');
        
        // Reverse calculation: tax_inclusive = base × (1 + increase%) × (1 + tax%)
        // So: base = tax_inclusive / ((1 + increase%) × (1 + tax%))
        // Tax amount = tax_inclusive - base × (1 + increase%)
        $multiplier = (1 + $priceIncrease / 100) * (1 + $taxRate / 100);
        $baseSubtotal = $taxInclusiveSubtotal / $multiplier;
        $priceAfterIncrease = $baseSubtotal * (1 + $priceIncrease / 100);
        $taxAmount = $taxInclusiveSubtotal - $priceAfterIncrease;
        
        return round($taxAmount, 2);
    }

    /**
     * Calculate tax-inclusive price for a subtotal (sum of base prices)
     * This applies tax-inclusive calculation to the total of multiple items
     * 
     * @param float $baseSubtotal Sum of base prices
     * @return float Tax-inclusive subtotal
     */
    public static function calculateTaxInclusiveSubtotal(float $baseSubtotal): float
    {
        return self::calculateTaxInclusivePrice($baseSubtotal);
    }

    /**
     * Calculate subtotal before tax (for display purposes)
     */
    public static function calculateSubtotalBeforeTax(float $taxInclusiveSubtotal): float
    {
        $taxEnabled = Setting::isEnabled('tax_enabled');
        
        if (!$taxEnabled) {
            return $taxInclusiveSubtotal;
        }
        
        $priceIncrease = (float)Setting::get('price_increase_percentage', '10');
        $taxRate = (float)Setting::get('tax_rate', '10');
        
        $multiplier = (1 + $priceIncrease / 100) * (1 + $taxRate / 100);
        $baseSubtotal = $taxInclusiveSubtotal / $multiplier;
        $subtotalBeforeTax = $baseSubtotal * (1 + $priceIncrease / 100);
        
        return round($subtotalBeforeTax, 2);
    }
}
