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
</script>

<template>
    <div
        role="radiogroup"
        :aria-label="t('language.label')"
        class="relative inline-flex items-center rounded-full border border-zinc-200/80 bg-zinc-200/60 p-0.5 shadow-inner backdrop-blur-xs transition-colors dark:border-zinc-700/80 dark:bg-zinc-800/80"
        :class="compact ? 'h-8' : 'h-9'"
    >
        <!-- Sliding Indicator Pill -->
        <span
            aria-hidden="true"
            class="absolute top-0.5 bottom-0.5 rounded-full bg-white shadow-xs transition-transform duration-200 ease-out dark:border dark:border-zinc-700/50 dark:bg-zinc-900 dark:shadow-md"
            :class="[
                'w-[calc(50%-2px)]',
                language === 'sw' ? 'translate-x-full' : 'translate-x-0',
            ]"
            :style="{ left: '2px' }"
        />

        <!-- Radio Button EN -->
        <button
            type="button"
            role="radio"
            :aria-checked="language === 'en'"
            @click="setLanguage('en')"
            :title="t('language.english')"
            :class="[
                'relative z-10 flex min-w-[34px] items-center justify-center rounded-full font-black tracking-wider uppercase transition-colors duration-150 focus:outline-hidden sm:min-w-[38px]',
                compact ? 'h-7 px-2 text-[11px]' : 'h-8 px-2.5 text-xs',
                language === 'en'
                    ? 'text-zinc-950 dark:text-white'
                    : 'text-zinc-500 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-zinc-200',
            ]"
        >
            EN
        </button>

        <!-- Radio Button SW -->
        <button
            type="button"
            role="radio"
            :aria-checked="language === 'sw'"
            @click="setLanguage('sw')"
            :title="t('language.swahili')"
            :class="[
                'relative z-10 flex min-w-[34px] items-center justify-center rounded-full font-black tracking-wider uppercase transition-colors duration-150 focus:outline-hidden sm:min-w-[38px]',
                compact ? 'h-7 px-2 text-[11px]' : 'h-8 px-2.5 text-xs',
                language === 'sw'
                    ? 'text-zinc-950 dark:text-white'
                    : 'text-zinc-500 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-zinc-200',
            ]"
        >
            SW
        </button>
    </div>
</template>
