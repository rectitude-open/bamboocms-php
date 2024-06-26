<?php

declare(strict_types=1);

return [
    App\Providers\AppServiceProvider::class,
    \Modules\Administrator\Infrastructure\ServiceProvider::class,
    \Modules\AdministratorRole\Infrastructure\ServiceProvider::class,
];
