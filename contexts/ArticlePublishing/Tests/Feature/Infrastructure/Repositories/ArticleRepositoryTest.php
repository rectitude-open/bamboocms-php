<?php

declare(strict_types=1);

use Contexts\ArticlePublishing\Domain\Models\Article;
use Contexts\ArticlePublishing\Domain\Models\ArticleId;
use Contexts\ArticlePublishing\Domain\Models\ArticleStatus;
use Contexts\ArticlePublishing\Infrastructure\Records\ArticleRecord;
use Contexts\ArticlePublishing\Infrastructure\Repositories\ArticleRepository;
use Carbon\CarbonImmutable;

it('can persist draft article data correctly', function () {
    $article = Article::createDraft(new ArticleId(0), 'My Article', 'This is my article body', new CarbonImmutable());
    $articleRepository = new ArticleRepository();

    $articleRepository->create($article);

    $this->assertDatabaseHas('articles', [
        'title' => 'My Article',
        'body' => 'This is my article body',
        'status' => ArticleRecord::mapStatusToRecord(ArticleStatus::draft()),
    ]);
});

it('can persist published article data correctly', function () {
    $article = Article::createPublished(new ArticleId(0), 'My Article', 'This is my article body', new CarbonImmutable());
    $articleRepository = new ArticleRepository();

    $articleRepository->create($article);

    $this->assertDatabaseHas('articles', [
        'title' => 'My Article',
        'body' => 'This is my article body',
        'status' => ArticleRecord::mapStatusToRecord(ArticleStatus::published()),
    ]);
});


it('can retrieve an article by ID', function () {
    // Create a test article in the database
    $createdArticle = Article::createDraft(new ArticleId(0), 'Test Article', 'Test Content', new CarbonImmutable());
    $articleRepository = new ArticleRepository();
    $savedArticle = $articleRepository->create($createdArticle);

    // Retrieve the article using getById
    $retrievedArticle = $articleRepository->getById($savedArticle->id);

    // Assert the retrieved article matches the created one
    expect($retrievedArticle->id->value)->toBe($savedArticle->id->value);
    expect($retrievedArticle->getTitle())->toBe('Test Article');
    expect($retrievedArticle->getBody())->toBe('Test Content');
    expect($retrievedArticle->getStatus()->equals(ArticleStatus::draft()))->toBeTrue();
});

it('can update an article', function () {
    // Create a test article in the database
    $createdArticle = Article::createDraft(new ArticleId(0), 'Original Title', 'Original Content', new CarbonImmutable());
    $articleRepository = new ArticleRepository();
    $savedArticle = $articleRepository->create($createdArticle);

    // Create an updated version of the article
    $updatedArticle = Article::createPublished(
        $savedArticle->id,
        'Updated Title',
        'Updated Content',
        new CarbonImmutable()
    );

    // Update the article
    $result = $articleRepository->update($updatedArticle);

    // Verify database was updated
    $this->assertDatabaseHas('articles', [
        'id' => $savedArticle->id->value,
        'title' => 'Updated Title',
        'body' => 'Updated Content',
        'status' => ArticleRecord::mapStatusToRecord(ArticleStatus::published()),
    ]);

    // Verify returned object reflects updates
    expect($result->getTitle())->toBe('Updated Title');
    expect($result->getBody())->toBe('Updated Content');
    expect($result->getStatus()->equals(ArticleStatus::published()))->toBeTrue();
});
