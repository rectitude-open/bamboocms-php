<?php

declare(strict_types=1);

return [
    App\Providers\AppServiceProvider::class,
    Contexts\ArticlePublishing\Infrastructure\ServiceProvider::class,
    Contexts\CategoryManagement\Infrastructure\ServiceProvider::class,
];
