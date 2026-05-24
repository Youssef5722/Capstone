<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sub_tasks', function (Blueprint $table) {
            $table->bigIncrements('id');

            // FK → tasks.id (bigIncrements)
            $table->unsignedBigInteger('task_id');

            // FK → students.id (increments = UNSIGNED INT) — any team member
            $table->unsignedInteger('assigned_to');

            $table->string('title', 255);
            $table->text('description')->nullable();

            $table->enum('status', ['pending', 'in_progress', 'submitted', 'approved', 'rejected'])->default('pending');

            $table->date('deadline')->nullable();

            $table->timestamps();

            $table->foreign('task_id')
                  ->references('id')->on('tasks')
                  ->onDelete('cascade');

            $table->foreign('assigned_to')
                  ->references('id')->on('students')
                  ->onDelete('cascade');

            // Performance: sub-tasks are always queried by their parent task
            $table->index(['task_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sub_tasks');
    }
};
