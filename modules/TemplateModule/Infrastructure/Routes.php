<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\TemplateModule\Application\Admin\Controllers\TemplateModuleController as AdminTemplateModuleController;

Route::middleware([])->name('TemplateModule.')->group(function () {
    Route::prefix('admin')->name('Admin.')->group(function () {
        Route::controller(AdminTemplateModuleController::class)->prefix('template-modules')->name('TemplateModule.')->group(function () {
            Route::get('', 'index')->name('index');
            Route::post('', 'store')->name('store');
            Route::get('{id}', 'show')->name('show');
            Route::put('{id}', 'update')->name('update');
            Route::delete('bulk', 'bulkDestroy')->name('bulkDestroy');
            Route::delete('{id}', 'destroy')->name('destroy');
        });
    });
});
