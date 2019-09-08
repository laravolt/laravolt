<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLaravoltAttributesToUsers extends Migration
{
    protected $table;

    /**
     * AddStatusToUsers constructor.
     */
    public function __construct()
    {
        $this->table = app(config('auth.providers.users.model'))->getTable();
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table($this->table, function (Blueprint $table) {
            $table->string('status')->after('email')->index()->nullable();
            $table->string('timezone')->default(config('app.timezone'))->after('status');
            $table->timestamp('password_changed_at')->nullable()->after('password');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table($this->table, function (Blueprint $table) {
            $table->dropColumn(['status', 'timezone', 'password_changed_at']);
        });
    }
}
