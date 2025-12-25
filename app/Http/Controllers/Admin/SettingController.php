<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SettingController extends Controller
{
    public function index()
    {
        $multiProductMode = Setting::isEnabled('multi_product_mode');
        
        // Tax and Shipping Settings
        $taxSettings = [
            'tax_enabled' => Setting::isEnabled('tax_enabled'),
            'tax_rate' => (float)Setting::get('tax_rate', '10'),
            'price_increase_percentage' => (float)Setting::get('price_increase_percentage', '10'),
        ];
        
        $shippingSettings = [
            'shipping_enabled' => Setting::isEnabled('shipping_enabled'),
            'shipping_fee' => (float)Setting::get('shipping_fee', '500'),
            'free_shipping_threshold' => (float)Setting::get('free_shipping_threshold', '10000'),
        ];

        return Inertia::render('Admin/Settings/Index', [
            'multiProductMode' => $multiProductMode,
            'taxSettings' => $taxSettings,
            'shippingSettings' => $shippingSettings,
        ]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'multi_product_mode' => 'nullable|boolean',
            // Tax settings
            'tax_enabled' => 'nullable|boolean',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'price_increase_percentage' => 'nullable|numeric|min:0|max:100',
            // Shipping settings
            'shipping_enabled' => 'nullable|boolean',
            'shipping_fee' => 'nullable|numeric|min:0',
            'free_shipping_threshold' => 'nullable|numeric|min:0',
        ]);

        // Update multi product mode if provided
        if (isset($validated['multi_product_mode'])) {
            Setting::set('multi_product_mode', $validated['multi_product_mode'] ? '1' : '0');
        }

        // Update tax settings if provided
        if (isset($validated['tax_enabled'])) {
            Setting::set('tax_enabled', $validated['tax_enabled'] ? '1' : '0');
        }
        if (isset($validated['tax_rate'])) {
            Setting::set('tax_rate', (string)$validated['tax_rate']);
        }
        if (isset($validated['price_increase_percentage'])) {
            Setting::set('price_increase_percentage', (string)$validated['price_increase_percentage']);
        }

        // Update shipping settings if provided
        if (isset($validated['shipping_enabled'])) {
            Setting::set('shipping_enabled', $validated['shipping_enabled'] ? '1' : '0');
        }
        if (isset($validated['shipping_fee'])) {
            Setting::set('shipping_fee', (string)$validated['shipping_fee']);
        }
        if (isset($validated['free_shipping_threshold'])) {
            Setting::set('free_shipping_threshold', (string)$validated['free_shipping_threshold']);
        }

        return back()->with('success', 'Settings updated successfully!');
    }
}
