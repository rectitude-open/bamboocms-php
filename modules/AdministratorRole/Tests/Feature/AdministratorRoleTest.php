<?php

declare(strict_types=1);
use Modules\AdministratorRole\Domain\Models\AdministratorRole;

require_once __DIR__.'/AdministratorRoleDataset.php';

beforeEach(function () {
    $this->routes = [
        'store' => 'AdministratorRole.AdministratorRole.Admin.store',
    ];
    $this->modelClass = AdministratorRole::class;
    $this->tableName = (new $this->modelClass)->getTable();
});

afterEach(function () {});

it('can store', function ($data, $expected, $factory = null) {
    ($factory ?? fn () => null)();

    $this->postJson($this->getRoute('store'), $data)
        ->assertJson([
            'data' => $expected,
            'message' => 'Success! The record has been added.',
        ])
        ->assertStatus(201);
    $this->assertDatabaseHas($this->tableName, $expected);
    $this->assertDatabaseCount($this->tableName, 1);
})->with('store');
