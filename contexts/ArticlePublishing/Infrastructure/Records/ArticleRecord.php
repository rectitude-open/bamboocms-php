<?php

declare(strict_types=1);

namespace Contexts\ArticlePublishing\Infrastructure\Records;

use App\Http\Models\BaseModel;
use Contexts\ArticlePublishing\Domain\Models\Article;
use Contexts\ArticlePublishing\Domain\Models\ArticleId;
use Contexts\ArticlePublishing\Domain\Models\ArticleStatus;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;

/**
 * @property int $id
 * @property string $title
 * @property string $body
 * @property int $status
 * @property Carbon $created_at
 * @property Carbon|null $updated_at
 */
class ArticleRecord extends BaseModel
{
    protected $table = 'articles';
    protected $fillable = ['title', 'body', 'status', 'created_at'];

    public const STATUS_MAPPING = [
        0 => 'draft',
        1 => 'published',
        2 => 'archived',
        3 => 'deleted'
    ];

    public static function mapStatusToDomain(int $status): ArticleStatus
    {
        if (!isset(self::STATUS_MAPPING[$status])) {
            throw new \InvalidArgumentException('Invalid status value');
        }

        return new ArticleStatus(self::STATUS_MAPPING[$status]);
    }

    public static function mapStatusToRecord(ArticleStatus $status): int
    {
        if (!in_array($status->getValue(), self::STATUS_MAPPING)) {
            throw new \InvalidArgumentException('Invalid status value');
        }

        return array_search($status->getValue(), self::STATUS_MAPPING);
    }

    public function toDomain(array $events = []): Article
    {
        return Article::reconstitute(
            new ArticleId($this->id),
            $this->title,
            $this->body,
            self::mapStatusToDomain($this->status),
            $this->created_at->toImmutable(),
            $this->updated_at?->toImmutable(),
            events: $events
        );
    }

    public function scopeSearch(Builder $query, array $criteria = [])
    {
        $query->when(isset($criteria['id']), function ($query) use ($criteria) {
            $query->where('id', $criteria['id']);
        });

        $query->when(isset($criteria['title']), function ($query) use ($criteria) {
            $query->where('title', 'like', "%{$criteria['title']}%");
        });

        $query->when(isset($criteria['status']), function ($query) use ($criteria) {
            $query->where('status', $criteria['status']);
        });

        $query->when(isset($criteria['created_at_range']), function ($query) use ($criteria) {
            [$start, $end] = $criteria['created_at_range'];
            $query->whereBetween('created_at', [$start, $end]);
        });

        return $query;
    }
}
