<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class TanzaniaPhoneNumber implements ValidationRule
{
    public function __construct(private readonly bool $required = true) {}

    /**
     * +255 numbers must have 12 digits. Numbers starting with 0 must have 10 digits.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($value === null || $value === '') {
            if ($this->required) {
                $fail(__('onboarding.phone_required'));
            }

            return;
        }

        if (! is_string($value)) {
            $fail(__('onboarding.phone_not_string'));

            return;
        }

        if (preg_match('/[A-Za-z]/', $value) === 1) {
            $fail(__('onboarding.phone_no_letters'));

            return;
        }

        if (preg_match('/\s/', $value) === 1) {
            $fail(__('onboarding.phone_no_spaces'));

            return;
        }

        if (str_starts_with($value, '+255')) {
            $digits = substr($value, 1);

            if (preg_match('/^\d{12}$/', $digits) !== 1) {
                $fail(__('onboarding.phone_plus255_length'));
            }

            return;
        }

        if (str_starts_with($value, '0')) {
            if (preg_match('/^0\d{9}$/', $value) !== 1) {
                $fail(__('onboarding.phone_zero_length'));
            }

            return;
        }

        $fail(__('onboarding.phone_prefix'));
    }
}
