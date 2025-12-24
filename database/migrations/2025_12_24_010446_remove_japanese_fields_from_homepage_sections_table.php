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
        Schema::table('homepage_sections', function (Blueprint $table) {
            $table->dropColumn(['title_ja', 'subtitle_ja', 'content_ja']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('homepage_sections', function (Blueprint $table) {
            $table->string('title_ja')->nullable()->after('title');
            $table->text('subtitle_ja')->nullable()->after('subtitle');
            $table->json('content_ja')->nullable()->after('content');
        });
    }
};
