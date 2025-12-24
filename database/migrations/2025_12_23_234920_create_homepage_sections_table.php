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
        Schema::create('homepage_sections', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // hero, features, product_info, pricing, how_it_works, custom
            $table->string('title')->nullable();
            $table->string('title_ja')->nullable(); // Japanese title
            $table->text('subtitle')->nullable();
            $table->text('subtitle_ja')->nullable(); // Japanese subtitle
            $table->json('content')->nullable(); // Structured content (English) - can contain badges, buttons, features, pricing tiers, steps, etc.
            $table->json('content_ja')->nullable(); // Structured content (Japanese)
            $table->string('background_color')->default('white'); // white, gray-50, etc.
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('homepage_sections');
    }
};
