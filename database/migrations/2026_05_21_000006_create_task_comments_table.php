<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('task_comments', function (Blueprint $table) {
            $table->bigIncrements('id');

            // Polymorphic target: Task or SubTask
            $table->unsignedBigInteger('commentable_id');
            $table->string('commentable_type', 255);

            // Polymorphic author: User (doctor) or Student
            $table->unsignedInteger('commented_by_id');
            $table->string('commented_by_type', 255);

            $table->text('comment');

            $table->timestamps();

            // Polymorphic index on the commentable (task/subtask)
            $table->index(['commentable_type', 'commentable_id']);

            // Index on the commenter (doctor/student)
            $table->index(['commented_by_type', 'commented_by_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('task_comments');
    }
};
