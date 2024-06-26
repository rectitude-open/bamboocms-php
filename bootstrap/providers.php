<?php

declare(strict_types=1);

return [
    App\Providers\AppServiceProvider::class,
    \Modules\TemplateModule\Infrastructure\ServiceProvider::class,
    \Modules\Administrator\Infrastructure\ServiceProvider::class,
];
