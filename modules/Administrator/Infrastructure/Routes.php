<?php

use Illuminate\Support\Facades\Route;

// Route::get('adminstrators', function () {
//     return 'Administrators OK';
// });

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('adminstrators', function () {
        return 'Administrators sanctum OK';
    });
});
