<?php

declare(strict_types=1);

namespace Contexts\Authorization\Infrastructure;

use Contexts\Authorization\Application\Coordinators\CurrentUserServiceCoordinator;
use Contexts\Authorization\Application\Coordinators\GlobalPermissionServiceCoordinator;
use Contexts\Authorization\Contracts\V1\Services\CurrentUserService;
use Contexts\Authorization\Contracts\V1\Services\GlobalPermissionService;
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
        $this->mergeConfigFrom(__DIR__.'/Configs/article_publishing.php', 'policies.article_publishing');
        // Event::listen(
        //     UserCreatedEvent::class
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

        $this->app->bind(CurrentUserService::class, CurrentUserServiceCoordinator::class);
        $this->app->bind(GlobalPermissionService::class, GlobalPermissionServiceCoordinator::class);
    }

    public function provides(): array
    {
        return [];
    }
}
