<?php

declare(strict_types=1);

use App\Exceptions\BizException;
use Contexts\Authorization\Domain\Role\Models\RoleId;
use Contexts\Authorization\Domain\UserIdentity\Models\RoleIdCollection;

beforeEach(function () {
    $this->roleId1 = RoleId::fromInt(1);
    $this->roleId2 = RoleId::fromInt(2);
    $this->roleId3 = RoleId::fromInt(3);
});

it('can be created with empty array', function () {
    $collection = new RoleIdCollection([]);

    expect($collection->count())->toBe(0);
});

it('can be created with valid role ids', function () {
    $collection = new RoleIdCollection([$this->roleId1, $this->roleId2]);

    expect($collection->count())->toBe(2);
});

it('throws exception when created with invalid role ids', function () {
    expect(fn () => new RoleIdCollection(['invalid']))
        ->toThrow(BizException::class, 'Invalid role id');
});

it('can check if it contains a role id', function () {
    $collection = new RoleIdCollection([$this->roleId1, $this->roleId2]);

    expect($collection->contains($this->roleId1))->toBeTrue();
    expect($collection->contains($this->roleId3))->toBeFalse();
});

it('can return difference between two collections', function () {
    $collection1 = new RoleIdCollection([$this->roleId1, $this->roleId2]);
    $collection2 = new RoleIdCollection([$this->roleId2]);

    $diff = $collection1->diff($collection2);

    expect($diff->count())->toBe(1);
    expect($diff->contains($this->roleId1))->toBeTrue();
    expect($diff->contains($this->roleId2))->toBeFalse();
});

it('can return array of role ids', function () {
    $collection = new RoleIdCollection([$this->roleId1, $this->roleId2]);

    expect($collection->getIdsArray())->toBe([1, 2]);
});

it('can map over collection items', function () {
    $collection = new RoleIdCollection([$this->roleId1, $this->roleId2]);

    $result = $collection->map(fn (RoleId $id) => $id->getValue() * 10);

    expect($result->toArray())->toBe([10, 20]);
});
