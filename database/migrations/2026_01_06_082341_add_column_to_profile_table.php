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
        Schema::table('profiles', function (Blueprint $table) {
            // change column name bio to bio_short
            $table->renameColumn('bio', 'bio_short');
            $table->longText('bio_long')->nullable()->after('bio_short');
            $table->string('location')->nullable()->after('bio_long');
            $table->boolean('is_hireable')->default(false)->after('location');
            $table->json('hero_image_codes')->nullable()->after('socials');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('profiles', function (Blueprint $table) {
            $table->renameColumn('bio_short', 'bio');
            $table->dropColumn('bio_long');
            $table->dropColumn('location');
            $table->dropColumn('is_hireable');
            $table->dropColumn('hero_image_codes');
        });
    }
};
