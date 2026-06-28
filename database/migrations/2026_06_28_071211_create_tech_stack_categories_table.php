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
        Schema::create('tech_stack_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('slug', 100)->unique();
            $table->string('color', 50)->nullable();
            $table->timestamps();
        });

        Schema::table('tech_stacks', function (Blueprint $table) {
            $table->dropColumn('category_id');
            $table->foreignId('tech_stack_category_id')->nullable()->constrained('tech_stack_categories')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tech_stacks', function (Blueprint $table) {
            $table->dropForeign(['tech_stack_category_id']);
            $table->dropColumn('tech_stack_category_id');
            $table->foreignId('category_id')->nullable();
        });

        Schema::dropIfExists('tech_stack_categories');
    }
};
