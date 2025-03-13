<?php

declare(strict_types=1);

use App\Exceptions\SysException;
use Contexts\Shared\ValueObjects\ViewerRole;
use Contexts\Shared\ValueObjects\ViewerRoleCollection;

beforeEach(function () {
    $this->readerRole = new ViewerRole(1, 'reader');
    $this->editorRole = new ViewerRole(2, 'editor');
    $this->adminRole = new ViewerRole(3, 'admin');
});

it('can be instantiated with an empty array', function () {
    $collection = new ViewerRoleCollection;

    expect($collection)->toBeInstanceOf(ViewerRoleCollection::class);
});

it('can be instantiated with valid roles', function () {
    $collection = new ViewerRoleCollection([$this->readerRole, $this->editorRole]);

    expect($collection)->toBeInstanceOf(ViewerRoleCollection::class);
});

it('throws exception when instantiated with invalid roles', function () {
    expect(fn () => new ViewerRoleCollection(['not-a-role']))
        ->toThrow(SysException::class, 'Invalid role');
});

it('can create collection from plain array', function () {
    $plainArray = [
        ['id' => 1, 'label' => 'reader'],
        ['id' => 2, 'label' => 'editor'],
    ];

    $collection = ViewerRoleCollection::fromPlainArray($plainArray);

    expect($collection)->toBeInstanceOf(ViewerRoleCollection::class)
        ->and($collection->isReader())->toBeTrue()
        ->and($collection->isEditor())->toBeTrue()
        ->and($collection->isAdmin())->toBeFalse();
});

it('correctly identifies reader role', function () {
    $withReader = new ViewerRoleCollection([$this->readerRole]);
    $withoutReader = new ViewerRoleCollection([$this->editorRole, $this->adminRole]);

    expect($withReader->isReader())->toBeTrue()
        ->and($withoutReader->isReader())->toBeFalse();
});

it('correctly identifies editor role', function () {
    $withEditor = new ViewerRoleCollection([$this->editorRole]);
    $withoutEditor = new ViewerRoleCollection([$this->readerRole, $this->adminRole]);

    expect($withEditor->isEditor())->toBeTrue()
        ->and($withoutEditor->isEditor())->toBeFalse();
});

it('correctly identifies admin role', function () {
    $withAdmin = new ViewerRoleCollection([$this->adminRole]);
    $withoutAdmin = new ViewerRoleCollection([$this->readerRole, $this->editorRole]);

    expect($withAdmin->isAdmin())->toBeTrue()
        ->and($withoutAdmin->isAdmin())->toBeFalse();
});

it('can map over the roles', function () {
    $collection = new ViewerRoleCollection([$this->readerRole, $this->editorRole]);

    $result = $collection->map(fn (ViewerRole $role) => $role->getLabel());

    expect($result)->toBeInstanceOf(Illuminate\Support\Collection::class)
        ->and($result->toArray())->toBe(['reader', 'editor']);
});
