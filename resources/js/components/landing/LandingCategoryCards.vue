<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { Armchair, UtensilsCrossed, Music2, Building2, ArrowRight } from '@lucide/vue';
import { useLanguage, type TranslationKey } from '@/composables/useLanguage';

interface StandardCategory {
    id: string;
    titleKey: TranslationKey;
    badgeKey: TranslationKey;
    listingsKey: TranslationKey;
    subtitleKey: TranslationKey;
    icon: any;
    exploreUrl: string;
}

const categories: StandardCategory[] = [
    {
        id: 'rentals',
        titleKey: 'category.social.title',
        badgeKey: 'category.social.badge',
        listingsKey: 'category.social.listings',
        subtitleKey: 'category.social.subtitle',
        icon: Armchair,
        exploreUrl: '/explore?category=rentals',
    },
    {
        id: 'catering',
        titleKey: 'category.corporate.title',
        badgeKey: 'category.corporate.badge',
        listingsKey: 'category.corporate.listings',
        subtitleKey: 'category.corporate.subtitle',
        icon: UtensilsCrossed,
        exploreUrl: '/explore?category=catering',
    },
    {
        id: 'sound',
        titleKey: 'category.entertainment.title',
        badgeKey: 'category.entertainment.badge',
        listingsKey: 'category.entertainment.listings',
        subtitleKey: 'category.entertainment.subtitle',
        icon: Music2,
        exploreUrl: '/explore?category=sound-media',
    },
    {
        id: 'venues',
        titleKey: 'category.community.title',
        badgeKey: 'category.community.badge',
        listingsKey: 'category.community.listings',
        subtitleKey: 'category.community.subtitle',
        icon: Building2,
        exploreUrl: '/explore?category=venues',
    },
];

const { t } = useLanguage();
</script>

<template>
    <section id="standards" class="relative z-20 -mt-10 mb-16 sm:-mt-16">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                <Link
                    v-for="cat in categories"
                    :key="cat.id"
                    :href="cat.exploreUrl"
                    class="group relative flex flex-col justify-between rounded-3xl border border-zinc-200/90 bg-white/95 p-6 shadow-xl backdrop-blur-xl transition-all duration-300 hover:-translate-y-2 hover:border-[#ff545a] hover:shadow-2xl dark:border-zinc-700/80 dark:bg-zinc-900/95 dark:hover:border-[#ff545a] dark:hover:bg-zinc-900 dark:hover:shadow-[0_0_30px_rgba(255,84,90,0.18)]"
                >
                    <div>
                        <!-- Top Icon & Badge with High Contrast -->
                        <div class="flex items-center justify-between">
                            <div
                                class="flex size-14 items-center justify-center rounded-2xl border border-red-100 bg-red-50/90 text-[#ff545a] shadow-xs transition-transform duration-300 group-hover:scale-110 group-hover:bg-[#ff545a] group-hover:text-white dark:border-zinc-700 dark:bg-zinc-800 dark:text-red-400 dark:group-hover:bg-[#ff545a] dark:group-hover:text-white"
                            >
                                <component
                                    :is="cat.icon"
                                    class="size-6 stroke-[2]"
                                />
                            </div>
                            <span
                                class="rounded-full border border-zinc-200/60 bg-zinc-100 px-3 py-1 text-[11px] font-bold text-zinc-700 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-200"
                            >
                                {{ t(cat.badgeKey) }}
                            </span>
                        </div>

                        <!-- Category Title -->
                        <h3
                            class="mt-5 font-display text-base sm:text-lg font-extrabold tracking-tight text-zinc-900 transition group-hover:text-[#ff545a] dark:text-white"
                        >
                            {{ t(cat.titleKey) }}
                        </h3>

                        <!-- Listings / Domain Label -->
                        <p
                            class="mt-1 text-xs font-semibold text-[#ff545a] dark:text-red-400"
                        >
                            {{ t(cat.listingsKey) }}
                        </p>

                        <!-- Subtitle Detail -->
                        <p
                            class="mt-2.5 text-xs leading-relaxed text-zinc-600 dark:text-zinc-300"
                        >
                            {{ t(cat.subtitleKey) }}
                        </p>
                    </div>

                    <!-- Bottom Action Bar -->
                    <div
                        class="mt-6 flex items-center justify-between border-t border-zinc-100 pt-3 text-xs font-bold text-zinc-800 group-hover:text-[#ff545a] dark:border-zinc-800 dark:text-zinc-200 dark:group-hover:text-[#ff545a]"
                    >
                        <span>{{ t('category.explore') }}</span>
                        <ArrowRight
                            class="size-4 transition-transform duration-300 group-hover:translate-x-1"
                        />
                    </div>
                </Link>
            </div>
        </div>
    </section>
</template>
