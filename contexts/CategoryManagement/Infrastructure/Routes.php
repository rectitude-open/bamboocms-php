<?php

declare(strict_types=1);

use Contexts\CategoryManagement\Presentation\Controllers\CategoryManagementController;
use Illuminate\Support\Facades\Route;

Route::middleware([])->name('CategoryManagement.')->group(function () {
    Route::controller(CategoryManagementController::class)->prefix('categories')->name('CategoryManagement.')->group(function () {
        Route::get('{id}', 'getCategory')->name('getCategory');
        Route::get('', 'getCategoryList')->name('getCategoryList');
        Route::post('', 'createCategory')->name('createCategory');
        Route::put('{id}/archive', 'archiveCategory')->name('archiveCategory');
        Route::put('{id}/publish', 'publishDraft')->name('publishDraft');
        Route::put('{id}', 'updateCategory')->name('updateCategory');
        Route::delete('{id}', 'deleteCategory')->name('deleteCategory');
    });
});
