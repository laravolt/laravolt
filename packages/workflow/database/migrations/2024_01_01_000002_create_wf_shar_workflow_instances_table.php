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
        Schema::create('wf_shar_workflow_instances', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('workflow_name');
            $table->enum('status', ['launching', 'running', 'completed', 'failed', 'cancelled'])->default('launching');
            $table->json('variables')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->index(['workflow_name', 'status']);
            $table->index('status');
            $table->index('created_by');
            $table->index('started_at');
            $table->index('completed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wf_shar_workflow_instances');
    }
};