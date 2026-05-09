<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('team_requests', function (Blueprint $table) {
            $table->bigIncrements('id');

            // FK → teams.id (bigIncrements = UNSIGNED BIGINT)
            $table->unsignedBigInteger('team_id');

            // What the leader wants to change — at least one must be non-null (app-layer only)
            $table->string('requested_name', 255)->nullable();

            // FK → project_ideas.id (bigIncrements = UNSIGNED BIGINT)
            $table->unsignedBigInteger('project_idea_id')->nullable();

            // Workflow status — NO DB UNIQUE(team_id, status) per SOP §12
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');

            // FK → students.id (increments = UNSIGNED INT)
            $table->unsignedInteger('requested_by');

            // FK → users.id (increments = UNSIGNED INT), nullable until reviewed
            $table->unsignedInteger('reviewed_by')->nullable();

            $table->timestamp('reviewed_at')->nullable();

            $table->timestamps();

            $table->foreign('team_id')
                  ->references('id')->on('teams')
                  ->onDelete('cascade');

            $table->foreign('project_idea_id')
                  ->references('id')->on('project_ideas')
                  ->onDelete('set null');

            $table->foreign('requested_by')
                  ->references('id')->on('students')
                  ->onDelete('cascade');

            $table->foreign('reviewed_by')
                  ->references('id')->on('users')
                  ->onDelete('set null');

            // Performance: doctors query pending requests by team
            $table->index(['team_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('team_requests');
    }
};
