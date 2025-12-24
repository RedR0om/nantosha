<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->boolean('is_bottle_based')->default(false)->after('price_per_mg');
            $table->integer('capsules_per_bottle')->default(50)->nullable()->after('is_bottle_based');
            $table->json('bottle_pricing_tiers')->nullable()->after('capsules_per_bottle');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['is_bottle_based', 'capsules_per_bottle', 'bottle_pricing_tiers']);
        });
    }
};
