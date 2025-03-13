<?php

declare(strict_types=1);

use Contexts\Shared\ValueObjects\Viewer;
use Contexts\Shared\ValueObjects\ViewerId;
use Contexts\Shared\ValueObjects\ViewerRole;
use Contexts\Shared\ValueObjects\ViewerRoleCollection;

beforeEach(function () {
    // Create base test data
    $this->viewerId = ViewerId::fromInt(1);
    $this->displayName = 'John Doe';
    $this->email = 'john@example.com';

    // Create collection with all roles
    $this->allRoles = [
        new ViewerRole(1, 'reader'),
        new ViewerRole(2, 'editor'),
        new ViewerRole(3, 'admin'),
    ];
    $this->fullRoleCollection = new ViewerRoleCollection($this->allRoles);

    // Create collection with reader role only
    $this->readerOnlyRoles = [new ViewerRole(1, 'reader')];
    $this->readerRoleCollection = new ViewerRoleCollection($this->readerOnlyRoles);

    // Create collection with editor role only
    $this->editorOnlyRoles = [new ViewerRole(2, 'editor')];
    $this->editorRoleCollection = new ViewerRoleCollection($this->editorOnlyRoles);

    // Create collection with admin role only
    $this->adminOnlyRoles = [new ViewerRole(3, 'admin')];
    $this->adminRoleCollection = new ViewerRoleCollection($this->adminOnlyRoles);

    // Create empty role collection
    $this->emptyRoleCollection = new ViewerRoleCollection([]);

    // Create viewer instance with all roles
    $this->fullRoleViewer = new Viewer(
        $this->viewerId,
        $this->displayName,
        $this->email,
        $this->fullRoleCollection
    );
});

it('can be created with proper values', function () {
    expect($this->fullRoleViewer)->toBeInstanceOf(Viewer::class)
        ->and($this->fullRoleViewer->getId())->toBe($this->viewerId)
        ->and($this->fullRoleViewer->getDisplayName())->toBe($this->displayName)
        ->and($this->fullRoleViewer->getEmail())->toBe($this->email)
        ->and($this->fullRoleViewer->getRoles())->toBe($this->fullRoleCollection);
});

it('determines if viewer is a reader correctly', function () {
    // Viewer with reader role
    $readerViewer = new Viewer(
        ViewerId::fromInt(2),
        'Reader User',
        'reader@example.com',
        $this->readerRoleCollection
    );

    // Viewer without reader role
    $nonReaderViewer = new Viewer(
        ViewerId::fromInt(3),
        'Non Reader',
        'nonreader@example.com',
        $this->adminRoleCollection
    );

    expect($this->fullRoleViewer->isReader())->toBeTrue()
        ->and($readerViewer->isReader())->toBeTrue()
        ->and($nonReaderViewer->isReader())->toBeFalse();
});

it('determines if viewer is an editor correctly', function () {
    // Viewer with editor role
    $editorViewer = new Viewer(
        ViewerId::fromInt(2),
        'Editor User',
        'editor@example.com',
        $this->editorRoleCollection
    );

    // Viewer without editor role
    $nonEditorViewer = new Viewer(
        ViewerId::fromInt(3),
        'Non Editor',
        'noneditor@example.com',
        $this->readerRoleCollection
    );

    expect($this->fullRoleViewer->isEditor())->toBeTrue()
        ->and($editorViewer->isEditor())->toBeTrue()
        ->and($nonEditorViewer->isEditor())->toBeFalse();
});

it('determines if viewer is an admin correctly', function () {
    // Viewer with admin role
    $adminViewer = new Viewer(
        ViewerId::fromInt(2),
        'Admin User',
        'admin@example.com',
        $this->adminRoleCollection
    );

    // Viewer without admin role
    $nonAdminViewer = new Viewer(
        ViewerId::fromInt(3),
        'Non Admin',
        'nonadmin@example.com',
        $this->editorRoleCollection
    );

    expect($this->fullRoleViewer->isAdmin())->toBeTrue()
        ->and($adminViewer->isAdmin())->toBeTrue()
        ->and($nonAdminViewer->isAdmin())->toBeFalse();
});

it('correctly identifies equality with another viewer with the same ID', function () {
    // Create viewer with the same ID but different properties
    $sameIdViewer = new Viewer(
        ViewerId::fromInt(1), // Same ID
        'Different Name',
        'different@example.com',
        $this->emptyRoleCollection // Different role collection
    );

    expect($this->fullRoleViewer->equals($sameIdViewer))->toBeTrue();
});

it('correctly identifies inequality with another viewer with a different ID', function () {
    // Create viewer with different ID
    $differentIdViewer = new Viewer(
        ViewerId::fromInt(999), // Different ID
        $this->displayName, // Same name
        $this->email, // Same email
        $this->fullRoleCollection // Same role collection
    );

    expect($this->fullRoleViewer->equals($differentIdViewer))->toBeFalse();
});

it('handles viewers with no roles correctly', function () {
    $noRoleViewer = new Viewer(
        ViewerId::fromInt(5),
        'No Role User',
        'norole@example.com',
        $this->emptyRoleCollection
    );

    expect($noRoleViewer->isReader())->toBeFalse()
        ->and($noRoleViewer->isEditor())->toBeFalse()
        ->and($noRoleViewer->isAdmin())->toBeFalse();
});
