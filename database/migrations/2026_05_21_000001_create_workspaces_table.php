<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workspaces', function (Blueprint $table) {
            $table->bigIncrements('id');

            // FK → teams.id (bigIncrements = UNSIGNED BIGINT)
            // UNIQUE: one workspace per team
            $table->unsignedBigInteger('team_id')->unique();

            // FK → academic_years.id (increments = UNSIGNED INT)
            $table->unsignedInteger('academic_year_id');

            // FK → levels.id (increments = UNSIGNED INT)
            $table->unsignedInteger('level_id');

            $table->enum('status', ['active', 'completed', 'suspended'])->default('active');

            $table->timestamps();

            $table->foreign('team_id')
                  ->references('id')->on('teams')
                  ->onDelete('cascade');

            $table->foreign('academic_year_id')
                  ->references('id')->on('academic_years')
                  ->onDelete('cascade');

            $table->foreign('level_id')
                  ->references('id')->on('levels')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workspaces');
    }
};
