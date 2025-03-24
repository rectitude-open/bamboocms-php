<?php

declare(strict_types=1);

namespace Contexts\Authorization\Infrastructure\Records;

use App\Exceptions\SysException;
use Contexts\Authorization\Domain\Role\Models\RoleId;
use Contexts\Authorization\Domain\UserIdentity\Models\Email;
use Contexts\Authorization\Domain\UserIdentity\Models\Password;
use Contexts\Authorization\Domain\UserIdentity\Models\RoleIdCollection;
use Contexts\Authorization\Domain\UserIdentity\Models\UserId;
use Contexts\Authorization\Domain\UserIdentity\Models\UserIdentity;
use Contexts\Authorization\Domain\UserIdentity\Models\UserStatus;
use Contexts\Authorization\Infrastructure\RecordFactories\UserRecordFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Laravel\Sanctum\HasApiTokens;

/**
 * @property int $id
 * @property string $display_name
 * @property string $email
 * @property string $password
 * @property int $status
 * @property Carbon $created_at
 * @property Carbon|null $updated_at
 */
class UserRecord extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;

    protected $table = 'users';

    protected $fillable = ['display_name', 'status', 'email', 'password', 'created_at'];

    public const STATUS_MAPPING = [
        0 => 'subspended',
        1 => 'active',
        2 => 'deleted',
    ];

    public function roles()
    {
        return $this->belongsToMany(RoleRecord::class, 'pivot_user_role', 'user_id', 'role_id');
    }

    public static function mapStatusToDomain(int $status): UserStatus
    {
        if (! isset(self::STATUS_MAPPING[$status])) {
            throw SysException::make('Invalid status value: '.$status);
        }

        return new UserStatus(self::STATUS_MAPPING[$status]);
    }

    public static function mapStatusToRecord(UserStatus $status): int
    {
        if (! in_array($status->getValue(), self::STATUS_MAPPING)) {
            throw SysException::make('Invalid status value: '.$status->getValue());
        }

        return array_search($status->getValue(), self::STATUS_MAPPING);
    }

    public function toDomain(array $events = []): UserIdentity
    {
        $rolesIds = $this->roles()->get()->pluck('id')->toArray();
        $roleIdsCollection = new RoleIdCollection(array_map(fn ($id) => RoleId::fromInt($id), $rolesIds));

        return UserIdentity::reconstitute(
            UserId::fromInt($this->id),
            new Email($this->email),
            Password::createFromHashedValue($this->password),
            $this->display_name,
            self::mapStatusToDomain($this->status),
            $this->created_at->toImmutable(),
            $this->updated_at?->toImmutable(),
            $roleIdsCollection,
            events: $events
        );
    }

    public function scopeSearch(Builder $query, array $criteria = [])
    {
        $query->when(isset($criteria['id']), function ($query) use ($criteria) {
            $query->where('id', $criteria['id']);
        });

        $query->when(isset($criteria['email']), function ($query) use ($criteria) {
            $query->where('email', 'like', "%{$criteria['email']}%");
        });

        $query->when(isset($criteria['display_name']), function ($query) use ($criteria) {
            $query->where('display_name', 'like', "%{$criteria['display_name']}%");
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
        return UserRecordFactory::new();
    }
}
