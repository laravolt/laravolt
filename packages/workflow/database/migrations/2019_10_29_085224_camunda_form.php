<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CamundaForm extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('camunda_form', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('process_definition_key');
            $table->string('task_name');
            $table->string('form_name');
            $table->string('field_name');
            $table->string('field_type');
            $table->string('field_label');
            $table->string('segment_group')->nullable();
            $table->string('segment_order')->nullable();
            $table->integer('field_order')->default(0);
            $table->text('field_select_query')->nullable();
            $table->json('field_meta')->nullable();
            $table->string('type')->default(\Laravolt\Workflow\Enum\FormType::MAIN_FORM)->comment('See '.\Laravolt\Workflow\Enum\FormType::class);
            $table->timestamps();
            $table->unique(['process_definition_key', 'form_name', 'field_name'], 'unique_field_per_form');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('camunda_form');
    }
}
