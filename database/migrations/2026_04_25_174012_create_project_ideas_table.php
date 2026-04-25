<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_ideas', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedInteger('doctor_id');  // matches users.id = increments() = UNSIGNED INT
            $table->unsignedInteger('level_id');
            $table->unsignedInteger('academic_year_id');

            $table->string('title', 255);
            $table->text('description')->nullable();

            $table->timestamps();

            $table->foreign('doctor_id')
                  ->references('id')->on('users')
                  ->cascadeOnDelete();

            $table->foreign('level_id')
                  ->references('id')->on('levels')
                  ->cascadeOnDelete();

            $table->foreign('academic_year_id')
                  ->references('id')->on('academic_years')
                  ->cascadeOnDelete();

            // Performance: every query scopes by doctor + level + year
            $table->index(['doctor_id', 'level_id', 'academic_year_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_ideas');
    }
};
