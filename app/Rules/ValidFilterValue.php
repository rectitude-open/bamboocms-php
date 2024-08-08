<?php

declare(strict_types=1);

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\ValidationRule;
use Validator;

class ValidFilterValue implements DataAwareRule, ValidationRule
{
    protected $data = [];

    public function __construct(private array $filterRules = []) {}

    public function setData(array $data): static
    {
        $this->data = $data;

        return $this;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $parts = explode('.', $attribute);
        $filterIndex = $parts[1];
        $filterId = $this->data['filters'][$filterIndex]['id'] ?? null;

        if ($filterId && isset($this->filterRules[$filterId])) {
            $validator = Validator::make(
                ['value' => $value],
                ['value' => $this->filterRules[$filterId]],
                [],
                ['value' => $filterId]
            );

            if ($validator->fails()) {
                foreach ($validator->errors()->all() as $message) {
                    $fail($message);
                }
            }
        } else {
            $fail('validation.invalid_filter')->translate(['name' => $filterId]);
        }
    }
}
