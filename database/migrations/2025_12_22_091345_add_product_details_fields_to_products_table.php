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
            $table->decimal('price_per_capsule', 10, 2)->nullable()->after('sale_price');
            $table->decimal('price_per_mg', 10, 4)->nullable()->after('price_per_capsule');
            $table->string('country_of_origin')->nullable()->after('directions');
            $table->text('manufacturer')->nullable()->after('country_of_origin');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['price_per_capsule', 'price_per_mg', 'country_of_origin', 'manufacturer']);
        });
    }
};
