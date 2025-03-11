<?php

declare(strict_types=1);

use Contexts\Authorization\Domain\Policies\RolePolicy;

return [
    'context_default' => [
        'handler' => RolePolicy::class,
        'rules' => [
            'roles' => ['editor', 'admin'],
        ],
    ],

    'actions' => [
        'publish' => [
            'handler' => RolePolicy::class,
            'rules' => [
                'roles' => ['editor', 'admin'],
            ],
        ],
    ],
];
