<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SegmentMigration extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('segments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('segment_name');
            $table->integer('segment_order')->default(0);
            $table->string('task_name');
            $table->string('process_definition_key');
            $table->unique(['process_definition_key', 'task_name', 'segment_name', 'segment_order'], 'unique_segment_order');
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
        Schema::drop('segments');
    }
}
