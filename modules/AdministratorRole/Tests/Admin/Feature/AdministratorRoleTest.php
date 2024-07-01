<?php

declare(strict_types=1);
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\AdministratorRole\Domain\Models\AdministratorRole;

uses(RefreshDatabase::class);

require_once __DIR__.'/AdministratorRoleDataset.php';

beforeEach(function () {
    $this->routes = [
        'index' => 'AdministratorRole.Admin.AdministratorRole.index',
        'store' => 'AdministratorRole.Admin.AdministratorRole.store',
    ];
    $this->modelClass = AdministratorRole::class;
    $this->tableName = (new $this->modelClass)->getTable();
});

afterEach(function () {});

it('can display list', function ($data, $expected, $expectedCount = 10, $factory = null) {
    ($factory ?? fn () => null)();

    // dump($this->getJson($this->getRoute('index', $data)));
    $this->getJson($this->getRoute('index', $data))
        ->assertJsonCount($expectedCount, 'data')
        ->assertJson($expected)
        ->assertStatus(200);
})->with('index');

it('cannot display list with invalid data', function ($data, $expected, $expectedCount = 0, $factory = null) {
    ($factory ?? fn () => null)();

    $this->getJson($this->getRoute('index', $data))
        ->assertJsonValidationErrors($expected)
        ->assertStatus(422);
    $this->assertDatabaseCount($this->tableName, $expectedCount);
})->with('indexInvalidData');

it('can store', function ($data, $expected, $expectedCount = 1, $factory = null) {
    ($factory ?? fn () => null)();

    $this->postJson($this->getRoute('store'), $data)
        ->assertJson([
            'data' => $expected,
            'message' => 'Success! The record has been added.',
        ])
        ->assertStatus(201);
    $this->assertDatabaseHas($this->tableName, $expected);
    $this->assertDatabaseCount($this->tableName, $expectedCount);
})->with('store');

it('cannot store with invalid data', function ($data, $expected, $expectedCount = 0, $factory = null) {
    ($factory ?? fn () => null)();

    $this->postJson($this->getRoute('store'), $data)
        ->assertJsonValidationErrors($expected)
        ->assertStatus(422);
    $this->assertDatabaseCount($this->tableName, $expectedCount);
})->with('storeInvalidData');
