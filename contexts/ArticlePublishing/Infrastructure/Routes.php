<?php

declare(strict_types=1);

use Contexts\ArticlePublishing\Presentation\Controllers\ArticlePublishingController;
use Illuminate\Support\Facades\Route;

Route::middleware([])->name('ArticlePublishing.')->group(function () {
    Route::controller(ArticlePublishingController::class)->prefix('articles')->name('ArticlePublishing.')->group(function () {
        Route::get('{id}', 'getArticle')->name('getArticle');
        Route::get('', 'getArticleList')->name('getArticleList');
        Route::post('', 'createArticle')->name('createArticle');
        Route::put('{id}/archive', 'archiveArticle')->name('archiveArticle');
        Route::put('{id}/publish', 'publishDraft')->name('publishDraft');
        Route::put('{id}', 'updateArticle')->name('updateArticle');
        Route::delete('{id}', 'deleteArticle')->name('deleteArticle');
    });
});
