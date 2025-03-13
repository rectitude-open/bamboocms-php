<?php

declare(strict_types=1);

use Contexts\Shared\ValueObjects\ViewerRole;

beforeEach(function () {
    $this->readerRole = new ViewerRole(1, 'reader');
    $this->editorRole = new ViewerRole(2, 'editor');
    $this->adminRole = new ViewerRole(3, 'admin');
});

it('can get id', function () {
    expect($this->readerRole->getId())->toBe(1);
    expect($this->editorRole->getId())->toBe(2);
    expect($this->adminRole->getId())->toBe(3);
});

it('can get label', function () {
    expect($this->readerRole->getLabel())->toBe('reader');
    expect($this->editorRole->getLabel())->toBe('editor');
    expect($this->adminRole->getLabel())->toBe('admin');
});

it('can identify reader role', function () {
    expect($this->readerRole->isReader())->toBeTrue();
    expect($this->editorRole->isReader())->toBeFalse();
    expect($this->adminRole->isReader())->toBeFalse();
});

it('can identify editor role', function () {
    expect($this->readerRole->isEditor())->toBeFalse();
    expect($this->editorRole->isEditor())->toBeTrue();
    expect($this->adminRole->isEditor())->toBeFalse();
});

it('can identify admin role', function () {
    expect($this->readerRole->isAdmin())->toBeFalse();
    expect($this->editorRole->isAdmin())->toBeFalse();
    expect($this->adminRole->isAdmin())->toBeTrue();
});

it('can compare equality with another role', function () {
    $sameAsReader = new ViewerRole(1, 'reader');
    $sameIdDifferentLabel = new ViewerRole(1, 'something_else');

    expect($this->readerRole->equals($sameAsReader))->toBeTrue();
    expect($this->readerRole->equals($sameIdDifferentLabel))->toBeTrue();
    expect($this->readerRole->equals($this->editorRole))->toBeFalse();
});
