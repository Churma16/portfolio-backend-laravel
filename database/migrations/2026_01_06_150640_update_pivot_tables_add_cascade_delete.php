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
        // Add foreign key constraints with cascade delete
        Schema::table('project_tech_stack', function (Blueprint $table) {
            $table->foreign('project_id')->references('id')->on('projects')->cascadeOnDelete();
            $table->foreign('tech_stack_id')->references('id')->on('tech_stacks')->cascadeOnDelete();
        });

        Schema::table('project_tag', function (Blueprint $table) {
            $table->foreign('project_id')->references('id')->on('projects')->cascadeOnDelete();
            $table->foreign('tag_id')->references('id')->on('tags')->cascadeOnDelete();
        });

        Schema::table('tag_work_experience', function (Blueprint $table) {
            $table->foreign('tag_id')->references('id')->on('tags')->cascadeOnDelete();
            $table->foreign('work_experience_id')->references('id')->on('work_experiences')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop added constraints
        Schema::table('project_tech_stack', function (Blueprint $table) {
            $table->dropForeign(['project_id']);
            $table->dropForeign(['tech_stack_id']);
        });

        Schema::table('project_tag', function (Blueprint $table) {
            $table->dropForeign(['project_id']);
            $table->dropForeign(['tag_id']);
        });

        Schema::table('tag_work_experience', function (Blueprint $table) {
            $table->dropForeign(['tag_id']);
            $table->dropForeign(['work_experience_id']);
        });

        Schema::table('tech_stack_work_experience', function (Blueprint $table) {
            $table->dropForeign(['tech_stack_id']);
            $table->dropForeign(['work_experience_id']);
        });
    }
};
