export const NIDA_LENGTH = 20;
export const PHONE_PLUS255_DIGITS = 12;
export const PHONE_ZERO_DIGITS = 10;
export const LICENCE_MAX_BYTES = 5 * 1024 * 1024;
export const LICENCE_TYPES = [
    'application/pdf',
    'image/jpeg',
    'image/jpg',
    'image/png',
];

export type FieldErrorKey =
    | 'brandNameRequired'
    | 'contactNameRequired'
    | 'nidaRequired'
    | 'nidaNoLetters'
    | 'nidaNoSpaces'
    | 'nidaNoSpecial'
    | 'nidaExactLength'
    | 'phoneRequired'
    | 'phoneNoLetters'
    | 'phoneNoSpaces'
    | 'phoneNoSpecial'
    | 'phonePlus255Length'
    | 'phoneZeroLength'
    | 'phonePrefix'
    | 'categoriesRequired'
    | 'licenceRequired'
    | 'licenceTooLarge'
    | 'licenceInvalidType'
    | 'brandNameMin'
    | 'contactNameMin'
    | 'contactNameInvalid'
    | 'nameTooLong'
    | 'secondaryPhoneSame'
    | 'instagramInvalid'
    | 'tiktokInvalid'
    | 'whatsappLinkInvalid'
    | 'websiteInvalid'
    | 'descriptionTooLong';

export type FieldError = {
    key: FieldErrorKey;
    count?: number;
    expected?: number;
};

export function nidaError(
    value: string,
    required: boolean,
): FieldError | null {
    const raw = value ?? '';

    if (raw.trim() === '') {
        return required ? { key: 'nidaRequired' } : null;
    }

    if (/[A-Za-z]/.test(raw)) {
        return { key: 'nidaNoLetters' };
    }

    if (/\s/.test(raw)) {
        return { key: 'nidaNoSpaces' };
    }

    if (/[^0-9]/.test(raw)) {
        return { key: 'nidaNoSpecial' };
    }

    if (raw.length !== NIDA_LENGTH) {
        return {
            key: 'nidaExactLength',
            count: raw.length,
            expected: NIDA_LENGTH,
        };
    }

    return null;
}

export function phoneError(
    value: string,
    required: boolean,
): FieldError | null {
    const raw = value ?? '';

    if (raw.trim() === '') {
        return required ? { key: 'phoneRequired' } : null;
    }

    if (/[A-Za-z]/.test(raw)) {
        return { key: 'phoneNoLetters' };
    }

    if (/\s/.test(raw)) {
        return { key: 'phoneNoSpaces' };
    }

    if (raw.startsWith('+255')) {
        const digits = raw.slice(1);

        if (/[^0-9]/.test(digits)) {
            return { key: 'phoneNoSpecial' };
        }

        if (digits.length !== PHONE_PLUS255_DIGITS) {
            return {
                key: 'phonePlus255Length',
                count: digits.length,
                expected: PHONE_PLUS255_DIGITS,
            };
        }

        return null;
    }

    if (raw.startsWith('0')) {
        if (/[^0-9]/.test(raw)) {
            return { key: 'phoneNoSpecial' };
        }

        if (raw.length !== PHONE_ZERO_DIGITS) {
            return {
                key: 'phoneZeroLength',
                count: raw.length,
                expected: PHONE_ZERO_DIGITS,
            };
        }

        return null;
    }

    if (/[^0-9+]/.test(raw) || (raw.includes('+') && !raw.startsWith('+'))) {
        return { key: 'phoneNoSpecial' };
    }

    return { key: 'phonePrefix' };
}

export function licenceError(file: File | null, hasExisting: boolean): FieldError | null {
    if (!file) {
        return hasExisting ? null : { key: 'licenceRequired' };
    }

    if (file.size > LICENCE_MAX_BYTES) {
        return { key: 'licenceTooLarge' };
    }

    const name = file.name.toLowerCase();
    const typeOk =
        LICENCE_TYPES.includes(file.type) ||
        name.endsWith('.pdf') ||
        name.endsWith('.jpg') ||
        name.endsWith('.jpeg') ||
        name.endsWith('.png');

    if (!typeOk) {
        return { key: 'licenceInvalidType' };
    }

    return null;
}

/*
 * The patterns below mirror StoreProviderKycRequest on the backend so the
 * slider can show errors before the user submits.
 */
export const NAME_MAX_LENGTH = 255;
export const DESCRIPTION_MAX_LENGTH = 2000;
const PERSON_NAME_PATTERN = /^[\p{L}\s'.-]+$/u;
const INSTAGRAM_PATTERN =
    /^(@?[A-Za-z0-9._]{1,30}|https?:\/\/(www\.)?instagram\.com\/[A-Za-z0-9._/?=&%-]+)$/i;
const TIKTOK_PATTERN =
    /^(@?[A-Za-z0-9._]{2,24}|https?:\/\/(www\.|vm\.|vt\.)?tiktok\.com\/[A-Za-z0-9._@/?=&%-]+)$/i;
const WHATSAPP_LINK_PATTERN =
    /^https?:\/\/(www\.)?(wa\.me|chat\.whatsapp\.com|whatsapp\.com\/channel)\/\S+$/i;

export function brandNameError(value: string, required: boolean): FieldError | null {
    const raw = (value ?? '').trim();

    if (raw === '') {
        return required ? { key: 'brandNameRequired' } : null;
    }

    if (raw.length < 2) {
        return { key: 'brandNameMin' };
    }

    if (raw.length > NAME_MAX_LENGTH) {
        return { key: 'nameTooLong', expected: NAME_MAX_LENGTH };
    }

    return null;
}

export function contactNameError(value: string, required: boolean): FieldError | null {
    const raw = (value ?? '').trim();

    if (raw === '') {
        return required ? { key: 'contactNameRequired' } : null;
    }

    if (raw.length < 2) {
        return { key: 'contactNameMin' };
    }

    if (raw.length > NAME_MAX_LENGTH) {
        return { key: 'nameTooLong', expected: NAME_MAX_LENGTH };
    }

    if (!PERSON_NAME_PATTERN.test(raw)) {
        return { key: 'contactNameInvalid' };
    }

    return null;
}

export function secondaryPhoneError(secondary: string, primary: string): FieldError | null {
    const base = phoneError(secondary, false);

    if (base) {
        return base;
    }

    const raw = (secondary ?? '').trim();

    if (raw !== '' && raw === (primary ?? '').trim()) {
        return { key: 'secondaryPhoneSame' };
    }

    return null;
}

export function instagramError(value: string): FieldError | null {
    const raw = (value ?? '').trim();

    return raw === '' || INSTAGRAM_PATTERN.test(raw) ? null : { key: 'instagramInvalid' };
}

export function tiktokError(value: string): FieldError | null {
    const raw = (value ?? '').trim();

    return raw === '' || TIKTOK_PATTERN.test(raw) ? null : { key: 'tiktokInvalid' };
}

export function whatsappError(value: string): FieldError | null {
    const raw = (value ?? '').trim();

    if (raw === '') {
        return null;
    }

    if (/^https?:\/\//i.test(raw)) {
        return WHATSAPP_LINK_PATTERN.test(raw) ? null : { key: 'whatsappLinkInvalid' };
    }

    return phoneError(raw, false);
}

/**
 * Adds https:// when the user types a bare domain (e.g. mybusiness.co.tz).
 */
export function normaliseWebsite(value: string): string {
    const raw = (value ?? '').trim();

    if (raw === '' || /^[a-z][a-z0-9+.-]*:\/\//i.test(raw)) {
        return raw;
    }

    return `https://${raw}`;
}

export function websiteError(value: string): FieldError | null {
    const raw = normaliseWebsite(value);

    if (raw === '') {
        return null;
    }

    try {
        const url = new URL(raw);
        const validProtocol = url.protocol === 'http:' || url.protocol === 'https:';
        const validHost = url.hostname.includes('.') && !/\s/.test(raw);

        return validProtocol && validHost && raw.length <= NAME_MAX_LENGTH
            ? null
            : { key: 'websiteInvalid' };
    } catch {
        return { key: 'websiteInvalid' };
    }
}

export function descriptionError(value: string): FieldError | null {
    const length = (value ?? '').length;

    return length > DESCRIPTION_MAX_LENGTH
        ? { key: 'descriptionTooLong', count: length, expected: DESCRIPTION_MAX_LENGTH }
        : null;
}

/**
 * Maps a backend validation error key to the slider step that owns the field,
 * so a failed submission jumps the user back to the right step.
 */
export function stepForField(field: string): number {
    const root = field.split('.')[0];

    if (['brand_name', 'contact_name', 'nida_number', 'primary_phone', 'secondary_phone'].includes(root)) {
        return 1;
    }

    if (root === 'categories') {
        return 2;
    }

    if (['instagram', 'whatsapp', 'tiktok', 'social_media', 'description'].includes(root)) {
        return 3;
    }

    if (root === 'business_licence') {
        return 4;
    }

    return 5;
}

export function phoneDigitProgress(value: string): {
    digits: number;
    expected: number | null;
    prefix: '+255' | '0' | null;
} {
    const raw = value ?? '';

    if (raw.startsWith('+255')) {
        return {
            digits: raw.slice(1).replace(/\D/g, '').length,
            expected: PHONE_PLUS255_DIGITS,
            prefix: '+255',
        };
    }

    if (raw.startsWith('0')) {
        return {
            digits: raw.replace(/\D/g, '').length,
            expected: PHONE_ZERO_DIGITS,
            prefix: '0',
        };
    }

    return { digits: raw.replace(/\D/g, '').length, expected: null, prefix: null };
}
