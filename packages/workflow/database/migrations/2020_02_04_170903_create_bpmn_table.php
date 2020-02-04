<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBpmnTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('workflow_bpmn', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('filename');
            $table->string('process_definition_id');
            $table->string('process_definition_key');
            $table->unsignedInteger('version');
            $table->string('deployment_id');
            $table->timestamp('deployed_at');
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
        Schema::dropIfExists('workflow_bpmn');
    }
}
