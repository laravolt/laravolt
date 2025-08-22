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
        Schema::create('wf_shar_workflows', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->longText('bpmn_xml');
            $table->text('description')->nullable();
            $table->unsignedInteger('version')->default(1);
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->unique(['name', 'version']);
            $table->index(['name', 'status']);
            $table->index('created_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wf_shar_workflows');
    }
};