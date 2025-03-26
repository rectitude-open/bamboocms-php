<?php

declare(strict_types=1);
use Carbon\CarbonImmutable;
use Contexts\Authorization\Domain\Factories\RoleFactory;
use Contexts\Authorization\Domain\Role\Exceptions\RoleNotFoundException;
use Contexts\Authorization\Domain\Role\Models\Role;
use Contexts\Authorization\Domain\Role\Models\RoleId;
use Contexts\Authorization\Domain\Role\Models\RoleStatus;
use Contexts\Authorization\Domain\Services\RoleLabelUniquenessService;
use Contexts\Authorization\Infrastructure\Persistence\RolePersistence;
use Contexts\Authorization\Infrastructure\Records\RoleRecord;

beforeEach(function () {
    $this->roleLabelUniquenessService = mock(RoleLabelUniquenessService::class);
    $this->roleLabelUniquenessService->shouldReceive('ensureUnique')->andReturn(true);
    $this->roleFactory = new RoleFactory($this->roleLabelUniquenessService);
});

it('can persist role data correctly', function () {
    $role = $this->roleFactory->create(RoleId::null(), 'My Role');
    $rolePersistence = new RolePersistence;

    $rolePersistence->create($role);

    $this->assertDatabaseHas('roles', [
        'label' => 'My Role',
        'status' => RoleRecord::mapStatusToRecord(RoleStatus::active()),
    ]);
});

it('can retrieve an role by ID', function () {
    // Create a test role in the database
    $createdRole = $this->roleFactory->create(RoleId::null(), 'Test Role');
    $rolePersistence = new RolePersistence;
    $savedRole = $rolePersistence->create($createdRole);

    // Retrieve the role using getById
    $retrievedRole = $rolePersistence->getById($savedRole->id);

    // Assert the retrieved role matches the created one
    expect($retrievedRole->getId()->getValue())->toBe($savedRole->getId()->getValue());
    expect($retrievedRole->getLabel())->toBe('Test Role');
    expect($retrievedRole->getStatus()->equals(RoleStatus::active()))->toBeTrue();
});

it('can retrieve multiple roles by IDs', function () {
    // Create multiple test roles in the database
    $rolePersistence = new RolePersistence;

    $role1 = $this->roleFactory->create(RoleId::null(), 'Role 1');
    $role2 = $this->roleFactory->create(RoleId::null(), 'Role 2');
    $role3 = $this->roleFactory->create(RoleId::null(), 'Role 3');

    $role1 = $rolePersistence->create($role1);
    $role2 = $rolePersistence->create($role2);
    $role3 = $rolePersistence->create($role3);

    // Retrieve the roles using getByIds
    $retrievedRoles = $rolePersistence->getByIds([
        $role1->id->getValue(),
        $role2->id->getValue(),
        $role3->id->getValue(),
    ]);

    // Assert the retrieved roles match the created ones
    expect($retrievedRoles->count())->toBe(3);
    expect($retrievedRoles[0]->getId()->getValue())->toBe($role1->getId()->getValue());
    expect($retrievedRoles[1]->getId()->getValue())->toBe($role2->getId()->getValue());
    expect($retrievedRoles[2]->getId()->getValue())->toBe($role3->getId()->getValue());
});

it('throws an exception when retrieving a non-existent role', function () {
    $rolePersistence = new RolePersistence;

    // Attempt to retrieve a non-existent role
    $rolePersistence->getById(RoleId::fromInt(999));
})->throws(RoleNotFoundException::class);

it('can update an role', function () {
    // Create a test role in the database
    $createdRole = $this->roleFactory->create(RoleId::null(), 'Original Label');
    $rolePersistence = new RolePersistence;
    $savedRole = $rolePersistence->create($createdRole);

    // Create an updated version of the role
    $updatedRole = $this->roleFactory->create(
        $savedRole->id,
        'Updated Label',
    );

    // Update the role
    $result = $rolePersistence->update($updatedRole);

    // Verify database was updated
    $this->assertDatabaseHas('roles', [
        'id' => $savedRole->getId()->getValue(),
        'label' => 'Updated Label',
        'status' => RoleRecord::mapStatusToRecord(RoleStatus::active()),
    ]);

    // Verify returned object reflects updates
    expect($result->getLabel())->toBe('Updated Label');
    expect($result->getStatus()->equals(RoleStatus::active()))->toBeTrue();
});

it('throws an exception when updating a non-existent role', function () {
    $rolePersistence = new RolePersistence;

    // Attempt to update a non-existent role
    $rolePersistence->update($this->roleFactory->create(RoleId::fromInt(999), 'Updated Label'));
})->throws(RoleNotFoundException::class);

it('can paginate roles', function () {
    // Create multiple test roles
    $rolePersistence = new RolePersistence;

    // Create 5 roles
    for ($i = 1; $i <= 5; $i++) {
        $role = $this->roleFactory->create(
            RoleId::null(),
            "Role $i",
            new CarbonImmutable
        );
        $rolePersistence->create($role);
    }

    // Test pagination with default criteria
    $result = $rolePersistence->paginate(1, 2, []);

    // Assert pagination metadata
    expect($result->total())->toBe(5);
    expect($result->perPage())->toBe(2);
    expect($result->currentPage())->toBe(1);
    expect(count($result->items()))->toBe(2); // 2 items on the first page

    // Test second page
    $result2 = $rolePersistence->paginate(2, 2, []);
    expect($result2->currentPage())->toBe(2);
    expect(count($result2->items()))->toBe(2); // 2 items on the second page

    // Test last page
    $result3 = $rolePersistence->paginate(3, 2, []);
    expect($result3->currentPage())->toBe(3);
    expect(count($result3->items()))->toBe(1); // 1 item on the last page
});

it('can filter roles with search criteria', function () {
    $rolePersistence = new RolePersistence;

    // Create roles with specific labels
    $role1 = $this->roleFactory->create(
        RoleId::null(),
        'Laravel Role',
        new CarbonImmutable
    );
    $rolePersistence->create($role1);

    $role2 = $this->roleFactory->create(
        RoleId::null(),
        'PHP Tutorial',
        new CarbonImmutable
    );
    $role2->suspend();
    $rolePersistence->create($role2);

    $role3 = $this->roleFactory->create(
        RoleId::null(),
        'Laravel Tips',
        new CarbonImmutable
    );
    $role3->suspend();
    $rolePersistence->create($role3);

    // Test search by label criteria
    $result = $rolePersistence->paginate(1, 10, ['label' => 'Laravel']);
    expect($result->total())->toBe(2); // Should find the two Laravel roles

    // Test search with status criteria
    $result = $rolePersistence->paginate(1, 10, [
        'label' => 'Laravel',
        'status' => RoleRecord::mapStatusToRecord(RoleStatus::active()),
    ]);
    expect($result->total())->toBe(1); // Should only find the active Laravel role

    // Test with no matching criteria
    $result = $rolePersistence->paginate(1, 10, ['label' => 'Django']);
    expect($result->total())->toBe(0);

    // Test search by created_at_range criteria
    $role4 = $this->roleFactory->create(
        RoleId::null(),
        'Past Role',
        new CarbonImmutable('2021-01-01')
    );
    $rolePersistence->create($role4);

    $result = $rolePersistence->paginate(1, 10, [
        'created_at_range' => ['2021-01-01', '2021-01-02'],
    ]);

    expect($result->total())->toBe(1); // Should find the role created on 2021-01-01
});

it('can delete an role', function () {
    // Create a test role in the database
    $createdRole = $this->roleFactory->create(RoleId::null(), 'Test Role');
    $rolePersistence = new RolePersistence;
    $savedRole = $rolePersistence->create($createdRole);

    // Delete the role
    $rolePersistence->delete($savedRole);

    // Verify the role was deleted
    $this->assertDatabaseMissing('roles', [
        'id' => $savedRole->getId()->getValue(),
    ]);
});

it('throws an exception when deleting a non-existent role', function () {
    $rolePersistence = new RolePersistence;

    // Attempt to delete a non-existent role
    $rolePersistence->delete($this->roleFactory->create(RoleId::fromInt(999), 'Test Role'));
})->throws(RoleNotFoundException::class);

it('returns true when role exists with given label', function () {
    // Create a test role in the database with a specific label
    $createdRole = $this->roleFactory->create(RoleId::null(), 'Unique Label');
    $rolePersistence = new RolePersistence;
    $rolePersistence->create($createdRole);

    // Check if the method correctly detects the existing label
    $result = $rolePersistence->existsByLabel('Unique Label');

    expect($result)->toBeTrue();
});

it('returns false when role does not exist with given label', function () {
    $rolePersistence = new RolePersistence;

    // Test with a label that doesn't exist in the database
    $result = $rolePersistence->existsByLabel('Non-Existent Label');

    expect($result)->toBeFalse();
});

it('can retrieve roles by labels', function () {
    // Create roles with specific labels
    $rolePersistence = new RolePersistence;

    $role1 = $this->roleFactory->create(RoleId::null(), 'Admin Role');
    $role2 = $this->roleFactory->create(RoleId::null(), 'Editor Role');
    $role3 = $this->roleFactory->create(RoleId::null(), 'Viewer Role');

    $rolePersistence->create($role1);
    $rolePersistence->create($role2);
    $rolePersistence->create($role3);

    // Retrieve the roles by labels
    $retrievedRoles = $rolePersistence->getByLabels(['Admin Role', 'Editor Role']);
    // Assert correct records were retrieved
    $roleLabelArray = $retrievedRoles->map(function ($role) {
        return $role->getLabel();
    })->toArray();
    expect($roleLabelArray)->toHaveCount(2);
    expect($roleLabelArray)->toContain('Admin Role', 'Editor Role');
    expect($roleLabelArray)->not->toContain('Viewer Role');
});

it('returns empty collection when no roles match the given labels', function () {
    $rolePersistence = new RolePersistence;

    // Retrieve with non-existent labels
    $retrievedRoles = $rolePersistence->getByLabels(['Non-Existent Label']);

    expect($retrievedRoles)->toBeInstanceOf(\Illuminate\Support\Collection::class);
    expect($retrievedRoles)->toBeEmpty();
});
