<?php

declare(strict_types=1);

namespace Contexts\Authorization\Domain\UserIdentity\Models;

use App\Exceptions\BizException;
use Carbon\CarbonImmutable;
use Contexts\Authorization\Domain\Role\Models\RoleId;
use Contexts\Authorization\Domain\UserIdentity\Events\PasswordChangedEvent;
use Contexts\Authorization\Domain\UserIdentity\Events\RoleAssignedEvent;
use Contexts\Authorization\Domain\UserIdentity\Events\RoleRemovedEvent;
use Contexts\Shared\Domain\BaseDomainModel;

class UserIdentity extends BaseDomainModel
{
    private RoleIdCollection $roleIdCollection;

    private function __construct(
        public UserId $id,
        private Email $email,
        private Password $password,
        private string $display_name,
        private UserStatus $status,
        private ?CarbonImmutable $created_at = null,
        private ?CarbonImmutable $updated_at = null
    ) {
        $this->created_at = $created_at ?? CarbonImmutable::now();
        $this->roleIdCollection = new RoleIdCollection;
    }

    public function hasAnyRole(RoleIdCollection $roleIds): bool
    {
        return $this->roleIdCollection->intersect($roleIds)->isNotEmpty();
    }

    public function syncRoles(RoleIdCollection $newRoles): void
    {
        $rolesToAdd = $newRoles->diff($this->roleIdCollection);

        $rolesToRemove = $this->roleIdCollection->diff($newRoles);

        $rolesToAdd->map(function (RoleId $roleId) {
            $this->recordEvent(new RoleAssignedEvent($this->id, $roleId));
        });

        $rolesToRemove->map(function (RoleId $roleId) {
            $this->recordEvent(new RoleRemovedEvent($this->id, $roleId));
        });

        $this->roleIdCollection = $newRoles;
    }

    public function authenticate(string $plainTextPassword): void
    {
        if ($this->status->equals(UserStatus::deleted())) {
            throw BizException::make('User is not active')->logContext($this->getUserSummary());
        }

        if (! $this->password->verify($plainTextPassword)) {
            throw BizException::make('Password is not correct')->logContext($this->getUserSummary());
        }
    }

    public function changePassword(string $newPassword): void
    {
        $this->password = Password::createFromPlainText($newPassword);
        $this->recordEvent(new PasswordChangedEvent($this->id));
    }

    protected function getUserSummary(): array
    {
        return [
            'id' => $this->id->getValue(),
            'email' => $this->email->getValue(),
            'display_name' => $this->display_name,
            'status' => $this->status->getValue(),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }

    public function modify(
        ?Email $newEmail,
        ?string $newDisplayName,
        ?UserStatus $newStatus,
        ?CarbonImmutable $newCreatedAt = null,
    ) {
        $this->email = $newEmail ?? $this->email;
        $this->display_name = $newDisplayName ?? $this->display_name;
        if ($newStatus && ! $this->status->equals($newStatus)) {
            $this->transitionStatus($newStatus);
        }
        $this->created_at = $newCreatedAt ?? $this->created_at;
    }

    public static function reconstitute(
        UserId $id,
        Email $email,
        Password $password,
        string $display_name,
        UserStatus $status,
        ?CarbonImmutable $created_at = null,
        ?CarbonImmutable $updated_at = null,
        ?RoleIdCollection $roleIdCollection = null,
        array $events = []
    ): self {
        $user = new self($id, $email, $password, $display_name, $status, $created_at, $updated_at);
        $user->roleIdCollection = $roleIdCollection ?? new RoleIdCollection;

        foreach ($events as $event) {
            $user->recordEvent($event);
        }

        return $user;
    }

    public static function createFromFactory(
        UserId $id,
        Email $email,
        Password $password,
        string $display_name,
        UserStatus $status,
        ?CarbonImmutable $created_at = null,
        ?CarbonImmutable $updated_at = null
    ): self {
        $user = new self($id, $email, $password, $display_name, $status, $created_at, $updated_at);

        return $user;
    }

    public function subspend()
    {
        $this->transitionStatus(UserStatus::subspended());
    }

    public function delete()
    {
        $this->transitionStatus(UserStatus::deleted());
    }

    private function transitionStatus(UserStatus $targetStatus): void
    {
        $this->status = $this->status->transitionTo($targetStatus);

        match ($this->status->getValue()) {
            default => null,
        };
    }

    public function getId(): UserId
    {
        return $this->id;
    }

    public function getEmail(): Email
    {
        return $this->email;
    }

    public function getPassword(): Password
    {
        return $this->password;
    }

    public function getDisplayName(): string
    {
        return $this->display_name;
    }

    public function getStatus(): UserStatus
    {
        return $this->status;
    }

    public function getRoleIdCollection(): RoleIdCollection
    {
        return $this->roleIdCollection;
    }

    public function getCreatedAt(): CarbonImmutable
    {
        return $this->created_at;
    }

    public function getUpdatedAt(): ?CarbonImmutable
    {
        return $this->updated_at;
    }
}
