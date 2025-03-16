<?php

declare(strict_types=1);

namespace Contexts\ArticlePublishing\Infrastructure;

use Contexts\ArticlePublishing\Domain\Events\ArticlePublishedEvent;
use Contexts\ArticlePublishing\Domain\Gateway\AuthorGateway;
use Contexts\ArticlePublishing\Domain\Gateway\AuthorizationGateway;
use Contexts\ArticlePublishing\Domain\Gateway\CategoryGateway;
use Contexts\ArticlePublishing\Domain\Gateway\ViewerGateway;
use Contexts\ArticlePublishing\Domain\Repositories\ArticleRepository;
use Contexts\ArticlePublishing\Infrastructure\Adapters\AuthorAdapter;
use Contexts\ArticlePublishing\Infrastructure\Adapters\AuthorizationAdapter;
use Contexts\ArticlePublishing\Infrastructure\Adapters\CategoryAdapter;
use Contexts\ArticlePublishing\Infrastructure\Adapters\ViewerAdapter;
use Contexts\ArticlePublishing\Infrastructure\EventListeners\ConsoleOutputListener;
use Contexts\ArticlePublishing\Infrastructure\Persistence\ArticlePersistence;
use Contexts\CategoryManagement\Application\Coordinators\CategoryManagementCoordinator;
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
        Event::listen(
            ArticlePublishedEvent::class,
            ConsoleOutputListener::class
        );

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

        $this->app->bind(ArticleRepository::class, ArticlePersistence::class);
        $this->app->bind(CategoryGateway::class, function ($app) {
            return new CategoryAdapter($app->make(CategoryManagementCoordinator::class));
        });
        $this->app->bind(AuthorizationGateway::class, AuthorizationAdapter::class);
        $this->app->bind(ViewerGateway::class, ViewerAdapter::class);
        $this->app->bind(AuthorGateway::class, AuthorAdapter::class);
    }

    public function provides(): array
    {
        return [];
    }
}
