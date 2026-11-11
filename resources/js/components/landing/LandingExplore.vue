<script setup lang="ts">
import { ref, computed } from 'vue';
import { MapPin, Users } from '@lucide/vue';
import { useLanguage, type TranslationKey } from '@/composables/useLanguage';

interface ListingItem {
    id: number;
    title: string;
    categoryKey: TranslationKey;
    standard: 'social' | 'corporate' | 'entertainment' | 'community';
    image: string;
    statusKey: TranslationKey;
    location: string;
    capacityKey: TranslationKey;
    descriptionKey: TranslationKey;
}

const activeFilter = ref('all');
const { t } = useLanguage();

const filterTabs = computed(() => [
    { id: 'all', label: t('explore.all') },
    { id: 'social', label: t('explore.social') },
    { id: 'corporate', label: t('explore.corporate') },
    { id: 'entertainment', label: t('explore.entertainment') },
    { id: 'community', label: t('explore.community') },
]);

const listings = ref<ListingItem[]>([
    {
        id: 1,
        title: 'Amani & Zawadi Wedding Gala',
        categoryKey: 'listing.1.category',
        standard: 'social',
        image: '/images/card-resort.jpg',
        statusKey: 'listing.1.status',
        location: 'Serena Grand Ballroom',
        capacityKey: 'listing.1.capacity',
        descriptionKey: 'listing.1.description',
    },
    {
        id: 2,
        title: 'East Africa Tech Summit 2026',
        categoryKey: 'listing.2.category',
        standard: 'corporate',
        image: '/images/hero-dar-night.jpg',
        statusKey: 'listing.2.status',
        location: 'JNICC • Dar es Salaam',
        capacityKey: 'listing.2.capacity',
        descriptionKey: 'listing.2.description',
    },
    {
        id: 3,
        title: 'Safari Beats Music Festival',
        categoryKey: 'listing.3.category',
        standard: 'entertainment',
        image: '/images/card-alpine.jpg',
        statusKey: 'listing.3.status',
        location: 'National Stadium Park',
        capacityKey: 'listing.3.capacity',
        descriptionKey: 'listing.3.description',
    },
    {
        id: 4,
        title: 'Education For All Benefit Gala',
        categoryKey: 'listing.4.category',
        standard: 'community',
        image: '/images/card-resort.jpg',
        statusKey: 'listing.4.status',
        location: 'Civic Center Grand Hall',
        capacityKey: 'listing.4.capacity',
        descriptionKey: 'listing.4.description',
    },
]);

const filteredListings = computed(() => {
    if (activeFilter.value === 'all') {
        return listings.value;
    }
    return listings.value.filter(
        (item) => item.standard === activeFilter.value,
    );
});
</script>

<template>
    <section id="explore" class="relative overflow-hidden py-16 sm:py-24">
        <div class="relative z-10 mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <!-- Header & Filter Tabs -->
            <div
                class="flex flex-col justify-between gap-6 pb-2 md:flex-row md:items-end"
            >
                <div>
                    <span
                        class="text-xs font-bold tracking-widest text-[#ff545a] uppercase"
                    >
                        {{ t('explore.eyebrow') }}
                    </span>
                    <h2
                        class="mt-1 font-display text-3xl sm:text-4xl font-extrabold tracking-tight text-zinc-950 dark:text-white"
                    >
                        {{ t('explore.title') }}
                    </h2>
                </div>

                <!-- Filter Tabs -->
                <div
                    class="flex flex-wrap items-center gap-1.5 rounded-xl border border-zinc-200/80 bg-white/90 p-1 shadow-md backdrop-blur-xl dark:border-white/15 dark:bg-zinc-900/80"
                >
                    <button
                        v-for="tab in filterTabs"
                        :key="tab.id"
                        @click="activeFilter = tab.id"
                        :class="[
                            'rounded-lg px-4 py-2 text-xs font-bold tracking-wider uppercase transition-all',
                            activeFilter === tab.id
                                ? 'bg-[#ff545a] text-white shadow-md'
                                : 'text-zinc-700 hover:bg-zinc-100 hover:text-zinc-950 dark:text-zinc-300 dark:hover:bg-white/10 dark:hover:text-white',
                        ]"
                        type="button"
                    >
                        {{ tab.label }}
                    </button>
                </div>
            </div>

            <!-- Featured Events Cards Grid with Frosted Glass -->
            <div
                class="mt-10 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4"
            >
                <div
                    v-for="item in filteredListings"
                    :key="item.id"
                    class="group flex flex-col justify-between overflow-hidden rounded-2xl border border-zinc-200/80 bg-white/95 shadow-xl backdrop-blur-2xl transition-all duration-300 hover:-translate-y-2 hover:border-[#ff545a] hover:shadow-2xl dark:border-white/15 dark:bg-zinc-900/90 dark:hover:border-[#ff545a] dark:hover:shadow-[0_0_30px_rgba(255,84,90,0.25)]"
                >
                    <!-- Card Image -->
                    <div
                        class="relative h-48 w-full overflow-hidden bg-zinc-100 dark:bg-zinc-800"
                    >
                        <img
                            :src="item.image"
                            :alt="item.title"
                            @error="
                                (e: any) =>
                                    (e.target.src = '/images/card-resort.jpg')
                            "
                            class="h-full w-full object-cover transition duration-500 group-hover:scale-105"
                        />
                        <div
                            class="absolute inset-0 bg-gradient-to-t from-zinc-950/40 via-transparent to-transparent"
                        ></div>
                        <span
                            class="absolute top-3 left-3 rounded-full border border-white/10 bg-zinc-950/85 px-3 py-1 text-[10px] font-bold tracking-wider text-white uppercase backdrop-blur-md"
                        >
                            {{ t(item.statusKey) }}
                        </span>
                    </div>

                    <!-- Card Body -->
                    <div
                        class="flex flex-1 flex-col justify-between p-5 text-left"
                    >
                        <div>
                            <div
                                class="text-[10px] font-bold tracking-widest text-[#ff545a] uppercase"
                            >
                                {{ t(item.categoryKey) }}
                            </div>
                            <h3
                                class="mt-1 font-display text-base font-bold text-zinc-900 transition-colors group-hover:text-[#ff545a] dark:text-white"
                            >
                                {{ item.title }}
                            </h3>
                            <p
                                class="mt-2 line-clamp-2 text-xs leading-relaxed text-zinc-600 dark:text-zinc-300"
                            >
                                {{ t(item.descriptionKey) }}
                            </p>
                        </div>

                        <!-- Card Footer -->
                        <div
                            class="mt-5 flex items-center justify-between border-t border-zinc-100 pt-3 text-[11px] font-medium text-zinc-500 dark:border-zinc-800 dark:text-zinc-400"
                        >
                            <div
                                class="flex items-center gap-1.5 text-zinc-700 dark:text-zinc-300"
                            >
                                <MapPin class="size-3.5 text-[#ff545a]" />
                                <span>{{ item.location }}</span>
                            </div>
                            <div
                                class="flex items-center gap-1.5 text-zinc-500 dark:text-zinc-400"
                            >
                                <Users class="size-3.5" />
                                <span>{{ t(item.capacityKey) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>
