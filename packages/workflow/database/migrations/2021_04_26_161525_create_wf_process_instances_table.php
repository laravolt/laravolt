<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wf_process_instances', function (Blueprint $table) {
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
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('workflow_process_instances');
    }
};
