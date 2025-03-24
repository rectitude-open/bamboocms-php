<?php

declare(strict_types=1);

namespace Contexts\Authorization\Infrastructure\Records;

use App\Exceptions\SysException;
use Contexts\Authorization\Domain\Role\Models\Role;
use Contexts\Authorization\Domain\Role\Models\RoleId;
use Contexts\Authorization\Domain\Role\Models\RoleStatus;
use Contexts\Authorization\Infrastructure\RecordFactories\RoleRecordFactory;
use Contexts\Shared\Infrastructure\BaseRecord;
use Contexts\Shared\Infrastructure\Traits\HasSortingScopeTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $label
 * @property int $status
 * @property Carbon $created_at
 * @property Carbon|null $updated_at
 */
class RoleRecord extends BaseRecord
{
    use HasSortingScopeTrait;

    protected $table = 'roles';

    protected $fillable = ['label', 'status', 'created_at'];

    public const STATUS_MAPPING = [
        0 => 'suspended',
        1 => 'active',
        2 => 'deleted',
    ];

    public static function mapStatusToDomain(int $status): RoleStatus
    {
        if (! isset(self::STATUS_MAPPING[$status])) {
            throw SysException::make('Invalid status value: '.$status);
        }

        return new RoleStatus(self::STATUS_MAPPING[$status]);
    }

    public static function mapStatusToRecord(RoleStatus $status): int
    {
        if (! in_array($status->getValue(), self::STATUS_MAPPING)) {
            throw SysException::make('Invalid status value: '.$status->getValue());
        }

        return array_search($status->getValue(), self::STATUS_MAPPING);
    }

    public function toDomain(array $events = []): Role
    {
        return Role::reconstitute(
            RoleId::fromInt($this->id),
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

    protected static function newFactory(): Factory
    {
        return RoleRecordFactory::new();
    }
}
