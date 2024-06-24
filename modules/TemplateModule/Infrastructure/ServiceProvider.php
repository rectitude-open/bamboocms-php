<?php

namespace Modules\TemplateModule\Infrastructure;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    public function boot(): void {}

    public function register(): void
    {
        $this->app->register(new class($this->app) extends RouteServiceProvider
        {
            public function boot(): void
            {
                parent::boot();
            }

            public function map(): void
            {
                Route::middleware('api')->prefix('api')->name('api.')->group(__DIR__.'/Routes.php');
            }
        });
    }

    public function provides(): array
    {
        return [];
    }
}
