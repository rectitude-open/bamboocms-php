<?php

declare(strict_types=1);

use Contexts\CategoryManagement\Application\Coordinators\CategoryServiceCoordinator;
use Contexts\CategoryManagement\Contracts\V1\DTOs\CategoryDTO;
use Contexts\CategoryManagement\Domain\Models\CategoryStatus;
use Contexts\CategoryManagement\Domain\Repositories\CategoryRepository;
use Contexts\CategoryManagement\Infrastructure\Records\CategoryRecord;

it('should resolve categories', function () {
    CategoryRecord::factory()->count(3)->create([
        'status' => CategoryRecord::mapStatusToRecord(CategoryStatus::active()),
    ]);
    CategoryRecord::factory()->count(3)->create([
        'status' => CategoryRecord::mapStatusToRecord(CategoryStatus::suspended()),
    ]);
    $categoryIds = CategoryRecord::all()->pluck('id')->toArray();
    $categoryRepository = app(CategoryRepository::class);
    $categoryServiceCoordinator = new CategoryServiceCoordinator($categoryRepository);

    $categories = $categoryServiceCoordinator->resolveCategories($categoryIds);

    expect($categories)->toHaveCount(6);
    expect($categories[0])->toBeInstanceOf(CategoryDTO::class);
    expect($categories[0]->status->value)->toBe('active');
    expect($categories[3]->status->value)->toBe('suspended');
});
