<script setup lang="ts">
import { UserPlus, Search, CalendarCheck2, PartyPopper, ArrowRight } from '@lucide/vue';
import { useLanguage, type TranslationKey } from '@/composables/useLanguage';

const steps = [
    {
        number: '01',
        icon: UserPlus,
        titleKey: 'steps.step1.title',
        descriptionKey: 'steps.step1.description',
        accentColor: 'text-[#ff545a] bg-red-500/10 dark:bg-red-500/20 border-red-200/80 dark:border-red-900/50',
    },
    {
        number: '02',
        icon: Search,
        titleKey: 'steps.step2.title',
        descriptionKey: 'steps.step2.description',
        accentColor: 'text-amber-500 bg-amber-500/10 dark:bg-amber-500/20 border-amber-200/80 dark:border-amber-900/50',
    },
    {
        number: '03',
        icon: CalendarCheck2,
        titleKey: 'steps.step3.title',
        descriptionKey: 'steps.step3.description',
        accentColor: 'text-blue-500 bg-blue-500/10 dark:bg-blue-500/20 border-blue-200/80 dark:border-blue-900/50',
    },
    {
        number: '04',
        icon: PartyPopper,
        titleKey: 'steps.step4.title',
        descriptionKey: 'steps.step4.description',
        accentColor: 'text-emerald-500 bg-emerald-500/10 dark:bg-emerald-500/20 border-emerald-200/80 dark:border-emerald-900/50',
    },
] satisfies {
    number: string;
    icon: any;
    titleKey: TranslationKey;
    descriptionKey: TranslationKey;
    accentColor: string;
}[];

const { t } = useLanguage();
</script>

<template>
    <section
        id="how-it-works"
        class="relative border-y border-zinc-200/50 bg-white/40 py-20 backdrop-blur-md sm:py-28 dark:border-white/10 dark:bg-zinc-950/40"
    >
        <div class="mx-auto max-w-7xl px-6 sm:px-8">
            <!-- Header -->
            <div class="mx-auto max-w-2xl text-center">
                <span
                    class="inline-flex items-center gap-1.5 rounded-full border border-red-200/80 bg-red-50/80 px-3.5 py-1 text-xs font-bold uppercase tracking-wider text-[#ff545a] dark:border-red-900/50 dark:bg-red-950/40"
                >
                    {{ t('steps.eyebrow') }}
                </span>
                <h2
                    class="mt-4 font-display text-3xl font-extrabold tracking-tight text-zinc-950 sm:text-4xl dark:text-white"
                >
                    {{ t('steps.title') }}
                </h2>
                <p class="mt-3 text-sm leading-relaxed text-zinc-600 sm:text-base dark:text-zinc-300">
                    {{ t('steps.description') }}
                </p>
            </div>

            <!-- 4 Steps Responsive Grid -->
            <div class="mt-14 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
                <div
                    v-for="step in steps"
                    :key="step.number"
                    class="group relative flex flex-col justify-between rounded-3xl border border-zinc-200/80 bg-white/95 p-7 shadow-lg backdrop-blur-xl transition-all duration-300 hover:-translate-y-2 hover:border-[#ff545a] hover:shadow-2xl dark:border-zinc-800 dark:bg-zinc-900/90 dark:hover:border-[#ff545a]"
                >
                    <div>
                        <!-- Step Number & Icon Header -->
                        <div class="flex items-center justify-between">
                            <span
                                class="font-display text-2xl font-black text-zinc-300 dark:text-zinc-700 transition group-hover:text-[#ff545a]"
                            >
                                {{ step.number }}
                            </span>
                            <div
                                class="flex size-13 items-center justify-center rounded-2xl border transition-transform duration-300 group-hover:scale-110 shadow-2xs"
                                :class="step.accentColor"
                            >
                                <component :is="step.icon" class="size-6 stroke-[2]" />
                            </div>
                        </div>

                        <!-- Step Title -->
                        <h3
                            class="mt-6 font-display text-lg font-bold text-zinc-950 transition group-hover:text-[#ff545a] dark:text-white"
                        >
                            {{ t(step.titleKey) }}
                        </h3>

                        <!-- Step Description -->
                        <p
                            class="mt-2.5 text-xs leading-relaxed text-zinc-600 dark:text-zinc-300"
                        >
                            {{ t(step.descriptionKey) }}
                        </p>
                    </div>

                    <div class="mt-6 flex items-center gap-1.5 text-[11px] font-bold text-[#ff545a] opacity-0 transition-opacity duration-200 group-hover:opacity-100">
                        <span>Step {{ step.number }}</span>
                        <ArrowRight class="size-3" />
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>
