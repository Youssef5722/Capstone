<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 100);
            $table->string('university_id', 50)->unique();
            $table->string('email', 150)->nullable()->unique();
            $table->string('password')->nullable();
            $table->string('activation_code', 20)->nullable()->unique();
            $table->boolean('is_active')->default(false);
            $table->unsignedInteger('level_id');
            $table->unsignedInteger('academic_year_id');
            $table->timestamp('activation_code_expires_at')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('level_id')->references('id')->on('levels');
            $table->foreign('academic_year_id')->references('id')->on('academic_years');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
