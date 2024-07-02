<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\AdministratorRole\Application\Admin\Controllers\AdministratorRoleController as AdminAdministratorRoleController;

Route::middleware([])->name('AdministratorRole.')->group(function () {
    Route::prefix('admin')->name('Admin.')->group(function () {
        Route::controller(AdminAdministratorRoleController::class)->prefix('administrator-roles')->name('AdministratorRole.')->group(function () {
            Route::get('', 'index')->name('index');
            Route::post('', 'store')->name('store');
            Route::get('{id}', 'show')->name('show');
            // Route::put('{id}', 'update')->name('update');
            // Route::delete('bulk', 'bulkDestroy')->name('bulkDestroy');
            // Route::delete('{id}', 'destroy')->name('destroy');
        });
    });
});
