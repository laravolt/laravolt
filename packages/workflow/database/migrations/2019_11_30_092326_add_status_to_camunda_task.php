<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusToCamundaTask extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('camunda_task', function (Blueprint $table) {
            $table->string('status')->after('form_id')->default(\Laravolt\Camunda\Enum\TaskStatus::NEW);
            $table->index('status');
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
            $table->dropColumn('status');
        });
    }
}
