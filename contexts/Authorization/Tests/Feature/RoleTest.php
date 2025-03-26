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

it('can get a role', function () {
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

it('can not get a role that does not exist', function () {
    $response = $this->get('roles/1');

    $response->assertStatus(404);
});

it('can get a list of roles', function () {
    $response = $this->postJson('roles', [
        'label' => 'My Role',
        'status' => 'active',
    ]);

    $response->assertStatus(201);

    $response = $this->get('roles');

    $response->assertStatus(200);
});

it('can get a list of roles with sorting', function () {
    $initialCount = RoleRecord::count();

    RoleRecord::factory(3)->create();

    $response = $this->get('roles?sorting=[{"id":"id","desc":false}]');

    $response->assertStatus(200);
    $response->assertJsonCount(3 + $initialCount, 'data');

    $responseIds = collect($response->json('data'))->pluck('id')->all();
    $sortedIds = collect($responseIds)->sort()->values()->all();
    expect($responseIds)->toBe($sortedIds);
});

it('can search for roles', function () {
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

it('can update a role', function () {
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

it('can suspend a role', function () {
    $response = $this->postJson('roles', [
        'label' => 'My Role',
        'status' => 'active',
    ]);

    $response->assertStatus(201);

    $id = $response->json('data.id');

    $response = $this->putJson("roles/{$id}/suspend");

    $response->assertStatus(200);
});

it('can delete a role', function () {
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
