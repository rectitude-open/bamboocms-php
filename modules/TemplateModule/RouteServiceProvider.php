<?php

namespace Modules\TemplateModule;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        parent::boot();
    }

    public function map(): void
    {
        Route::middleware('api')->prefix('api')->name('api.')->group(__DIR__.'/Infrastructure/Routes/api.php');
    }
}
