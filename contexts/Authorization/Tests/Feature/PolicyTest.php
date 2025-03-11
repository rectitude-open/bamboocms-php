<?php

declare(strict_types=1);

use Contexts\Authorization\Domain\Policies\RolePolicy;
use Contexts\Authorization\Domain\Services\PolicyFactory;
use Contexts\Authorization\Domain\UserIdentity\Models\Email;
use Contexts\Authorization\Domain\UserIdentity\Models\RoleIdCollection;
use Contexts\Authorization\Domain\UserIdentity\Models\UserIdentity;
use Contexts\Authorization\Domain\UserIdentity\Models\UserId;
use Contexts\Authorization\Domain\Role\Models\Role;
use Contexts\Authorization\Domain\Role\Models\RoleId;
use Contexts\Authorization\Infrastructure\Repositories\RoleRepository;
use Contexts\Authorization\Infrastructure\Repositories\UserRepository;
use Contexts\Authorization\Domain\UserIdentity\Models\Password;
use Illuminate\Support\Facades\Config;

function setTestConfig()
{
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
}

it('can get default policy handler', function () {
    setTestConfig();

    $policy = app(PolicyFactory::class)
        ->forContext('article_publishing')
        ->action('publish');

    expect($policy)->toBeInstanceOf(RolePolicy::class);
});

it('can evaluate user against policy', function () {
    setTestConfig();

    $email = new Email('test@example.com');
    $password = Password::createFromPlainText('password123');
    $user = UserIdentity::create(UserId::null(), $email, $password, 'My User');
    $userRepository = new UserRepository();
    $user = $userRepository->create($user);

    $role = Role::create(RoleId::null(), 'admin');
    $roleRepository = new RoleRepository();
    $role = $roleRepository->create($role);

    $user->syncRoles(new RoleIdCollection([$role->getId()]));
    $user = $userRepository->update($user);

    $policy = app(PolicyFactory::class)
            ->forContext('article_publishing')
            ->action('publish');

    expect($policy->evaluate($user))->toBeTrue();
});
