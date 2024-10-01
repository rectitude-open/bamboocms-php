<?php

declare(strict_types=1);

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidSorting implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $decoded = json_decode($value, true);

        if (! is_array($decoded)) {
            $fail('validation.invalid_sorting_format')->translate();

            return;
        }

        foreach ($decoded as $item) {
            if (! isset($item['id'], $item['desc']) || ! is_string($item['id']) || ! is_bool($item['desc'])) {
                $fail('validation.invalid_sorting_format_name')->translate(['name' => $attribute]);

                return;
            }
        }
    }
}
