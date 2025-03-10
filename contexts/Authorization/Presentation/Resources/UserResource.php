<?php

declare(strict_types=1);

namespace Contexts\Authorization\Presentation\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        /**
         * @var \Contexts\Authorization\Domain\UserIdentity\Models\UserIdentity $user
         */
        $user = $this->resource;

        return [
            'id' => (int) $user->getId()->getValue(),

            'email' => (string) $user->getEmail()->getValue(),
            'display_name' => (string) $user->getDisplayName(),
            'status' => (string) $user->getStatus()->getValue(),

            'created_at' => $user->getCreatedAt()->format('Y-m-d H:i:s'),
            'updated_at' => $user->getUpdatedAt()?->format('Y-m-d H:i:s'),
        ];
    }
}
