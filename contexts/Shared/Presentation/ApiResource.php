<?php

declare(strict_types=1);

namespace Contexts\Shared\Presentation;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;

class ApiResource
{
    private int $httpCode = 200;

    private array $headers = [
        'Content-Type' => 'application/json',
    ];

    private string $message = '';

    private array $additional = [];

    public function __construct(private string $type, private mixed $data = null, private ?string $resourceClass = '')
    {
    }

    public static function checkResourceClass(?string $resouceClass = '')
    {
        if (! empty($resourceClass) && ! is_subclass_of($resourceClass, JsonResource::class)) {
            throw new \InvalidArgumentException('Resource must be an instance of '.JsonResource::class);
        }
    }

    public function message(string $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function header(string $key, string $value): self
    {
        $this->headers[$key] = $value;

        return $this;
    }

    public function additional(array $additional): self
    {
        $this->additional = $additional;

        return $this;
    }

    private function prepareMessage(): array
    {
        if (empty($this->message)) {
            return [];
        }

        return ['message' => $this->message];
    }

    private function prepareSuccess(): array
    {
        return [
            'success' => $this->type === 'success' ? true : false,
        ];
    }

    private function prepareHttpCode(?int $httpCode): int
    {
        if ($httpCode) {
            return $httpCode;
        }
        if ($this->type === 'success') {
            return 200;
        }

        return 400;
    }

    private function buildDefaultResponse(int $httpCode): JsonResponse
    {
        return response()->json([
            ...$this->prepareSuccess(),
            'data' => $this->data,
            ...$this->prepareMessage(),
            ...$this->additional,
        ], $httpCode, $this->headers);
    }

    private function makeResource()
    {
        static::checkResourceClass($this->resourceClass);

        if ($this->data instanceof LengthAwarePaginator || $this->data instanceof Collection) {
            return $this->resourceClass::collection($this->data);
        }

        return new $this->resourceClass($this->data);
    }

    private function buildResourceResponse(int $httpCode): JsonResponse
    {
        return $this->makeResource()->additional([
            ...$this->prepareSuccess(),
            ...$this->prepareMessage(),
            ...$this->additional,
        ])->response()->setStatusCode($httpCode)->withHeaders($this->headers);
    }

    public function send(?int $httpCode = null): JsonResponse
    {
        $code = $this->prepareHttpCode($httpCode);

        if ($this->resourceClass) {
            return $this->buildResourceResponse($code);
        } else {
            return $this->buildDefaultResponse($code);
        }
    }
}
