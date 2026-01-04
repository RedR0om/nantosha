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
        Schema::table('corporate_profile_contents', function (Blueprint $table) {
            // Remove the unique constraint on section_type and key
            // This allows multiple entries with the same section_type and key
            // (e.g., multiple office_location entries with key='head_office' but different field_types)
            $table->dropUnique(['section_type', 'key']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('corporate_profile_contents', function (Blueprint $table) {
            // Re-add the unique constraint if rolling back
            $table->unique(['section_type', 'key']);
        });
    }
};
