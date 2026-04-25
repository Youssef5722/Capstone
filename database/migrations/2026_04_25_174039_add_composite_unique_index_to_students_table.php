<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            // Drop the global unique constraint — university_id should only be unique per year
            $table->dropUnique(['university_id']);

            // Composite unique: a student cannot appear twice in the same year
            $table->unique(['university_id', 'academic_year_id']);

            // Composite index: every student query scopes by both columns — required for performance
            $table->index(['academic_year_id', 'level_id']);
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropIndex(['academic_year_id', 'level_id']);
            $table->dropUnique(['university_id', 'academic_year_id']);
            $table->unique(['university_id']);
        });
    }
};
