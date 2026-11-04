import { ref } from 'vue';
import english from '@/locales/en.json';
import swahili from '@/locales/sw.json';

// Add a locale here and provide matching keys in its JSON file to support another language.
export type Language = 'en' | 'sw';

type Locale = typeof english;
type TranslationTree = { [key: string]: string | TranslationTree };
type TranslationKeyOf<T> = {
    [Key in keyof T & string]: T[Key] extends string
        ? Key
        : T[Key] extends object
          ? `${Key}.${TranslationKeyOf<T[Key]>}`
          : never;
}[keyof T & string];

export type TranslationKey = TranslationKeyOf<typeof english>;

const translations: Record<Language, Locale> = {
    en: english,
    sw: swahili,
};

const storageKey = 'heyevents.language';
const localeCookie = 'heyevents_locale';

function saveLocaleCookie(nextLanguage: Language): void {
    if (typeof document === 'undefined') {
        return;
    }

    document.cookie = `${localeCookie}=${nextLanguage};path=/;max-age=31536000;SameSite=Lax`;
}

function getSavedLanguage(): Language {
    if (typeof window === 'undefined') {
        return 'en';
    }

    try {
        return window.localStorage.getItem(storageKey) === 'sw' ? 'sw' : 'en';
    } catch {
        return 'en';
    }
}

function getValue(locale: TranslationTree, key: string): string | undefined {
    let value: string | TranslationTree = locale;

    for (const segment of key.split('.')) {
        if (typeof value === 'string' || !(segment in value)) {
            return undefined;
        }

        value = value[segment];
    }

    return typeof value === 'string' ? value : undefined;
}

export const language = ref<Language>(getSavedLanguage());

if (typeof document !== 'undefined') {
    document.documentElement.lang = language.value;
    saveLocaleCookie(language.value);
}

export function setLanguage(nextLanguage: Language): void {
    language.value = nextLanguage;

    if (typeof document !== 'undefined') {
        document.documentElement.lang = nextLanguage;
        saveLocaleCookie(nextLanguage);
    }

    try {
        window.localStorage.setItem(storageKey, nextLanguage);
    } catch {
        // The selected language still applies for this visit when storage is unavailable.
    }
}

export function useLanguage() {
    return {
        language,
        currentLanguage: language,
        setLanguage,
        t: (key: TranslationKey) =>
            getValue(translations[language.value], key) ??
            getValue(english, key) ??
            key,
    };
}
