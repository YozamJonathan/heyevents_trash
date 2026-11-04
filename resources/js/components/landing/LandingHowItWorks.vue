<script setup lang="ts">
import { Compass, CalendarCheck, QrCode } from '@lucide/vue';
import { useLanguage, type TranslationKey } from '@/composables/useLanguage';

const steps = [
    {
        icon: Compass,
        titleKey: 'steps.select.title',
        descriptionKey: 'steps.select.description',
    },
    {
        icon: CalendarCheck,
        titleKey: 'steps.plan.title',
        descriptionKey: 'steps.plan.description',
    },
    {
        icon: QrCode,
        titleKey: 'steps.execute.title',
        descriptionKey: 'steps.execute.description',
    },
] satisfies {
    icon: typeof Compass;
    titleKey: TranslationKey;
    descriptionKey: TranslationKey;
}[];

const { t } = useLanguage();
</script>

<template>
    <section
        id="how-it-works"
        class="relative border-y border-zinc-200/50 bg-white/40 py-16 backdrop-blur-md sm:py-24 dark:border-white/10 dark:bg-zinc-950/40"
    >
        <div class="mx-auto max-w-7xl px-6 sm:px-8">
            <!-- Header -->
            <div class="mx-auto max-w-xl text-center">
                <span
                    class="text-xs font-bold tracking-wider text-[#ff545a] uppercase"
                >
                    {{ t('steps.eyebrow') }}
                </span>
                <h2
                    class="mt-2 font-sans text-2xl font-black tracking-wider text-zinc-900 uppercase sm:text-3xl dark:text-white"
                >
                    {{ t('steps.title') }}
                </h2>
                <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-300">
                    {{ t('steps.description') }}
                </p>
            </div>

            <!-- Steps Grid with Glass Cards -->
            <div class="mt-12 grid grid-cols-1 gap-8 md:grid-cols-3">
                <div
                    v-for="(step, index) in steps"
                    :key="index"
                    class="relative flex flex-col items-center rounded-2xl border border-zinc-200/80 bg-white/90 p-8 text-center shadow-md backdrop-blur-xl transition-all duration-300 hover:-translate-y-1.5 hover:shadow-xl dark:border-zinc-700/80 dark:bg-zinc-900/90 dark:hover:border-zinc-600"
                >
                    <span
                        class="mb-2 text-xs font-bold text-zinc-400 dark:text-zinc-400"
                    >
                        {{ t('steps.label') }}
                        {{ String(index + 1).padStart(2, '0') }}
                    </span>

                    <div
                        class="mb-4 flex size-14 items-center justify-center rounded-2xl border border-red-100 bg-red-50 text-[#ff545a] dark:border-zinc-700 dark:bg-zinc-800 dark:text-red-400"
                    >
                        <component :is="step.icon" class="size-6 stroke-[2]" />
                    </div>

                    <h3
                        class="font-sans text-base font-bold text-zinc-900 dark:text-white"
                    >
                        {{ t(step.titleKey) }}
                    </h3>

                    <p
                        class="mt-2 max-w-xs text-xs leading-relaxed text-zinc-600 dark:text-zinc-300"
                    >
                        {{ t(step.descriptionKey) }}
                    </p>
                </div>
            </div>
        </div>
    </section>
</template>
