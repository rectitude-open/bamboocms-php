<?php

declare(strict_types=1);
use Carbon\CarbonImmutable;
use Contexts\Authorization\Domain\Role\Exceptions\RoleNotFoundException;
use Contexts\Authorization\Domain\Role\Models\Role;
use Contexts\Authorization\Domain\Role\Models\RoleId;
use Contexts\Authorization\Domain\Role\Models\RoleStatus;
use Contexts\Authorization\Infrastructure\Records\RoleRecord;
use Contexts\Authorization\Infrastructure\Repositories\RoleRepository;

it('can persist role data correctly', function () {
    $role = Role::create(RoleId::null(), 'My Role');
    $roleRepository = new RoleRepository;

    $roleRepository->create($role);

    $this->assertDatabaseHas('roles', [
        'label' => 'My Role',
        'status' => RoleRecord::mapStatusToRecord(RoleStatus::active()),
    ]);
});

it('can retrieve an role by ID', function () {
    // Create a test role in the database
    $createdRole = Role::create(RoleId::null(), 'Test Role');
    $roleRepository = new RoleRepository;
    $savedRole = $roleRepository->create($createdRole);

    // Retrieve the role using getById
    $retrievedRole = $roleRepository->getById($savedRole->id);

    // Assert the retrieved role matches the created one
    expect($retrievedRole->getId()->getValue())->toBe($savedRole->getId()->getValue());
    expect($retrievedRole->getLabel())->toBe('Test Role');
    expect($retrievedRole->getStatus()->equals(RoleStatus::active()))->toBeTrue();
});

it('can retrieve multiple roles by IDs', function () {
    // Create multiple test roles in the database
    $roleRepository = new RoleRepository;

    $role1 = Role::create(RoleId::null(), 'Role 1');
    $role2 = Role::create(RoleId::null(), 'Role 2');
    $role3 = Role::create(RoleId::null(), 'Role 3');

    $role1 = $roleRepository->create($role1);
    $role2 = $roleRepository->create($role2);
    $role3 = $roleRepository->create($role3);

    // Retrieve the roles using getByIds
    $retrievedRoles = $roleRepository->getByIds([
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
    $roleRepository = new RoleRepository;

    // Attempt to retrieve a non-existent role
    $roleRepository->getById(RoleId::fromInt(999));
})->throws(RoleNotFoundException::class);

it('can update an role', function () {
    // Create a test role in the database
    $createdRole = Role::create(RoleId::null(), 'Original Label');
    $roleRepository = new RoleRepository;
    $savedRole = $roleRepository->create($createdRole);

    // Create an updated version of the role
    $updatedRole = Role::create(
        $savedRole->id,
        'Updated Label',
    );

    // Update the role
    $result = $roleRepository->update($updatedRole);

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
    $roleRepository = new RoleRepository;

    // Attempt to update a non-existent role
    $roleRepository->update(Role::create(RoleId::fromInt(999), 'Updated Label'));
})->throws(RoleNotFoundException::class);

it('can paginate roles', function () {
    // Create multiple test roles
    $roleRepository = new RoleRepository;

    // Create 5 roles
    for ($i = 1; $i <= 5; $i++) {
        $role = Role::create(
            RoleId::null(),
            "Role $i",
            new CarbonImmutable
        );
        $roleRepository->create($role);
    }

    // Test pagination with default criteria
    $result = $roleRepository->paginate(1, 2, []);

    // Assert pagination metadata
    expect($result->total())->toBe(5);
    expect($result->perPage())->toBe(2);
    expect($result->currentPage())->toBe(1);
    expect(count($result->items()))->toBe(2); // 2 items on the first page

    // Test second page
    $result2 = $roleRepository->paginate(2, 2, []);
    expect($result2->currentPage())->toBe(2);
    expect(count($result2->items()))->toBe(2); // 2 items on the second page

    // Test last page
    $result3 = $roleRepository->paginate(3, 2, []);
    expect($result3->currentPage())->toBe(3);
    expect(count($result3->items()))->toBe(1); // 1 item on the last page
});

it('can filter roles with search criteria', function () {
    $roleRepository = new RoleRepository;

    // Create roles with specific labels
    $role1 = Role::create(
        RoleId::null(),
        'Laravel Role',
        new CarbonImmutable
    );
    $roleRepository->create($role1);

    $role2 = Role::create(
        RoleId::null(),
        'PHP Tutorial',
        new CarbonImmutable
    );
    $role2->subspend();
    $roleRepository->create($role2);

    $role3 = Role::create(
        RoleId::null(),
        'Laravel Tips',
        new CarbonImmutable
    );
    $role3->subspend();
    $roleRepository->create($role3);

    // Test search by label criteria
    $result = $roleRepository->paginate(1, 10, ['label' => 'Laravel']);
    expect($result->total())->toBe(2); // Should find the two Laravel roles

    // Test search with status criteria
    $result = $roleRepository->paginate(1, 10, [
        'label' => 'Laravel',
        'status' => RoleRecord::mapStatusToRecord(RoleStatus::active()),
    ]);
    expect($result->total())->toBe(1); // Should only find the active Laravel role

    // Test with no matching criteria
    $result = $roleRepository->paginate(1, 10, ['label' => 'Django']);
    expect($result->total())->toBe(0);

    // Test search by created_at_range criteria
    $role4 = Role::create(
        RoleId::null(),
        'Past Role',
        new CarbonImmutable('2021-01-01')
    );
    $roleRepository->create($role4);

    $result = $roleRepository->paginate(1, 10, [
        'created_at_range' => ['2021-01-01', '2021-01-02'],
    ]);

    expect($result->total())->toBe(1); // Should find the role created on 2021-01-01
});

it('can delete an role', function () {
    // Create a test role in the database
    $createdRole = Role::create(RoleId::null(), 'Test Role');
    $roleRepository = new RoleRepository;
    $savedRole = $roleRepository->create($createdRole);

    // Delete the role
    $roleRepository->delete($savedRole);

    // Verify the role was deleted
    $this->assertDatabaseMissing('roles', [
        'id' => $savedRole->getId()->getValue(),
    ]);
});

it('throws an exception when deleting a non-existent role', function () {
    $roleRepository = new RoleRepository;

    // Attempt to delete a non-existent role
    $roleRepository->delete(Role::create(RoleId::fromInt(999), 'Test Role'));
})->throws(RoleNotFoundException::class);
