<?php

declare(strict_types=1);
use Carbon\CarbonImmutable;
use Contexts\CategoryManagement\Domain\Exceptions\CategoryNotFoundException;
use Contexts\CategoryManagement\Domain\Models\Category;
use Contexts\CategoryManagement\Domain\Models\CategoryId;
use Contexts\CategoryManagement\Domain\Models\CategoryStatus;
use Contexts\CategoryManagement\Infrastructure\Persistence\CategoryPersistence;
use Contexts\CategoryManagement\Infrastructure\Records\CategoryRecord;

it('can persist category data correctly', function () {
    $category = Category::create(CategoryId::null(), 'My Category');
    $categoryPersistence = new CategoryPersistence;

    $categoryPersistence->create($category);

    $this->assertDatabaseHas('categories', [
        'label' => 'My Category',
        'status' => CategoryRecord::mapStatusToRecord(CategoryStatus::active()),
    ]);
});

it('can retrieve an category by ID', function () {
    // Create a test category in the database
    $createdCategory = Category::create(CategoryId::null(), 'Test Category');
    $categoryPersistence = new CategoryPersistence;
    $savedCategory = $categoryPersistence->create($createdCategory);

    // Retrieve the category using getById
    $retrievedCategory = $categoryPersistence->getById($savedCategory->id);

    // Assert the retrieved category matches the created one
    expect($retrievedCategory->getId()->getValue())->toBe($savedCategory->getId()->getValue());
    expect($retrievedCategory->getLabel())->toBe('Test Category');
    expect($retrievedCategory->getStatus()->equals(CategoryStatus::active()))->toBeTrue();
});

it('throws an exception when retrieving a non-existent category', function () {
    $categoryPersistence = new CategoryPersistence;

    // Attempt to retrieve a non-existent category
    $categoryPersistence->getById(CategoryId::fromInt(999));
})->throws(CategoryNotFoundException::class);

it('can update an category', function () {
    // Create a test category in the database
    $createdCategory = Category::create(CategoryId::null(), 'Original Label');
    $categoryPersistence = new CategoryPersistence;
    $savedCategory = $categoryPersistence->create($createdCategory);

    // Create an updated version of the category
    $updatedCategory = Category::create(
        $savedCategory->id,
        'Updated Label',
    );

    // Update the category
    $result = $categoryPersistence->update($updatedCategory);

    // Verify database was updated
    $this->assertDatabaseHas('categories', [
        'id' => $savedCategory->getId()->getValue(),
        'label' => 'Updated Label',
        'status' => CategoryRecord::mapStatusToRecord(CategoryStatus::active()),
    ]);

    // Verify returned object reflects updates
    expect($result->getLabel())->toBe('Updated Label');
    expect($result->getStatus()->equals(CategoryStatus::active()))->toBeTrue();
});

it('throws an exception when updating a non-existent category', function () {
    $categoryPersistence = new CategoryPersistence;

    // Attempt to update a non-existent category
    $categoryPersistence->update(Category::create(CategoryId::fromInt(999), 'Updated Label'));
})->throws(CategoryNotFoundException::class);

it('can paginate categories', function () {
    // Create multiple test categories
    $categoryPersistence = new CategoryPersistence;

    // Create 5 categories
    for ($i = 1; $i <= 5; $i++) {
        $category = Category::create(
            CategoryId::null(),
            "Category $i",
            new CarbonImmutable
        );
        $categoryPersistence->create($category);
    }

    // Test pagination with default criteria
    $result = $categoryPersistence->paginate(1, 2, []);

    // Assert pagination metadata
    expect($result->total())->toBe(5);
    expect($result->perPage())->toBe(2);
    expect($result->currentPage())->toBe(1);
    expect(count($result->items()))->toBe(2); // 2 items on the first page

    // Test second page
    $result2 = $categoryPersistence->paginate(2, 2, []);
    expect($result2->currentPage())->toBe(2);
    expect(count($result2->items()))->toBe(2); // 2 items on the second page

    // Test last page
    $result3 = $categoryPersistence->paginate(3, 2, []);
    expect($result3->currentPage())->toBe(3);
    expect(count($result3->items()))->toBe(1); // 1 item on the last page
});

it('can filter categories with search criteria', function () {
    $categoryPersistence = new CategoryPersistence;

    // Create categories with specific labels
    $category1 = Category::create(
        CategoryId::null(),
        'Laravel Category',
        new CarbonImmutable
    );
    $categoryPersistence->create($category1);

    $category2 = Category::create(
        CategoryId::null(),
        'PHP Tutorial',
        new CarbonImmutable
    );
    $category2->subspend();
    $categoryPersistence->create($category2);

    $category3 = Category::create(
        CategoryId::null(),
        'Laravel Tips',
        new CarbonImmutable
    );
    $category3->subspend();
    $categoryPersistence->create($category3);

    // Test search by label criteria
    $result = $categoryPersistence->paginate(1, 10, ['label' => 'Laravel']);
    expect($result->total())->toBe(2); // Should find the two Laravel categories

    // Test search with status criteria
    $result = $categoryPersistence->paginate(1, 10, [
        'label' => 'Laravel',
        'status' => CategoryRecord::mapStatusToRecord(CategoryStatus::active()),
    ]);
    expect($result->total())->toBe(1); // Should only find the active Laravel category

    // Test with no matching criteria
    $result = $categoryPersistence->paginate(1, 10, ['label' => 'Django']);
    expect($result->total())->toBe(0);

    // Test search by created_at_range criteria
    $category4 = Category::create(
        CategoryId::null(),
        'Past Category',
        new CarbonImmutable('2021-01-01')
    );
    $categoryPersistence->create($category4);

    $result = $categoryPersistence->paginate(1, 10, [
        'created_at_range' => ['2021-01-01', '2021-01-02'],
    ]);

    expect($result->total())->toBe(1); // Should find the category created on 2021-01-01
});

it('can delete an category', function () {
    // Create a test category in the database
    $createdCategory = Category::create(CategoryId::null(), 'Test Category');
    $categoryPersistence = new CategoryPersistence;
    $savedCategory = $categoryPersistence->create($createdCategory);

    // Delete the category
    $categoryPersistence->delete($savedCategory);

    // Verify the category was deleted
    $this->assertDatabaseMissing('categories', [
        'id' => $savedCategory->getId()->getValue(),
    ]);
});

it('throws an exception when deleting a non-existent category', function () {
    $categoryPersistence = new CategoryPersistence;

    // Attempt to delete a non-existent category
    $categoryPersistence->delete(Category::create(CategoryId::fromInt(999), 'Test Category'));
})->throws(CategoryNotFoundException::class);
