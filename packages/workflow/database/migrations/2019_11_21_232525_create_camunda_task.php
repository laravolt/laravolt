<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCamundaTask extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('camunda_task', function (Blueprint $table) {
            $table->string('process_definition_key');
            $table->string('process_instance_id');
            $table->string('task_name');
            $table->string('task_id')->nullable();
            $table->morphs('form');
            $table->timestamps();

            $table->unique(['form_type', 'form_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('camunda_task');
    }
}
