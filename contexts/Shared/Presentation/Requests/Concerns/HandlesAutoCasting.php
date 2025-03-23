<?php

declare(strict_types=1);

namespace Contexts\Shared\Presentation\Requests\Concerns;

trait HandlesAutoCasting
{
    protected function autoCast(): void
    {
        $casts = $this->inferCastsFromRules();

        $this->merge(
            collect($this->all())
                ->mapWithKeys(fn ($value, $key) => [
                    $key => $this->castValue(
                        $key,
                        $value,
                        $casts[$key] ?? null
                    ),
                ])
                ->toArray()
        );
    }

    private function inferCastsFromRules(): array
    {
        return collect($this->rules())
            ->mapWithKeys(fn ($rules, $key) => [
                $key => $this->parseTypeFromRules((array) $rules),
            ])
            ->filter()
            ->toArray();
    }

    private function parseTypeFromRules(array $rules): ?string
    {
        foreach ($rules as $rule) {
            if ($type = $this->matchPrimitiveType($rule)) {
                return $type;
            }
        }

        return null;
    }

    private function matchPrimitiveType($rule): ?string
    {
        return match (true) {
            $rule === 'integer' => 'int',
            $rule === 'boolean' => 'bool',
            $rule === 'numeric' => 'float',
            $rule === 'array' => 'array',
            default => null
        };
    }

    private function castValue(string $key, mixed $value, ?string $type): mixed
    {
        if ($type === null || $value === null) {
            return $value;
        }

        return match ($type) {
            'int' => (int) $value,
            'float' => (float) $value,
            'bool' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            'array' => (array) $value,
            default => $value
        };
    }
}
