<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('phases', function (Blueprint $table) {
            $table->bigIncrements('id');

            // FK → workspaces.id (bigIncrements = UNSIGNED BIGINT)
            $table->unsignedBigInteger('workspace_id');

            $table->string('title', 255);
            $table->text('description')->nullable();
            $table->date('start_date');
            $table->date('end_date');

            $table->enum('status', ['upcoming', 'active', 'completed'])->default('upcoming');

            // Display order of phases within a workspace
            $table->unsignedTinyInteger('order')->default(1);

            $table->timestamps();

            $table->foreign('workspace_id')
                  ->references('id')->on('workspaces')
                  ->onDelete('cascade');

            // Performance: phases are always queried by workspace and sorted by order
            $table->index(['workspace_id', 'order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('phases');
    }
};
