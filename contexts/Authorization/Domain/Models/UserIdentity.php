<?php

declare(strict_types=1);

namespace Contexts\Authorization\Domain\Models;

use App\Exceptions\BizException;
use App\Http\DomainModel\BaseDomainModel;
use Carbon\CarbonImmutable;
use Contexts\Authorization\Domain\Events\PasswordChangedEvent;
use Contexts\Authorization\Domain\Events\UserCreatedEvent;

class UserIdentity extends BaseDomainModel
{
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
        array $events = []
    ): self {
        $user = new self($id, $email, $password, $display_name, $status, $created_at, $updated_at);
        foreach ($events as $event) {
            $user->recordEvent($event);
        }

        return $user;
    }

    public static function create(
        UserId $id,
        Email $email,
        Password $password,
        string $display_name,
        ?CarbonImmutable $created_at = null,
        ?CarbonImmutable $updated_at = null
    ): self {
        $user = new self($id, $email, $password, $display_name, UserStatus::active(), $created_at, $updated_at);
        $user->recordEvent(new UserCreatedEvent($user->id));

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

    public function getCreatedAt(): CarbonImmutable
    {
        return $this->created_at;
    }

    public function getUpdatedAt(): ?CarbonImmutable
    {
        return $this->updated_at;
    }
}
