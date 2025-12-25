<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('acl_permission_role', function (Blueprint $table): void {
            $table->ulid('permission_id');
            $table->ulid('role_id');

            $table->foreign('permission_id')->references('id')->on('acl_permissions')->onDelete('cascade');
            $table->foreign('role_id')->references('id')->on('acl_roles')->onDelete('cascade');

            $table->primary(['permission_id', 'role_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::drop('acl_permission_role');
    }
};
