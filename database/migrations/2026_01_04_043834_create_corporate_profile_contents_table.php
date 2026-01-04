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
        Schema::create('corporate_profile_contents', function (Blueprint $table) {
            $table->id();
            $table->string('section_type'); // page_title, company_info, office_location, contact_info
            $table->string('key')->nullable(); // Unique key for specific content items (e.g., 'title', 'subtitle', 'head_office', etc.)
            $table->string('title')->nullable();
            $table->string('subtitle')->nullable();
            $table->string('label')->nullable(); // For labels like "Company Name:", "CEO:", "Address:", etc.
            $table->text('text')->nullable();
            $table->text('value')->nullable(); // For values like company name, CEO name, addresses, etc.
            $table->string('field_type')->nullable(); // phone, email, address, fax, mobile, etc.
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
        Schema::dropIfExists('corporate_profile_contents');
    }
};
