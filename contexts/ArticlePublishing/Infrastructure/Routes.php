<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Contexts\ArticlePublishing\Presentation\Controllers\ArticlePublishingController;

Route::middleware([])->name('ArticlePublishing.')->group(function () {
    Route::controller(ArticlePublishingController::class)->prefix('articles')->name('ArticlePublishing.')->group(function () {
        Route::get('{id}', 'getArticle')->name('getArticle');
        Route::get('', 'getArticleList')->name('getArticleList');
        Route::post('', 'createArticle')->name('createArticle');
        Route::put('{id}/publish', 'publishDraft')->name('publishDraft');
    });
});
