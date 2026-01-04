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
        Schema::create('dr_landrito_profile_contents', function (Blueprint $table) {
            $table->id();
            $table->string('section_type'); // profile_header, education, career, clinical_practice, current_focus, award, publication, photo, newsletter_info, medical_professionals
            $table->string('key')->nullable(); // Unique key for specific content items (e.g., 'title', 'subtitle', 'photo1', etc.)
            $table->string('title')->nullable();
            $table->string('subtitle')->nullable();
            $table->text('text')->nullable();
            $table->text('description')->nullable();
            $table->string('image_url')->nullable();
            $table->string('image_alt')->nullable();
            $table->string('caption')->nullable();
            $table->json('content')->nullable(); // For structured content like awards list
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index(['section_type', 'sort_order']);
            $table->unique(['section_type', 'key']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dr_landrito_profile_contents');
    }
};
