<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['auth:admin'])->group(function () {
    Route::get('adminstrators', function () {
        return 'Administrators sanctum OK';
    });
});
