<script setup lang="ts">
import { useLanguage } from '@/composables/useLanguage';

withDefaults(
    defineProps<{
        compact?: boolean;
    }>(),
    {
        compact: false,
    },
);

const { language, setLanguage, t } = useLanguage();

function changeLanguage(event: Event): void {
    setLanguage(
        (event.target as HTMLSelectElement).value === 'sw' ? 'sw' : 'en',
    );
}
</script>

<template>
    <select
        :value="language"
        :aria-label="t('language.label')"
        @change="changeLanguage"
        :class="[
            'rounded-lg border border-zinc-200 bg-white text-xs font-semibold text-zinc-800 shadow-xs dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100',
            compact ? 'h-9 px-2.5' : 'h-10 px-3',
        ]"
    >
        <option value="en">{{ t('language.english') }}</option>
        <option value="sw">{{ t('language.swahili') }}</option>
    </select>
</template>
