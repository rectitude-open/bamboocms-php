<?php

declare(strict_types=1);
use Carbon\CarbonImmutable;
use Contexts\ArticlePublishing\Domain\Models\Article;
use Contexts\ArticlePublishing\Domain\Models\ArticleId;
use Contexts\ArticlePublishing\Domain\Models\ArticleStatus;
use Contexts\ArticlePublishing\Infrastructure\Records\ArticleRecord;
use Contexts\ArticlePublishing\Infrastructure\Repositories\ArticleRepository;

it('can persist draft article data correctly', function () {
    $article = Article::createDraft(ArticleId::null(), 'My Article', 'This is my article body', new CarbonImmutable);
    $articleRepository = new ArticleRepository;

    $articleRepository->create($article);

    $this->assertDatabaseHas('articles', [
        'title' => 'My Article',
        'body' => 'This is my article body',
        'status' => ArticleRecord::mapStatusToRecord(ArticleStatus::draft()),
    ]);
});

it('can persist published article data correctly', function () {
    $article = Article::createPublished(ArticleId::null(), 'My Article', 'This is my article body', new CarbonImmutable);
    $articleRepository = new ArticleRepository;

    $articleRepository->create($article);

    $this->assertDatabaseHas('articles', [
        'title' => 'My Article',
        'body' => 'This is my article body',
        'status' => ArticleRecord::mapStatusToRecord(ArticleStatus::published()),
    ]);
});

it('can retrieve an article by ID', function () {
    // Create a test article in the database
    $createdArticle = Article::createDraft(ArticleId::null(), 'Test Article', 'Test Content', new CarbonImmutable);
    $articleRepository = new ArticleRepository;
    $savedArticle = $articleRepository->create($createdArticle);

    // Retrieve the article using getById
    $retrievedArticle = $articleRepository->getById($savedArticle->id);

    // Assert the retrieved article matches the created one
    expect($retrievedArticle->getId()->getValue())->toBe($savedArticle->getId()->getValue());
    expect($retrievedArticle->getTitle())->toBe('Test Article');
    expect($retrievedArticle->getBody())->toBe('Test Content');
    expect($retrievedArticle->getStatus()->equals(ArticleStatus::draft()))->toBeTrue();
});

it('can update an article', function () {
    // Create a test article in the database
    $createdArticle = Article::createDraft(ArticleId::null(), 'Original Title', 'Original Content', new CarbonImmutable);
    $articleRepository = new ArticleRepository;
    $savedArticle = $articleRepository->create($createdArticle);

    // Create an updated version of the article
    $updatedArticle = Article::createPublished(
        $savedArticle->id,
        'Updated Title',
        'Updated Content',
        new CarbonImmutable
    );

    // Update the article
    $result = $articleRepository->update($updatedArticle);

    // Verify database was updated
    $this->assertDatabaseHas('articles', [
        'id' => $savedArticle->getId()->getValue(),
        'title' => 'Updated Title',
        'body' => 'Updated Content',
        'status' => ArticleRecord::mapStatusToRecord(ArticleStatus::published()),
    ]);

    // Verify returned object reflects updates
    expect($result->getTitle())->toBe('Updated Title');
    expect($result->getBody())->toBe('Updated Content');
    expect($result->getStatus()->equals(ArticleStatus::published()))->toBeTrue();
});

it('can paginate articles', function () {
    // Create multiple test articles
    $articleRepository = new ArticleRepository;

    // Create 5 articles
    for ($i = 1; $i <= 5; $i++) {
        $article = Article::createPublished(
            ArticleId::null(),
            "Article $i",
            "Content $i",
            new CarbonImmutable
        );
        $articleRepository->create($article);
    }

    // Test pagination with default criteria
    $result = $articleRepository->paginate(1, 2, []);

    // Assert pagination metadata
    expect($result->total())->toBe(5);
    expect($result->perPage())->toBe(2);
    expect($result->currentPage())->toBe(1);
    expect(count($result->items()))->toBe(2); // 2 items on the first page

    // Test second page
    $result2 = $articleRepository->paginate(2, 2, []);
    expect($result2->currentPage())->toBe(2);
    expect(count($result2->items()))->toBe(2); // 2 items on the second page

    // Test last page
    $result3 = $articleRepository->paginate(3, 2, []);
    expect($result3->currentPage())->toBe(3);
    expect(count($result3->items()))->toBe(1); // 1 item on the last page
});

it('can filter articles with search criteria', function () {
    $articleRepository = new ArticleRepository;

    // Create articles with specific titles
    $article1 = Article::createDraft(
        ArticleId::null(),
        'Laravel Article',
        'Content about Laravel',
        new CarbonImmutable
    );
    $articleRepository->create($article1);

    $article2 = Article::createDraft(
        ArticleId::null(),
        'PHP Tutorial',
        'Content about PHP',
        new CarbonImmutable
    );
    $articleRepository->create($article2);

    $article3 = Article::createPublished(
        ArticleId::null(),
        'Laravel Tips',
        'More Laravel content',
        new CarbonImmutable
    );
    $articleRepository->create($article3);

    // Test search by title criteria
    $result = $articleRepository->paginate(1, 10, ['title' => 'Laravel']);
    expect($result->total())->toBe(2); // Should find the two Laravel articles

    // Test search with status criteria
    $result = $articleRepository->paginate(1, 10, [
        'title' => 'Laravel',
        'status' => ArticleRecord::mapStatusToRecord(ArticleStatus::published()),
    ]);
    expect($result->total())->toBe(1); // Should only find the published Laravel article

    // Test with no matching criteria
    $result = $articleRepository->paginate(1, 10, ['title' => 'Django']);
    expect($result->total())->toBe(0);

    // Test search by created_at_range criteria
    $article4 = Article::createPublished(
        ArticleId::null(),
        'Laravel Tips',
        'More Laravel content',
        new CarbonImmutable('2021-01-01')
    );
    $articleRepository->create($article4);

    $result = $articleRepository->paginate(1, 10, [
        'created_at_range' => ['2021-01-01', '2021-01-02'],
    ]);

    expect($result->total())->toBe(1); // Should find the article created on 2021-01-01
});
