<?php

namespace Foo\Bar;

use Illuminate\Support\ServiceProvider;

class BarServiceProvider extends ServiceProvider
{
    /**
     * Boot the application events.
     */
    public function boot(): void {}

    /**
     * Register the service provider.
     */
    public function register(): void
    {
        $this->app->register(RouteServiceProvider::class);
    }

    public function provides(): array
    {
        return [];
    }
}
