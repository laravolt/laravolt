<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected string $table;

    /**
     * AddStatusToUsers constructor.
     */
    public function __construct()
    {
        /** @var class-string $modelClass */
        $modelClass = config('laravolt.epicentrum.models.user');
        /** @var Model $model */
        $model = resolve($modelClass);
        $this->table = $model->getTable();
    }

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table($this->table, function (Blueprint $table): void {
            if (! Schema::hasColumn($this->table, 'status')) {
                $table->string('status')->after('email')->index()->nullable();
            }
            if (! Schema::hasColumn($this->table, 'timezone')) {
                $table->string('timezone')->default(config('app.timezone'))->after('status');
            }
            if (! Schema::hasColumn($this->table, 'password_changed_at')) {
                $table->timestamp('password_changed_at')->nullable()->after('password');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table($this->table, function (Blueprint $table): void {
            $table->dropColumn(['status', 'timezone', 'password_changed_at']);
        });
    }
};
