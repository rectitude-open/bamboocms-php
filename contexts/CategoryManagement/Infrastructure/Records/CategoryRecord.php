<?php

declare(strict_types=1);

namespace Contexts\CategoryManagement\Infrastructure\Records;

use App\Http\Models\BaseModel;
use Contexts\CategoryManagement\Domain\Models\Category;
use Contexts\CategoryManagement\Domain\Models\CategoryId;
use Contexts\CategoryManagement\Domain\Models\CategoryStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $label
 * @property string $body
 * @property int $status
 * @property Carbon $created_at
 * @property Carbon|null $updated_at
 */
class CategoryRecord extends BaseModel
{
    protected $table = 'categories';

    protected $fillable = ['label', 'status', 'created_at'];

    public const STATUS_MAPPING = [
        0 => 'subspended',
        1 => 'active',
        2 => 'deleted',
    ];

    public static function mapStatusToDomain(int $status): CategoryStatus
    {
        if (! isset(self::STATUS_MAPPING[$status])) {
            throw new \InvalidArgumentException('Invalid status value');
        }

        return new CategoryStatus(self::STATUS_MAPPING[$status]);
    }

    public static function mapStatusToRecord(CategoryStatus $status): int
    {
        if (! in_array($status->getValue(), self::STATUS_MAPPING)) {
            throw new \InvalidArgumentException('Invalid status value');
        }

        return array_search($status->getValue(), self::STATUS_MAPPING);
    }

    public function toDomain(array $events = []): Category
    {
        return Category::reconstitute(
            CategoryId::fromInt($this->id),
            $this->label,
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

        $query->when(isset($criteria['label']), function ($query) use ($criteria) {
            $query->where('label', 'like', "%{$criteria['label']}%");
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
