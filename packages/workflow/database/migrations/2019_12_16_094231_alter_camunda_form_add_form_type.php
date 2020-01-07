<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterCamundaFormAddFormType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('camunda_form', function (Blueprint $table) {
            $table->string('type')->default(\Laravolt\Camunda\Enum\FormType::MAIN_FORM)->comment("See " . \Laravolt\Workflow\Enum\FormType::class);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('camunda_form', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
}
