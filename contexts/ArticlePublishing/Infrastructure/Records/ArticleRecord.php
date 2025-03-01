<?php

declare(strict_types=1);

namespace Contexts\ArticlePublishing\Infrastructure\Records;

use App\Http\Models\BaseModel;
use Carbon\Traits\Timestamp;
use Contexts\ArticlePublishing\Domain\Models\Article;
use Contexts\ArticlePublishing\Domain\Models\ArticleId;

class ArticleRecord extends BaseModel
{
    protected $table = 'articles';
    protected $fillable = ['title', 'content', 'created_at'];

    public function toDomain(): Article
    {
        return new Article(
            new ArticleId($this->id),
            $this->title,
            $this->content,
            $this->created_at->toImmutable(),
            $this->updated_at?->toImmutable(),
        );
    }
}
