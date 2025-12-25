<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add default tax and shipping settings
        $defaultSettings = [
            [
                'key' => 'tax_enabled',
                'value' => '1', // Enabled by default
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'tax_rate',
                'value' => '10', // 10% tax rate
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'price_increase_percentage',
                'value' => '10', // 10% price increase
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'shipping_enabled',
                'value' => '1', // Enabled by default
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'shipping_fee',
                'value' => '500', // Default shipping fee
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'free_shipping_threshold',
                'value' => '10000', // Free shipping over ¥10,000
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($defaultSettings as $setting) {
            DB::table('settings')->updateOrInsert(
                ['key' => $setting['key']],
                $setting
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('settings')->whereIn('key', [
            'tax_enabled',
            'tax_rate',
            'price_increase_percentage',
            'shipping_enabled',
            'shipping_fee',
            'free_shipping_threshold',
        ])->delete();
    }
};
