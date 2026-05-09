<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teams', function (Blueprint $table) {
            $table->bigIncrements('id');

            // Nullable name — may be auto-generated on distribution or set later via request
            $table->string('name', 255)->nullable();

            // FK → students.id (increments = UNSIGNED INT)
            $table->unsignedInteger('leader_id');

            // FK → levels.id (increments = UNSIGNED INT)
            $table->unsignedInteger('level_id');

            // FK → academic_years.id (increments = UNSIGNED INT)
            $table->unsignedInteger('academic_year_id');

            $table->timestamps();

            $table->foreign('leader_id')
                  ->references('id')->on('students')
                  ->onDelete('cascade');

            $table->foreign('level_id')
                  ->references('id')->on('levels')
                  ->onDelete('cascade');

            $table->foreign('academic_year_id')
                  ->references('id')->on('academic_years')
                  ->onDelete('cascade');

            // Performance: every team query scopes by level + year
            $table->index(['level_id', 'academic_year_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teams');
    }
};
