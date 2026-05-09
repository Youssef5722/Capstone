<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('team_project', function (Blueprint $table) {
            // FK → teams.id (bigIncrements = UNSIGNED BIGINT)
            // Unique: one project assignment per team
            $table->unsignedBigInteger('team_id')->unique();

            // FK → project_ideas.id (bigIncrements = UNSIGNED BIGINT)
            $table->unsignedBigInteger('project_idea_id');

            // No timestamps() per SOP

            $table->foreign('team_id')
                  ->references('id')->on('teams')
                  ->onDelete('cascade');

            $table->foreign('project_idea_id')
                  ->references('id')->on('project_ideas')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('team_project');
    }
};
