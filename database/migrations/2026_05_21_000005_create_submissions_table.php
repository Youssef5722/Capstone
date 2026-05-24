<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('submissions', function (Blueprint $table) {
            $table->bigIncrements('id');

            // Polymorphic: Task or SubTask
            $table->unsignedBigInteger('submittable_id');
            $table->string('submittable_type', 255);

            // FK → students.id (increments = UNSIGNED INT)
            $table->unsignedInteger('submitted_by');

            $table->string('file_path', 500);
            $table->string('file_name', 255);
            $table->string('file_type', 100);

            $table->enum('status', ['pending', 'approved', 'rejected', 'revision_required'])->default('pending');

            // Filled when status = rejected
            $table->text('rejection_reason')->nullable();

            // UNSIGNED INT — can be users.id (doctor) or students.id (leader)
            // No FK enforced because reviewer type varies (mixed model references)
            $table->unsignedInteger('reviewed_by')->nullable();
            $table->timestamp('reviewed_at')->nullable();

            $table->timestamps();

            $table->foreign('submitted_by')
                  ->references('id')->on('students')
                  ->onDelete('cascade');

            // Polymorphic index — required for morphTo performance
            $table->index(['submittable_type', 'submittable_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('submissions');
    }
};
