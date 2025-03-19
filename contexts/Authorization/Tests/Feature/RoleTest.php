<?php

declare(strict_types=1);
use Contexts\Authorization\Domain\Role\Models\RoleStatus;
use Contexts\Authorization\Infrastructure\Records\RoleRecord;

it('can create active roles via api', function () {
    $response = $this->postJson('roles', [
        'label' => 'My Role',
        'status' => 'active',
    ]);

    $response->assertStatus(201);
});

it('can get a category', function () {
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

it('can not get a category that does not exist', function () {
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

it('can update a category', function () {
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

it('can subspend a category', function () {
    $response = $this->postJson('roles', [
        'label' => 'My Role',
        'status' => 'active',
    ]);

    $response->assertStatus(201);

    $id = $response->json('data.id');

    $response = $this->putJson("roles/{$id}/subspend");

    $response->assertStatus(200);
});

it('can delete a category', function () {
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
