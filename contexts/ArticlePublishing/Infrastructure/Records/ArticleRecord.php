<?php

declare(strict_types=1);

namespace Contexts\ArticlePublishing\Infrastructure\Records;

use App\Http\Models\BaseModel;
use Contexts\ArticlePublishing\Domain\Models\Article;
use Contexts\ArticlePublishing\Domain\Models\ArticleId;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $title
 * @property string $body
 * @property Carbon $created_at
 * @property Carbon|null $updated_at
 */
class ArticleRecord extends BaseModel
{
    protected $table = 'articles';
    protected $fillable = ['title', 'body', 'created_at'];

    public function toDomain(): Article
    {
        return new Article(
            new ArticleId($this->id),
            $this->title,
            $this->body,
            $this->created_at->toImmutable(),
            $this->updated_at?->toImmutable(),
        );
    }
}
