<?php

declare(strict_types=1);

namespace Contexts\CategoryManagement\Presentation\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        /**
         * @var \Contexts\CategoryManagement\Domain\Models\Category $category
         */
        $category = $this->resource;

        return [
            'id' => (int) $category->getId()->getValue(),

            'label' => (string) $category->getLabel(),
            'status' => (string) $category->getStatus()->getValue(),

            'created_at' => $category->getCreatedAt()->format('Y-m-d H:i:s'),
            'updated_at' => $category->getUpdatedAt()?->format('Y-m-d H:i:s'),
        ];
    }
}
