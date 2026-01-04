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
        Schema::create('checkout_contents', function (Blueprint $table) {
            $table->id();
            $table->string('section_type'); // risks_prohibitions, shipping_customs
            $table->string('key')->nullable(); // Unique key for specific content items (e.g., 'risks_title', 'prohibitions_title', 'shipping_info')
            $table->string('title')->nullable(); // Section title or item title
            $table->text('content')->nullable(); // Main content text
            $table->text('description')->nullable(); // Additional description
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index(['section_type', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('checkout_contents');
    }
};
