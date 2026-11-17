<script setup lang="ts">
import { useLanguage, type TranslationKey } from '@/composables/useLanguage';
import { Star, ShieldCheck, Quote, MapPin } from '@lucide/vue';

interface ReviewItem {
    name: string;
    initials: string;
    location: string;
    avatarGradient: string;
    roleKey: TranslationKey;
    eventTypeKey: TranslationKey;
    contentKey: TranslationKey;
}

const reviews: ReviewItem[] = [
    {
        name: 'Amina Mtwana',
        initials: 'AM',
        location: 'Dar es Salaam',
        avatarGradient: 'from-[#ff545a] to-rose-400',
        roleKey: 'review.1.role',
        eventTypeKey: 'review.1.category',
        contentKey: 'review.1.content',
    },
    {
        name: 'Baraka Makene',
        initials: 'BM',
        location: 'Arusha & Dodoma',
        avatarGradient: 'from-amber-500 to-orange-500',
        roleKey: 'review.2.role',
        eventTypeKey: 'review.2.category',
        contentKey: 'review.2.content',
    },
    {
        name: 'Neema Kimaro',
        initials: 'NK',
        location: 'Mwanza & Zanzibar',
        avatarGradient: 'from-indigo-500 to-sky-500',
        roleKey: 'review.3.role',
        eventTypeKey: 'review.3.category',
        contentKey: 'review.3.content',
    },
];

const { t } = useLanguage();
</script>

<template>
    <section
        id="reviews"
        class="relative border-t border-zinc-200/60 bg-slate-50/70 py-14 sm:py-16 backdrop-blur-md dark:border-zinc-800/80 dark:bg-zinc-950/70"
    >
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <!-- Header Section -->
            <div class="mx-auto max-w-xl text-center">
                <div
                    class="inline-flex items-center gap-1.5 rounded-full border border-red-500/20 bg-red-500/10 px-3 py-0.5 text-[10px] font-bold uppercase tracking-wider text-[#ff545a] dark:border-red-500/30 dark:bg-red-500/20"
                >
                    <span class="size-1.5 rounded-full bg-[#ff545a] animate-pulse"></span>
                    <span>{{ t('reviews.eyebrow') }}</span>
                </div>
                <h2
                    class="mt-2 font-display text-xl sm:text-2xl lg:text-3xl font-black tracking-tight text-zinc-950 dark:text-white"
                >
                    {{ t('reviews.title') }}
                </h2>
                <p class="mt-2 text-xs sm:text-sm text-zinc-600 dark:text-zinc-400">
                    {{ t('reviews.subtitle') }}
                </p>
            </div>

            <!-- Reviews Grid (3 columns on desktop, slightly more compact UI) -->
            <div class="mt-8 grid grid-cols-1 gap-4 md:grid-cols-3 lg:gap-5">
                <div
                    v-for="(rev, idx) in reviews"
                    :key="idx"
                    class="group relative flex flex-col justify-between rounded-2xl border border-zinc-200/90 bg-white/95 p-5 sm:p-6 shadow-xs backdrop-blur-xl transition-all duration-300 hover:-translate-y-1 hover:border-[#ff545a]/50 hover:shadow-lg hover:shadow-[#ff545a]/5 dark:border-zinc-800/80 dark:bg-zinc-900/90 dark:hover:border-[#ff545a]/50 dark:hover:shadow-black/40"
                >
                    <!-- Background Watermark Quote -->
                    <Quote
                        class="pointer-events-none absolute -right-2 -top-2 size-16 text-zinc-100 opacity-60 transition-all duration-300 stroke-[1.2] group-hover:scale-105 group-hover:text-red-500/10 group-hover:opacity-100 dark:text-zinc-800/40 dark:group-hover:text-[#ff545a]/10"
                    />

                    <div>
                        <!-- Top Bar: 5 Stars + Verified Booking Badge -->
                        <div class="relative z-10 flex items-center justify-between">
                            <div class="flex items-center gap-0.5">
                                <Star
                                    v-for="s in 5"
                                    :key="s"
                                    class="size-3.5 fill-amber-400 text-amber-400"
                                />
                            </div>
                            <span
                                class="inline-flex items-center gap-1 rounded-full bg-emerald-500/10 px-2 py-0.5 text-[10px] font-bold text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400"
                            >
                                <ShieldCheck class="size-3 stroke-[2.5]" />
                                <span>{{ t('reviews.verifiedBooking') }}</span>
                            </span>
                        </div>

                        <!-- Testimonial Quote -->
                        <p
                            class="relative z-10 mt-3.5 font-sans text-xs sm:text-[13px] leading-relaxed font-normal text-zinc-600 dark:text-zinc-300"
                        >
                            "{{ t(rev.contentKey) }}"
                        </p>
                    </div>

                    <!-- Card Bottom Details -->
                    <div
                        class="relative z-10 mt-4 border-t border-zinc-100 pt-3.5 dark:border-zinc-800"
                    >
                        <!-- Category / Equipment Hired Tag -->
                        <div class="mb-3">
                            <span
                                class="inline-flex items-center gap-1.5 rounded-md bg-zinc-100 px-2 py-0.5 text-[10px] font-semibold text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300"
                            >
                                <span class="size-1 rounded-full bg-[#ff545a]"></span>
                                <span>{{ t(rev.eventTypeKey) }}</span>
                            </span>
                        </div>

                        <!-- Host Profile -->
                        <div class="flex items-center gap-2.5">
                            <div
                                :class="[
                                    'flex size-9 shrink-0 items-center justify-center rounded-xl bg-gradient-to-tr text-[11px] font-extrabold text-white shadow-xs',
                                    rev.avatarGradient,
                                ]"
                            >
                                {{ rev.initials }}
                            </div>
                            <div class="min-w-0 flex-1">
                                <h4
                                    class="truncate text-xs font-bold text-zinc-950 dark:text-white"
                                >
                                    {{ rev.name }}
                                </h4>
                                <div
                                    class="flex flex-wrap items-center gap-1 text-[10px] text-zinc-500 dark:text-zinc-400"
                                >
                                    <span class="truncate font-medium">{{ t(rev.roleKey) }}</span>
                                    <span>•</span>
                                    <span class="inline-flex items-center gap-0.5 font-medium text-zinc-600 dark:text-zinc-300">
                                        <MapPin class="size-2.5 text-[#ff545a]" />
                                        {{ rev.location }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>
