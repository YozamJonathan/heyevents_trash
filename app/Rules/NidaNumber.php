<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class NidaNumber implements ValidationRule
{
    /**
     * NIDA is stored and validated as a 20-character digit string.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value)) {
            $fail(__('onboarding.nida_not_string'));

            return;
        }

        if ($value === '') {
            $fail(__('onboarding.nida_required'));

            return;
        }

        if (preg_match('/[A-Za-z]/', $value) === 1) {
            $fail(__('onboarding.nida_no_letters'));

            return;
        }

        if (preg_match('/\s/', $value) === 1) {
            $fail(__('onboarding.nida_no_spaces'));

            return;
        }

        if (preg_match('/[^0-9]/', $value) === 1) {
            $fail(__('onboarding.nida_no_special'));

            return;
        }

        if (strlen($value) !== 20) {
            $fail(__('onboarding.nida_exact_length'));
        }
    }
}
