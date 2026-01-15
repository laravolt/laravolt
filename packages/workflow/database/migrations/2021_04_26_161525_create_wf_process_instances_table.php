<?php

declare(strict_types=1);

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
        Schema::create('wf_process_instances', function (Blueprint $table): void {
            $table->string('id')->primary();
            $table->string('definition_id');
            $table->string('definition_key');
            $table->string('business_key')->nullable();
            $table->string('tenant_id')->nullable();
            $table->jsonb('variables');
            $table->jsonb('tasks');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workflow_process_instances');
    }
};
