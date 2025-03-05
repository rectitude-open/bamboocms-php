<?php

declare(strict_types=1);

use Carbon\CarbonImmutable;
use Contexts\CategoryManagement\Domain\Models\Category;
use Contexts\CategoryManagement\Domain\Models\CategoryId;
use Contexts\CategoryManagement\Domain\Models\CategoryStatus;

it('can create draft category with valid data', function () {
    $category = Category::createDraft(new CategoryId(0), 'Title', 'body', new CarbonImmutable);
    expect($category->getTitle())->toBe('Title');
    expect($category->getbody())->toBe('body');
    expect($category->getStatus()->equals(CategoryStatus::draft()))->toBeTrue();
});

it('can create published category with valid data', function () {
    $category = Category::createPublished(new CategoryId(0), 'Title', 'body', new CarbonImmutable);
    expect($category->getTitle())->toBe('Title');
    expect($category->getbody())->toBe('body');
    expect($category->getStatus()->equals(CategoryStatus::published()))->toBeTrue();
});

it('can auto generate created_at date', function () {
    $category = Category::createDraft(new CategoryId(0), 'Title', 'body', new CarbonImmutable);
    expect($category->getCreatedAt())->toBeInstanceOf(CarbonImmutable::class);
});

it('can reconstitute an category from its data', function () {
    $id = new CategoryId(1);
    $title = 'Reconstituted Title';
    $body = 'Reconstituted content body';
    $status = CategoryStatus::published();
    $createdAt = CarbonImmutable::now()->subDays(5);
    $updatedAt = CarbonImmutable::now()->subDays(1);

    $category = Category::reconstitute($id, $title, $body, $status, $createdAt, $updatedAt);

    expect($category->id)->toEqual($id);
    expect($category->getTitle())->toBe($title);
    expect($category->getBody())->toBe($body);
    expect($category->getStatus())->toEqual($status);
    expect($category->getCreatedAt())->toEqual($createdAt);
    expect($category->getUpdatedAt())->toEqual($updatedAt);
});

it('can publish a draft category', function () {
    $category = Category::createDraft(new CategoryId(1), 'Draft Title', 'Draft content');
    $category->publish();

    expect($category->getStatus()->equals(CategoryStatus::published()))->toBeTrue();
    expect($category->releaseEvents())->toHaveCount(1);
});

it('should record domain events when category is published', function () {
    $category = Category::createPublished(new CategoryId(1), 'Title', 'body');
    $events = $category->releaseEvents();

    expect($events)->toHaveCount(1);
    expect($events[0])->toBeInstanceOf(\Contexts\CategoryManagement\Domain\Events\CategoryPublishedEvent::class);
});

it('can release events and clear them from the category', function () {
    $category = Category::createPublished(new CategoryId(1), 'Title', 'body');

    // First release should return events
    $events = $category->releaseEvents();
    expect($events)->toHaveCount(1);

    // Second release should return empty array since events were cleared
    $emptyEvents = $category->releaseEvents();
    expect($emptyEvents)->toBeEmpty();
});

it('can revise an category title', function () {
    $category = Category::createDraft(new CategoryId(1), 'Original Title', 'Original Body');
    $category->revise('New Title', null, null);

    expect($category->getTitle())->toBe('New Title');
    expect($category->getBody())->toBe('Original Body');
    expect($category->getStatus()->equals(CategoryStatus::draft()))->toBeTrue();
});

it('can revise an category body', function () {
    $category = Category::createDraft(new CategoryId(1), 'Original Title', 'Original Body');
    $category->revise(null, 'New Body Content', null);

    expect($category->getTitle())->toBe('Original Title');
    expect($category->getBody())->toBe('New Body Content');
    expect($category->getStatus()->equals(CategoryStatus::draft()))->toBeTrue();
});

it('can revise an category status', function () {
    $category = Category::createDraft(new CategoryId(1), 'Original Title', 'Original Body');
    $category->revise(null, null, CategoryStatus::published());

    expect($category->getTitle())->toBe('Original Title');
    expect($category->getBody())->toBe('Original Body');
    expect($category->getStatus()->equals(CategoryStatus::published()))->toBeTrue();
    expect($category->releaseEvents())->toHaveCount(1);
});

it('can revise multiple category properties at once', function () {
    $category = Category::createDraft(new CategoryId(1), 'Original Title', 'Original Body');
    $category->revise('New Title', 'New Body', CategoryStatus::published());

    expect($category->getTitle())->toBe('New Title');
    expect($category->getBody())->toBe('New Body');
    expect($category->getStatus()->equals(CategoryStatus::published()))->toBeTrue();
    expect($category->releaseEvents())->toHaveCount(1);
});

it('can revise category created_at date', function () {
    $originalDate = CarbonImmutable::now()->subDays(5);
    $category = Category::createDraft(new CategoryId(1), 'Original Title', 'Original Body', $originalDate);

    $newDate = CarbonImmutable::now()->subDays(10);
    $category->revise(null, null, null, $newDate);

    expect($category->getCreatedAt())->toEqual($newDate);
});

it('does not trigger status transition when same status provided', function () {
    $category = Category::createDraft(new CategoryId(1), 'Title', 'Body');
    $category->revise(null, null, CategoryStatus::draft());

    expect($category->getStatus()->equals(CategoryStatus::draft()))->toBeTrue();
    expect($category->releaseEvents())->toBeEmpty();
});

it('can archive an category', function () {
    $category = Category::createPublished(new CategoryId(1), 'Title', 'Body');
    $category->releaseEvents(); // Clear initial events

    $category->archive();

    expect($category->getStatus()->equals(CategoryStatus::archived()))->toBeTrue();
    expect($category->releaseEvents())->toBeEmpty(); // No events for archiving
});

it('can delete an category', function () {
    $category = Category::createPublished(new CategoryId(1), 'Title', 'Body');
    $category->archive();
    $category->releaseEvents(); // Clear initial events

    $category->delete();

    expect($category->getStatus()->equals(CategoryStatus::deleted()))->toBeTrue();
    expect($category->releaseEvents())->toBeEmpty(); // No events for deleting
});
