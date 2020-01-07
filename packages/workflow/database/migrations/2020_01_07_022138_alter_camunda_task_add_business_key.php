<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterCamundaTaskAddBusinessKey extends Migration
{
    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        Schema::table('camunda_task', function (Blueprint $table) {
            $table->jsonb('traceable')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        Schema::table('camunda_task', function (Blueprint $table) {
            $table->dropColumn(['traceable']);
        });
    }
}
