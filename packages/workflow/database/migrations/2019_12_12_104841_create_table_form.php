<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableForm extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('form', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->unique();
            $table->timestamps();
        });

        Schema::create('form_field', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('form_id');
            $table->string('name');
            $table->string('label');
            $table->string('type');
            $table->unsignedSmallInteger('order')->default(1);
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->foreign('form_id')->references('id')->on('form')->onDelete('cascade');
            $table->unique(['form_id', 'name']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('form_field');
        Schema::dropIfExists('form');
    }
}
