<?php

namespace App\Http\Requests\Onboarding;

use App\Models\ServiceProviderProfile;
use App\Rules\NidaNumber;
use App\Rules\TanzaniaPhoneNumber;
use App\Rules\WhatsappContact;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProviderKycRequest extends FormRequest
{
    public const INSTAGRAM_PATTERN = '#^(@?[A-Za-z0-9._]{1,30}|https?://(www\.)?instagram\.com/[A-Za-z0-9._/?=&%-]+)$#i';

    public const TIKTOK_PATTERN = '#^(@?[A-Za-z0-9._]{2,24}|https?://(www\.|vm\.|vt\.)?tiktok\.com/[A-Za-z0-9._@/?=&%-]+)$#i';

    public const PERSON_NAME_PATTERN = "/^[\pL\s'.-]+$/u";

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * Normalise raw input before validation runs.
     */
    protected function prepareForValidation(): void
    {
        $website = $this->input('social_media');

        if (is_string($website) && $website !== '' && preg_match('#^[a-z][a-z0-9+.-]*://#i', $website) !== 1) {
            $website = 'https://'.$website;
        }

        $this->merge([
            'nida_number' => is_string($this->nida_number) ? $this->nida_number : (string) $this->nida_number,
            'primary_phone' => is_string($this->primary_phone) ? $this->primary_phone : (string) $this->primary_phone,
            'secondary_phone' => $this->secondary_phone === '' ? null : $this->secondary_phone,
            'social_media' => $website === '' ? null : $website,
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $existingProfile = $this->user()?->serviceProviderProfile;

        $licenceRule = ($existingProfile && $existingProfile->business_licence_path)
            ? ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120']
            : ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'];

        return [
            'brand_name' => ['required', 'string', 'min:2', 'max:255'],
            'contact_name' => ['required', 'string', 'min:2', 'max:255', 'regex:'.self::PERSON_NAME_PATTERN],
            'nida_number' => ['required', 'string', new NidaNumber],
            'primary_phone' => ['required', 'string', new TanzaniaPhoneNumber(true)],
            'secondary_phone' => ['nullable', 'string', 'different:primary_phone', new TanzaniaPhoneNumber(false)],
            'business_licence' => $licenceRule,
            'categories' => ['required', 'array', 'min:1'],
            'categories.*' => ['string', 'distinct', Rule::in(ServiceProviderProfile::CATEGORIES)],
            'instagram' => ['nullable', 'string', 'max:255', 'regex:'.self::INSTAGRAM_PATTERN],
            'whatsapp' => ['nullable', 'string', 'max:255', new WhatsappContact],
            'tiktok' => ['nullable', 'string', 'max:255', 'regex:'.self::TIKTOK_PATTERN],
            'social_media' => ['nullable', 'string', 'max:255', 'url:http,https'],
            'description' => ['nullable', 'string', 'max:2000'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'brand_name.required' => __('onboarding.brand_name_required'),
            'brand_name.min' => __('onboarding.brand_name_min'),
            'brand_name.max' => __('onboarding.brand_name_max'),
            'contact_name.required' => __('onboarding.contact_name_required'),
            'contact_name.min' => __('onboarding.contact_name_min'),
            'contact_name.max' => __('onboarding.contact_name_max'),
            'contact_name.regex' => __('onboarding.contact_name_invalid'),
            'secondary_phone.different' => __('onboarding.secondary_phone_same'),
            'categories.required' => __('onboarding.categories_required'),
            'categories.min' => __('onboarding.categories_required'),
            'categories.*.in' => __('onboarding.categories_invalid'),
            'categories.*.distinct' => __('onboarding.categories_invalid'),
            'business_licence.required' => __('onboarding.licence_required'),
            'business_licence.file' => __('onboarding.licence_required'),
            'business_licence.mimes' => __('onboarding.licence_invalid_type'),
            'business_licence.max' => __('onboarding.licence_too_large'),
            'instagram.regex' => __('onboarding.instagram_invalid'),
            'tiktok.regex' => __('onboarding.tiktok_invalid'),
            'social_media.url' => __('onboarding.website_invalid'),
            'description.max' => __('onboarding.description_max'),
        ];
    }
}
