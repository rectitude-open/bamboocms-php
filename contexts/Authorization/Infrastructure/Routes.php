<?php

declare(strict_types=1);

use App\Http\Middlewares\AuthenticateMiddleware;
use Contexts\Authorization\Presentation\Controllers\AuthenticationController;
use Contexts\Authorization\Presentation\Controllers\RoleController;
use Contexts\Authorization\Presentation\Controllers\UserIdentityController;
use Illuminate\Support\Facades\Route;

Route::name('Authentication')->withoutMiddleware(AuthenticateMiddleware::class)->group(function () {
    Route::controller(AuthenticationController::class)->prefix('auth')->name('Auth.')->group(function () {
        Route::post('login', 'login')->name('login');
    });
});

Route::middleware([])->name('Authorization.')->group(function () {
    Route::controller(UserIdentityController::class)->prefix('users')->name('User.')->group(function () {
        Route::get('{id}', 'getUser')->name('getUser');
        Route::get('', 'getUserList')->name('getUserList');
        Route::post('', 'createUser')->name('createUser');
        Route::patch('{id}/password', 'changePassword')->name('changePassword');
        Route::put('{id}/roles', 'updateRoles')->name('updateRoles');
        Route::put('{id}/suspend', 'suspendUser')->name('suspendUser');
        Route::put('{id}', 'updateUser')->name('updateUser');
        Route::delete('{id}', 'deleteUser')->name('deleteUser');
    });
    Route::controller(RoleController::class)->prefix('roles')->name('Role.')->group(function () {
        Route::get('{id}', 'getRole')->name('getRole');
        Route::get('', 'getRoleList')->name('getRoleList');
        Route::post('', 'createRole')->name('createRole');
        Route::patch('{id}/password', 'changePassword')->name('changePassword');
        Route::put('{id}/suspend', 'suspendRole')->name('suspendRole');
        Route::put('{id}', 'updateRole')->name('updateRole');
        Route::delete('{id}', 'deleteRole')->name('deleteRole');
    });
});
