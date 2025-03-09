<?php

declare(strict_types=1);

use Contexts\Authorization\Presentation\Controllers\AuthorizationController;
use Illuminate\Support\Facades\Route;

Route::middleware([])->name('Authorization.')->group(function () {
    Route::controller(AuthorizationController::class)->prefix('users')->name('Authorization.')->group(function () {
        Route::get('{id}', 'getUser')->name('getUser');
        Route::get('', 'getUserList')->name('getUserList');
        Route::post('', 'createUser')->name('createUser');
        Route::put('{id}/subspend', 'subspendUser')->name('subspendUser');
        Route::put('{id}', 'updateUser')->name('updateUser');
        Route::delete('{id}', 'deleteUser')->name('deleteUser');
    });
});
