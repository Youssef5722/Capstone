<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->bigIncrements('id');

            // FK → workspaces.id (bigIncrements)
            $table->unsignedBigInteger('workspace_id');

            // FK → phases.id (bigIncrements)
            $table->unsignedBigInteger('phase_id');

            // FK → users.id (increments = UNSIGNED INT) — doctor who created the task
            $table->unsignedInteger('created_by');

            // FK → students.id (increments = UNSIGNED INT) — always the team leader
            $table->unsignedInteger('assigned_to');

            $table->string('title', 255);
            $table->text('description')->nullable();

            $table->enum('priority', ['low', 'medium', 'high'])->default('medium');
            $table->enum('status', ['pending', 'in_progress', 'submitted', 'approved', 'rejected'])->default('pending');

            $table->date('deadline')->nullable();

            $table->timestamps();

            $table->foreign('workspace_id')
                  ->references('id')->on('workspaces')
                  ->onDelete('cascade');

            $table->foreign('phase_id')
                  ->references('id')->on('phases')
                  ->onDelete('cascade');

            $table->foreign('created_by')
                  ->references('id')->on('users')
                  ->onDelete('cascade');

            $table->foreign('assigned_to')
                  ->references('id')->on('students')
                  ->onDelete('cascade');

            // Performance: every task query scopes by workspace + phase + status
            $table->index(['workspace_id', 'phase_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
