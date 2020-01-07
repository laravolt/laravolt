<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnCalledElementCamundaForm extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('camunda_form', function (Blueprint $table) {
            $table->string('called_element')->nullable()->after('field_label');
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
            $table->dropColumn('called_element');
        });
    }
}
