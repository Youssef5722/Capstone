<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('team_student', function (Blueprint $table) {
            // FK → teams.id (bigIncrements = UNSIGNED BIGINT)
            $table->unsignedBigInteger('team_id');

            // FK → students.id (increments = UNSIGNED INT)
            $table->unsignedInteger('student_id');

            // Required for scoping and the UNIQUE constraint
            $table->unsignedInteger('academic_year_id');

            // No timestamps() per SOP §5.3

            $table->foreign('team_id')
                  ->references('id')->on('teams')
                  ->onDelete('cascade');

            $table->foreign('student_id')
                  ->references('id')->on('students')
                  ->onDelete('cascade');

            $table->foreign('academic_year_id')
                  ->references('id')->on('academic_years')
                  ->onDelete('cascade');

            // One team per student per academic year (application layer also enforces this)
            $table->unique(['student_id', 'academic_year_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('team_student');
    }
};
