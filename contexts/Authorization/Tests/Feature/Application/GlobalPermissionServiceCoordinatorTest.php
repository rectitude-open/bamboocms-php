<?php

declare(strict_types=1);

use Contexts\Authorization\Application\Coordinators\GlobalPermissionServiceCoordinator;
use Contexts\Authorization\Contracts\V1\Services\GlobalPermissionService;
use Contexts\Authorization\Domain\Policies\RolePolicy;
use Contexts\Authorization\Domain\Role\Models\Role;
use Contexts\Authorization\Domain\Role\Models\RoleId;
use Contexts\Authorization\Domain\UserIdentity\Models\RoleIdCollection;
use Contexts\Authorization\Infrastructure\Records\UserRecord;
use Contexts\Authorization\Infrastructure\Repositories\RoleRepository;
use Contexts\Authorization\Infrastructure\Repositories\UserRepository;
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
            'edit' => [
                'handler' => RolePolicy::class,
                'rules' => [
                    'roles' => ['editor', 'admin'],
                ],
            ],
        ],
    ]);
});

it('can be instantiated through container', function () {
    $service = app(GlobalPermissionService::class);

    expect($service)->toBeInstanceOf(GlobalPermissionServiceCoordinator::class);
});

it('can check permission for admin user', function () {
    // Setup repositories
    $userRepository = new UserRepository;
    $roleRepository = new RoleRepository;

    // Create admin user
    $userRecord = UserRecord::factory()->create();
    $this->actingAs($userRecord);

    // Create admin role
    $adminRole = Role::create(RoleId::null(), 'admin');
    $adminRole = $roleRepository->create($adminRole);

    // Assign admin role to user
    $user = $userRecord->toDomain();
    $user->syncRoles(new RoleIdCollection([$adminRole->getId()]));
    $userRepository->update($user);

    // Check if the admin can publish
    $permissionService = app(GlobalPermissionService::class);
    expect($permissionService->checkPermission('article_publishing', 'publish'))->toBeTrue();
});

it('denies permission for users without required roles', function () {
    // Setup repositories
    $userRepository = new UserRepository;
    $roleRepository = new RoleRepository;

    // Create regular user
    $userRecord = UserRecord::factory()->create();
    $this->actingAs($userRecord);

    // Create editor role (not admin)
    $editorRole = Role::create(RoleId::null(), 'editor');
    $editorRole = $roleRepository->create($editorRole);

    // Assign editor role to user
    $user = $userRecord->toDomain();
    $user->syncRoles(new RoleIdCollection([$editorRole->getId()]));
    $userRepository->update($user);

    // Editor can edit but cannot publish
    $permissionService = app(GlobalPermissionService::class);
    expect($permissionService->checkPermission('article_publishing', 'edit'))->toBeTrue();
    expect($permissionService->checkPermission('article_publishing', 'publish'))->toBeFalse();
});

it('handles non-existing permissions gracefully', function () {
    $userRecord = UserRecord::factory()->create();
    $this->actingAs($userRecord);

    $permissionService = app(GlobalPermissionService::class);

    // This should not throw exceptions but return false for non-existing contexts/actions
    expect($permissionService->checkPermission('non_existing_context', 'some_action'))->toBeFalse();
    expect($permissionService->checkPermission('article_publishing', 'non_existing_action'))->toBeFalse();
});
