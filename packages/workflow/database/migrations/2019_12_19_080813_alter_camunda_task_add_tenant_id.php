<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterCamundaTaskAddTenantId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('camunda_task', function (Blueprint $table) {
            $table->unsignedBigInteger('id_kantor')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('camunda_task', function (Blueprint $table) {
            $table->dropColumn('id_kantor');
        });
    }
}
