<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add explicit onDelete('restrict') to foreign keys that previously
     * had no onDelete clause (defaulting to RESTRICT implicitly, but
     * now made explicit for clarity and consistency).
     *
     * Tables affected:
     *   - students.level_id
     *   - students.academic_year_id
     *   - users.role_id
     */
    public function up(): void
    {
        // ── students table ────────────────────────────────────────────────
        Schema::table('students', function (Blueprint $table) {
            // Drop existing foreign keys before re-adding with explicit onDelete
            $table->dropForeign(['level_id']);
            $table->dropForeign(['academic_year_id']);

            $table->foreign('level_id')
                  ->references('id')->on('levels')
                  ->onDelete('restrict');

            $table->foreign('academic_year_id')
                  ->references('id')->on('academic_years')
                  ->onDelete('restrict');
        });

        // ── users table ───────────────────────────────────────────────────
        Schema::table('users', function (Blueprint $table) {
            // Drop existing foreign key before re-adding with explicit onDelete
            $table->dropForeign(['role_id']);

            $table->foreign('role_id')
                  ->references('id')->on('roles')
                  ->onDelete('restrict');
        });
    }

    /**
     * Reverse: drop and re-add foreign keys without onDelete clause
     * to restore the previous implicit-RESTRICT state.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropForeign(['level_id']);
            $table->dropForeign(['academic_year_id']);

            $table->foreign('level_id')->references('id')->on('levels');
            $table->foreign('academic_year_id')->references('id')->on('academic_years');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->foreign('role_id')->references('id')->on('roles');
        });
    }
};
