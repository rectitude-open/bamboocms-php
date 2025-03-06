<?php

declare(strict_types=1);

use Carbon\CarbonImmutable;
use Contexts\CategoryManagement\Domain\Models\Category;
use Contexts\CategoryManagement\Domain\Models\CategoryId;
use Contexts\CategoryManagement\Domain\Models\CategoryStatus;

it('can create category with valid data', function () {
    $category = Category::create(CategoryId::null(), 'Label');
    expect($category->getLabel())->toBe('Label');
    expect($category->getStatus()->equals(CategoryStatus::active()))->toBeTrue();
    expect($category->getCreatedAt())->toBeInstanceOf(CarbonImmutable::class);
});

it('can reconstitute an category from its data', function () {
    $id = CategoryId::fromInt(1);
    $label = 'Reconstituted Label';
    $status = CategoryStatus::active();
    $createdAt = CarbonImmutable::now()->subDays(5);
    $updatedAt = CarbonImmutable::now()->subDays(1);

    $category = Category::reconstitute($id, $label, $status, $createdAt, $updatedAt);

    expect($category->id)->toEqual($id);
    expect($category->getLabel())->toBe($label);
    expect($category->getStatus())->toEqual($status);
    expect($category->getCreatedAt())->toEqual($createdAt);
    expect($category->getUpdatedAt())->toEqual($updatedAt);
});

it('should record domain events when category is active', function () {
    $category = Category::create(CategoryId::fromInt(1), 'Label');
    $events = $category->releaseEvents();

    expect($events)->toHaveCount(1);
    expect($events[0])->toBeInstanceOf(\Contexts\CategoryManagement\Domain\Events\CategoryCreatedEvent::class);
});

it('can release events and clear them from the category', function () {
    $category = Category::create(CategoryId::fromInt(1), 'Label');

    // First release should return events
    $events = $category->releaseEvents();
    expect($events)->toHaveCount(1);

    // Second release should return empty array since events were cleared
    $emptyEvents = $category->releaseEvents();
    expect($emptyEvents)->toBeEmpty();
});

it('can modify an category label', function () {
    $category = Category::create(CategoryId::fromInt(1), 'Original Label');
    $category->modify('New Label', null);

    expect($category->getLabel())->toBe('New Label');
});

it('can modify an category status', function () {
    $category = Category::create(CategoryId::fromInt(1), 'Original Label');
    $category->modify(null, CategoryStatus::active());

    expect($category->getLabel())->toBe('Original Label');
    expect($category->getStatus()->equals(CategoryStatus::active()))->toBeTrue();
    expect($category->releaseEvents())->toHaveCount(1);
});

it('can modify multiple category properties at once', function () {
    $category = Category::create(CategoryId::fromInt(1), 'Original Label');
    $category->modify('New Label', CategoryStatus::active());

    expect($category->getLabel())->toBe('New Label');
    expect($category->getStatus()->equals(CategoryStatus::active()))->toBeTrue();
    expect($category->releaseEvents())->toHaveCount(1);
});

it('can modify category created_at date', function () {
    $originalDate = CarbonImmutable::now()->subDays(5);
    $category = Category::create(CategoryId::fromInt(1), 'Original Label', $originalDate);

    $newDate = CarbonImmutable::now()->subDays(10);
    $category->modify(null, null, $newDate);

    expect($category->getCreatedAt())->toEqual($newDate);
});

it('does not trigger status transition when same status provided', function () {
    $category = Category::create(CategoryId::fromInt(1), 'Label');
    $category->releaseEvents();

    $category->modify(null, CategoryStatus::subspended());
    expect($category->getStatus()->equals(CategoryStatus::subspended()))->toBeTrue();
    expect($category->releaseEvents())->toBeEmpty();
});

it('can subspend an category', function () {
    $category = Category::create(CategoryId::fromInt(1), 'Label');
    $category->releaseEvents();

    $category->subspend();

    expect($category->getStatus()->equals(CategoryStatus::subspended()))->toBeTrue();
    expect($category->releaseEvents())->toBeEmpty(); // No events for subspending
});

it('can delete an category', function () {
    $category = Category::create(CategoryId::fromInt(1), 'Label');
    $category->subspend();
    $category->releaseEvents();

    $category->delete();

    expect($category->getStatus()->equals(CategoryStatus::deleted()))->toBeTrue();
    expect($category->releaseEvents())->toBeEmpty(); // No events for deleting
});
