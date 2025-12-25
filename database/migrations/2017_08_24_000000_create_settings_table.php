<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private readonly string $table;

    private readonly string $key;

    private readonly string $value;

    /**
     * Set up the options.
     */
    public function __construct()
    {
        /** @var string $table */
        $table = config('setting.database.table');
        /** @var string $key */
        $key = config('setting.database.key');
        /** @var string $value */
        $value = config('setting.database.value');

        $this->table = $table;
        $this->key = $key;
        $this->value = $value;
    }

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create($this->table, function (Blueprint $table): void {
            $table->increments('id');
            $table->string($this->key)->index();
            $table->text($this->value);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::drop($this->table);
    }
};
