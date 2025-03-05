<?php

declare(strict_types=1);

namespace Contexts\CategoryManagement\Infrastructure;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/Migrations');
        $this->loadJsonTranslationsFrom(__DIR__.'/Lang');
        // Event::listen(
        //     CategoryPublishedEvent::class
        // );
    }

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
                Route::middleware('api')->prefix(config('ROUTE_PREFIX'))->group(__DIR__.'/Routes.php');
            }
        });
    }

    public function provides(): array
    {
        return [];
    }
}
