<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $table;

    /**
     * AddStatusToUsers constructor.
     */
    public function __construct()
    {
        $this->table = app(config('laravolt.epicentrum.models.user'))->getTable();
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table($this->table, function (Blueprint $table) {
            if (!Schema::hasColumn($this->table, 'status')) {
                $table->string('status')->after('email')->index()->nullable();
            }
            if (!Schema::hasColumn($this->table, 'timezone')) {
                $table->string('timezone')->default(config('app.timezone'))->after('status');
            }
            if (!Schema::hasColumn($this->table, 'password_changed_at')) {
                $table->timestamp('password_changed_at')->nullable()->after('password');
            }
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
};
