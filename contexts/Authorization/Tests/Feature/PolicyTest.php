<?php

declare(strict_types=1);

use Contexts\Authorization\Domain\Factories\RoleFactory;
use Contexts\Authorization\Domain\Policies\RolePolicy;
use Contexts\Authorization\Domain\Role\Models\RoleId;
use Contexts\Authorization\Domain\Services\PolicyFactory;
use Contexts\Authorization\Domain\Services\RoleLabelUniquenessService;
use Contexts\Authorization\Domain\UserIdentity\Models\RoleIdCollection;
use Contexts\Authorization\Infrastructure\Persistence\RolePersistence;
use Contexts\Authorization\Infrastructure\Persistence\UserPersistence;
use Contexts\Authorization\Infrastructure\Records\UserRecord;
use Illuminate\Support\Facades\Config;

beforeEach(function () {
    Config::set('policies.article_publishing', [
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
                    'roles' => ['admin'],
                ],
            ],
        ],
    ]);

    $this->roleLabelUniquenessService = mock(RoleLabelUniquenessService::class);
    $this->roleLabelUniquenessService->shouldReceive('ensureUnique')->andReturn(true);
    $this->roleFactory = new RoleFactory($this->roleLabelUniquenessService);
    $this->loginAsUser();
});

it('can get default policy handler', function () {
    $policy = app(PolicyFactory::class)
        ->forContext('article_publishing')
        ->action('publish');

    expect($policy)->toBeInstanceOf(RolePolicy::class);
});

it('can evaluate user against policy', function () {
    $userPersistence = new UserPersistence;

    $userRecord = UserRecord::factory()->create();

    $this->actingAs($userRecord);

    $role = $this->roleFactory->create(RoleId::null(), 'admin');
    $rolePersistence = new RolePersistence;
    $role = $rolePersistence->create($role);

    $user = $userRecord->toDomain();
    $user->syncRoles(new RoleIdCollection([$role->getId()]));
    $user = $userPersistence->update($user);

    $policy = app(PolicyFactory::class)
        ->forContext('article_publishing')
        ->action('publish');

    expect($policy->evaluate($user))->toBeTrue();
});
