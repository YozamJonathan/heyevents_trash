<script setup lang="ts">
import {
    Sparkles,
    ShieldCheck,
    Truck,
    BadgePercent,
    ArrowRight,
    MessageCircle,
} from '@lucide/vue';
import { Link } from '@inertiajs/vue3';
import { useLanguage } from '@/composables/useLanguage';

const { t } = useLanguage();

const cards = [
    {
        icon: ShieldCheck,
        titleKey: 'about.card1Title',
        descKey: 'about.card1Desc',
    },
    {
        icon: BadgePercent,
        titleKey: 'about.card2Title',
        descKey: 'about.card2Desc',
    },
    {
        icon: Truck,
        titleKey: 'about.card3Title',
        descKey: 'about.card3Desc',
    },
];
</script>

<template>
    <section id="about" class="relative scroll-mt-20 py-16 sm:py-24">
        <div class="mx-auto max-w-7xl px-6 sm:px-8">
            <!-- Header Section -->
            <div class="mx-auto max-w-2xl text-center">
                <div
                    class="inline-flex items-center gap-2 rounded-full border border-red-500/20 bg-red-500/10 px-3.5 py-1 text-xs font-bold uppercase tracking-wider text-[#ff545a] backdrop-blur-md dark:border-red-500/30 dark:bg-red-500/15"
                >
                    <Sparkles class="size-3.5" />
                    <span>{{ t('about.badge') }}</span>
                </div>
                <h2
                    class="mt-4 font-display text-2xl font-extrabold tracking-tight text-zinc-950 sm:text-3xl md:text-4xl dark:text-white"
                >
                    {{ t('about.title') }}
                </h2>
                <p
                    class="mt-3 text-sm leading-relaxed text-zinc-600 sm:text-base dark:text-zinc-300"
                >
                    {{ t('about.description') }}
                </p>
            </div>

            <!-- 3 Clean Value Cards -->
            <div class="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                <div
                    v-for="(card, idx) in cards"
                    :key="idx"
                    class="group relative rounded-2xl border border-zinc-200/80 bg-white/85 p-6 shadow-sm backdrop-blur-md transition hover:-translate-y-1 hover:border-[#ff545a]/50 hover:shadow-md dark:border-zinc-800/80 dark:bg-zinc-900/80"
                >
                    <div
                        class="flex size-11 items-center justify-center rounded-xl bg-[#ff545a]/10 text-[#ff545a] transition group-hover:scale-105 group-hover:bg-[#ff545a] group-hover:text-white dark:bg-[#ff545a]/20"
                    >
                        <component :is="card.icon" class="size-5" />
                    </div>
                    <h3
                        class="mt-4 font-display text-base font-bold text-zinc-950 dark:text-white"
                    >
                        {{ t(card.titleKey) }}
                    </h3>
                    <p class="mt-2 text-xs sm:text-sm leading-relaxed text-zinc-600 dark:text-zinc-400">
                        {{ t(card.descKey) }}
                    </p>
                </div>
            </div>

            <!-- Sleek Bottom Action Banner -->
            <div
                class="mt-10 flex flex-col items-center justify-between gap-4 rounded-2xl border border-zinc-200/80 bg-white/80 p-6 text-center shadow-sm backdrop-blur-md sm:flex-row sm:text-left dark:border-zinc-800 dark:bg-zinc-900/80"
            >
                <div class="max-w-xl">
                    <h4
                        class="font-display text-base font-bold text-zinc-950 sm:text-lg dark:text-white"
                    >
                        {{ t('about.ctaTitle') }}
                    </h4>
                    <p class="mt-0.5 text-xs text-zinc-600 dark:text-zinc-400">
                        {{ t('about.ctaDesc') }}
                    </p>
                </div>

                <div class="flex flex-wrap items-center justify-center gap-2.5 shrink-0">
                    <Link
                        href="/explore"
                        class="inline-flex items-center gap-1.5 rounded-xl bg-[#ff545a] px-4 py-2.5 text-xs font-bold text-white shadow-xs transition hover:bg-[#e0454b]"
                    >
                        <span>{{ t('about.exploreServices') }}</span>
                        <ArrowRight class="size-3.5" />
                    </Link>
                    <Link
                        href="/register"
                        class="inline-flex items-center gap-1.5 rounded-xl border border-zinc-200 bg-white px-4 py-2.5 text-xs font-bold text-zinc-800 shadow-2xs transition hover:bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100 dark:hover:bg-zinc-700"
                    >
                        <span>{{ t('about.joinVendor') }}</span>
                    </Link>
                    <a
                        href="https://wa.me/255763327959"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="inline-flex items-center gap-1.5 rounded-xl border border-emerald-300 bg-emerald-50 px-3.5 py-2.5 text-xs font-semibold text-emerald-800 shadow-2xs transition hover:bg-emerald-100 dark:border-emerald-800 dark:bg-emerald-950/40 dark:text-emerald-300 dark:hover:bg-emerald-950/60"
                    >
                        <MessageCircle class="size-3.5 text-emerald-600 dark:text-emerald-400" />
                        <span>WhatsApp</span>
                    </a>
                </div>
            </div>
        </div>
    </section>
</template>
