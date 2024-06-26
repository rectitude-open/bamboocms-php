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
        Schema::create('administrator_roles', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->unique();
            $table->string('description')->default('');

            $table->timestamps();
        });

        Schema::create('administrator_permissions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->unique();

            $table->timestamps();
        });

        Schema::create('pivot_administrator_role_permission', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('administrator_role_id');
            $table->unsignedInteger('administrator_permission_id');

            $table->foreign('administrator_role_id', 'fk_pivot_admin_role')->references('id')->on('administrator_roles')->onDelete('cascade');
            $table->foreign('administrator_permission_id', 'fk_pivot_admin_permission')->references('id')->on('administrator_permissions')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('administrator_roles');
        Schema::dropIfExists('administrator_permissions');
        Schema::dropIfExists('pivot_administrator_role_permission');
    }
};
