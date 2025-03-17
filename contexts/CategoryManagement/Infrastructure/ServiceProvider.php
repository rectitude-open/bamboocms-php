<?php

declare(strict_types=1);

namespace Contexts\CategoryManagement\Infrastructure;

use Contexts\CategoryManagement\Domain\Repositories\CategoryRepository;
use Contexts\CategoryManagement\Infrastructure\Persistence\CategoryPersistence;
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
        //     CategoryCreatedEvent::class
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
                Route::middleware('api')->prefix(config('app.backend_route_prefix'))->group(__DIR__.'/Routes.php');
            }
        });
        $this->app->bind(CategoryRepository::class, CategoryPersistence::class);
    }

    public function provides(): array
    {
        return [];
    }
}
