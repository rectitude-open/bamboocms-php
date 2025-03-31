<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title')->default('');
            $table->text('body')->nullable();
            $table->tinyInteger('status')->default(0)->comment('0: draft, 1: published, 2: archived, 3: deleted');
            $table->unsignedInteger('author_id');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('pivot_article_category', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('article_id');
            $table->unsignedInteger('category_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('articles');
        Schema::dropIfExists('pivot_article_category');
    }
};
