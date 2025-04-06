<?php

declare(strict_types=1);
use Contexts\Authorization\Domain\Policies\RolePolicy;
use Contexts\Authorization\Domain\Role\Models\RoleStatus;
use Contexts\Authorization\Infrastructure\Records\RoleRecord;

beforeEach(function () {
    Config::set('policies.authorization', [
        'context_default' => [
            'handler' => RolePolicy::class,
            'rules' => [
                'roles' => ['admin'],
            ],
        ],
    ]);
    $this->loginAsAdmin();
});

it('can create active roles via api', function () {
    $response = $this->postJson('roles', [
        'label' => 'My Role',
        'status' => 'active',
    ]);

    $response->assertStatus(201);
});

it('can get a role via api', function () {
    $response = $this->postJson('roles', [
        'label' => 'My Role',
        'status' => 'active',
    ]);

    $response->assertStatus(201);

    $id = $response->json('data.id');

    $response = $this->get("roles/{$id}");

    $response->assertStatus(200);
    $response->assertJson([
        'data' => [
            'id' => $id,
            'label' => 'My Role',
            'status' => 'active',
        ],
    ]);
});

it('can not get a role that does not exist via api', function () {
    $response = $this->get('roles/1');

    $response->assertStatus(404);
});

it('can get a list of roles via api', function () {
    $response = $this->postJson('roles', [
        'label' => 'My Role',
        'status' => 'active',
    ]);

    $response->assertStatus(201);

    $response = $this->get('roles');

    $response->assertStatus(200);
});

it('can get a list of roles with sorting via api', function () {
    $initialCount = RoleRecord::count();

    RoleRecord::factory(3)->create();

    $response = $this->get('roles?sorting=[{"id":"id","desc":false}]');

    $response->assertStatus(200);
    $response->assertJsonCount(3 + $initialCount, 'data');

    $responseIds = collect($response->json('data'))->pluck('id')->all();
    $sortedIds = collect($responseIds)->sort()->values()->all();
    expect($responseIds)->toBe($sortedIds);
});

it('can search for roles via api', function () {
    RoleRecord::factory()->create([
        'label' => 'My Role',
        'status' => RoleRecord::mapStatusToRecord(RoleStatus::active()),
    ]);
    RoleRecord::factory()->create([
        'label' => 'Other Role',
        'status' => RoleRecord::mapStatusToRecord(RoleStatus::active()),
    ]);

    $response = $this->get('roles?label=My');

    $response->assertStatus(200);
    $response->assertJsonCount(1, 'data');
    $response->assertJson([
        'data' => [
            [
                'label' => 'My Role',
            ],
        ],
    ]);

    $response = $this->get('roles?filters=[{"id":"label","value":"Other"}]');
    $response->assertStatus(200);
    $response->assertJsonCount(1, 'data');
    $response->assertJson([
        'data' => [
            [
                'label' => 'Other Role',
            ],
        ],
    ]);
});

it('can search for roles with created_at via api', function () {
    RoleRecord::factory(3)->create([
        'status' => RoleRecord::mapStatusToRecord(RoleStatus::active()),
        'created_at' => now()->subDays(7),
    ]);
    RoleRecord::factory()->create([
        'label' => 'Role1',
        'status' => RoleRecord::mapStatusToRecord(RoleStatus::active()),
        'created_at' => now()->subDays(5),
    ]);
    RoleRecord::factory()->create([
        'label' => 'Role2',
        'status' => RoleRecord::mapStatusToRecord(RoleStatus::active()),
        'created_at' => now()->subDays(2),
    ]);

    RoleRecord::factory(3)->create([
        'status' => RoleRecord::mapStatusToRecord(RoleStatus::active()),
        'created_at' => now()->subDays(1),
    ]);

    $response = $this->get('roles?filters=[{"id":"created_at","value":["'.now()->subDays(5)->format('Y-m-d').'","'.now()->subDays(1)->format('Y-m-d').'"]}]');

    $response->assertStatus(200);
    $response->assertJsonCount(2, 'data');
    $response->assertJson([
        'data' => [
            [
                'label' => 'Role2',
            ],
            [
                'label' => 'Role1',
            ],
        ],
    ]);

    $response = $this->get('roles?created_at[]='.now()->subDays(5)->format('Y-m-d').'&created_at[]='.now()->subDays(1)->format('Y-m-d'));

    $response->assertStatus(200);
    $response->assertJsonCount(2, 'data');
    $response->assertJson([
        'data' => [
            [
                'label' => 'Role2',
            ],
            [
                'label' => 'Role1',
            ],
        ],
    ]);
});

it('can update a role via api', function () {
    $response = $this->postJson('roles', [
        'label' => 'My Role',
        'status' => 'active',
    ]);

    $response->assertStatus(201);

    $id = $response->json('data.id');

    $response = $this->putJson("roles/{$id}", [
        'label' => 'My Updated Role',
    ]);

    $response->assertStatus(200);
    $response->assertJson([
        'data' => [
            'id' => $id,
            'label' => 'My Updated Role',
            'status' => 'active',
        ],
    ]);
});

it('can suspend a role via api', function () {
    $response = $this->postJson('roles', [
        'label' => 'My Role',
        'status' => 'active',
    ]);

    $response->assertStatus(201);

    $id = $response->json('data.id');

    $response = $this->putJson("roles/{$id}/suspend");

    $response->assertStatus(200);
});

it('can delete a role via api', function () {
    $response = $this->postJson('roles', [
        'label' => 'My Role',
        'status' => 'active',
    ]);

    $response->assertStatus(201);

    $id = $response->json('data.id');

    $response = $this->delete("roles/{$id}");

    $response->assertStatus(200);

    $response = $this->get("roles/{$id}");

    $response->assertStatus(404);
});
