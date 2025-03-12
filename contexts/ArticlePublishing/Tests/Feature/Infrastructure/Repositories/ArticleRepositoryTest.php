<?php

declare(strict_types=1);

namespace Contexts\ArticlePublishing\Tests\Feature\Infrastructure\Repositories;

use Carbon\CarbonImmutable;
use Contexts\ArticlePublishing\Domain\Exceptions\ArticleNotFoundException;
use Contexts\ArticlePublishing\Domain\Models\Article;
use Contexts\ArticlePublishing\Domain\Models\ArticleCategory;
use Contexts\ArticlePublishing\Domain\Models\ArticleCategoryCollection;
use Contexts\ArticlePublishing\Domain\Models\ArticleId;
use Contexts\ArticlePublishing\Domain\Models\ArticleStatus;
use Contexts\ArticlePublishing\Domain\Models\AuthorId;
use Contexts\ArticlePublishing\Infrastructure\Records\ArticleRecord;
use Contexts\ArticlePublishing\Infrastructure\Repositories\ArticleRepository;
use Contexts\CategoryManagement\Infrastructure\Records\CategoryRecord;

beforeEach(function () {
    // Create test categories
    $this->categories = CategoryRecord::factory()->count(3)->create();

    // Initialize repository
    $this->repository = new ArticleRepository;
});

it('can create an article', function () {
    $article = Article::createDraft(
        ArticleId::null(),
        'Test Article Title',
        'Test Article Content',
        new ArticleCategoryCollection([
            new ArticleCategory($this->categories[0]->id, $this->categories[0]->label),
            new ArticleCategory($this->categories[1]->id, $this->categories[1]->label),
        ]),
        AuthorId::null()
    );

    $savedArticle = $this->repository->create($article);

    expect($savedArticle->getId()->getValue())->toBeGreaterThan(0)
        ->and($savedArticle->getTitle())->toBe('Test Article Title')
        ->and($savedArticle->getBody())->toBe('Test Article Content')
        ->and($savedArticle->getStatus()->getValue())->toBe(ArticleStatus::DRAFT);

    $this->assertDatabaseHas('articles', [
        'id' => $savedArticle->getId()->getValue(),
        'title' => 'Test Article Title',
        'body' => 'Test Article Content',
        'status' => 0,
        'author_id' => $savedArticle->getAuthorId()->getValue(),
    ]);

    $categoryIds = $savedArticle->getCategories()->getIdsArray();
    foreach ($categoryIds as $categoryId) {
        $this->assertDatabaseHas('pivot_article_category', [
            'article_id' => $savedArticle->getId()->getValue(),
            'category_id' => $categoryId,
        ]);
    }
});

it('can get an article by id', function () {
    $articleRecord = ArticleRecord::create([
        'title' => 'Test Article Title',
        'body' => 'Test Article Content',
        'status' => 0,
        'author_id' => 1,
        'created_at' => now(),
    ]);

    $articleRecord->categories()->attach([$this->categories[0]->id, $this->categories[1]->id]);

    $article = $this->repository->getById(ArticleId::fromInt($articleRecord->id));

    expect($article->getId()->getValue())->toBe($articleRecord->id)
        ->and($article->getTitle())->toBe('Test Article Title')
        ->and($article->getBody())->toBe('Test Article Content')
        ->and($article->getStatus()->getValue())->toBe(ArticleStatus::DRAFT)
        ->and($article->getAuthorId()->getValue())->toBe(1);

    $categoryIds = $article->getCategories()->getIdsArray();
    expect($categoryIds)->toContain($this->categories[0]->id, $this->categories[1]->id);
});

it('throws exception when article not found', function () {
    $this->expectException(ArticleNotFoundException::class);

    $this->repository->getById(ArticleId::fromInt(999));
});

it('can update an article', function () {
    $articleRecord = ArticleRecord::create([
        'title' => 'Original Title',
        'body' => 'Original Content',
        'status' => 0,
        'author_id' => 0,
        'created_at' => now(),
    ]);

    $articleRecord->categories()->attach([$this->categories[0]->id]);

    $article = $this->repository->getById(ArticleId::fromInt($articleRecord->id));

    $article->revise(
        'Updated Title',
        'Updated Content',
        ArticleStatus::published(),
        new ArticleCategoryCollection([
            new ArticleCategory($this->categories[1]->id, $this->categories[1]->label),
            new ArticleCategory($this->categories[2]->id, $this->categories[2]->label),
        ]),
        AuthorId::fromInt(1)
    );

    $updatedArticle = $this->repository->update($article);

    expect($updatedArticle->getTitle())->toBe('Updated Title')
        ->and($updatedArticle->getBody())->toBe('Updated Content')
        ->and($updatedArticle->getStatus()->getValue())->toBe(ArticleStatus::PUBLISHED);

    $this->assertDatabaseHas('articles', [
        'id' => $updatedArticle->getId()->getValue(),
        'title' => 'Updated Title',
        'body' => 'Updated Content',
        'status' => 1,
        'author_id' => 1,
    ]);

    $this->assertDatabaseMissing('pivot_article_category', [
        'article_id' => $updatedArticle->getId()->getValue(),
        'category_id' => $this->categories[0]->id,
    ]);

    $this->assertDatabaseHas('pivot_article_category', [
        'article_id' => $updatedArticle->getId()->getValue(),
        'category_id' => $this->categories[1]->id,
    ]);

    $this->assertDatabaseHas('pivot_article_category', [
        'article_id' => $updatedArticle->getId()->getValue(),
        'category_id' => $this->categories[2]->id,
    ]);
});

it('can paginate articles', function () {
    for ($i = 1; $i <= 10; $i++) {
        $status = $i % 3 === 0 ? 1 : 0;
        $article = ArticleRecord::create([
            'title' => "Article Title {$i}",
            'body' => "Article Content {$i}",
            'status' => $status,
            'author_id' => 1,
            'created_at' => CarbonImmutable::now()->subDays($i),
        ]);
        $article->categories()->attach([$this->categories[$i % 3]->id]);
    }

    // Test basic pagination
    $result = $this->repository->paginate(1, 5);
    expect($result->count())->toBe(5)
        ->and($result->total())->toBe(10);

    // Test filtering by status
    $publishedResult = $this->repository->paginate(1, 10, ['status' => 1]);
    expect($publishedResult->count())->toBeLessThan(10)
        ->and($publishedResult->items())->toHaveCount(3);

    // Test search by title
    $searchResult = $this->repository->paginate(1, 10, ['title' => 'Title 1']);
    expect($searchResult->items())->toHaveCount(2); // 1 and 10
});

it('can delete an article', function () {
    $articleRecord = ArticleRecord::create([
        'title' => 'Test Article Title',
        'body' => 'Test Article Content',
        'status' => 0,
        'created_at' => now(),
        'author_id' => 1,
    ]);
    $articleRecord->categories()->attach([$this->categories[0]->id]);

    $id = $articleRecord->id;

    $article = $this->repository->getById(ArticleId::fromInt($id));

    $this->repository->delete($article);

    $this->assertSoftDeleted('articles', ['id' => $id]);

    $this->expectException(ArticleNotFoundException::class);
    $this->repository->getById(ArticleId::fromInt($id));
});
