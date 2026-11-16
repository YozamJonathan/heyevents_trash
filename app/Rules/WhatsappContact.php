<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class WhatsappContact implements ValidationRule
{
    /**
     * Accepts a Tanzanian phone number (+255 / 0 format) or a WhatsApp link
     * (wa.me, chat.whatsapp.com or whatsapp.com/channel).
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($value === null || $value === '') {
            return;
        }

        if (! is_string($value)) {
            $fail(__('onboarding.whatsapp_invalid'));

            return;
        }

        if (preg_match('#^https?://#i', $value) === 1) {
            if (preg_match('#^https?://(www\.)?(wa\.me|chat\.whatsapp\.com|whatsapp\.com/channel)/\S+$#i', $value) !== 1) {
                $fail(__('onboarding.whatsapp_link_invalid'));
            }

            return;
        }

        (new TanzaniaPhoneNumber(false))->validate($attribute, $value, $fail);
    }
}
