<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class Username implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $pattern = '/^[a-z0-9]+(?:_[a-z0-9]+)*$/';

        if (preg_match($pattern, $value) != 1) {
            $fail('The :attribute match be start with a letter ro number with or without underscore and not end with underscore.');
        }
    }
}
