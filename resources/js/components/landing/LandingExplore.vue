<script setup lang="ts">
import { ref, computed } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import {
    MapPin,
    ShieldCheck,
    Armchair,
    Speaker,
    Building2,
    UtensilsCrossed,
    Palette,
    Car,
    Sparkles,
    ArrowRight,
    CheckCircle2,
} from '@lucide/vue';
import { useLanguage } from '@/composables/useLanguage';

interface CatalogDisplayItem {
    id: number;
    title: string;
    category: string;
    description?: string;
    price: string;
    client_price?: number;
    pricing_unit?: string;
    location: string;
    image: string;
    providerName?: string;
    stock_quantity?: number;
    is_featured?: boolean;
}

const props = defineProps<{
    featuredItems?: CatalogDisplayItem[];
}>();

const { t } = useLanguage();
const page = usePage();
const user = computed(() => page.props.auth?.user);

const activeFilter = ref('all');

const filterTabs = computed(() => [
    { id: 'all', label: t('explore.all'), icon: Sparkles },
    { id: 'rentals', label: t('explore.rentals'), icon: Armchair },
    { id: 'venues', label: t('explore.venues'), icon: Building2 },
    { id: 'catering', label: t('explore.catering'), icon: UtensilsCrossed },
    { id: 'sound', label: t('explore.sound'), icon: Speaker },
    { id: 'decor', label: t('explore.decor'), icon: Palette },
    { id: 'transport', label: t('explore.transport'), icon: Car },
]);

const fallbackItems: CatalogDisplayItem[] = [
    {
        id: 1,
        title: 'Chiavari & Banquet Chairs (White & Gold)',
        category: 'rentals',
        description: 'Heavy-duty resin Chiavari chairs with soft cushions, ideal for weddings and gala dinners.',
        price: 'TZS 2,500 / chair',
        client_price: 2500,
        pricing_unit: 'per piece',
        location: 'Kinondoni, Dar es Salaam',
        image: '/images/card-resort.jpg',
        providerName: 'Classic Event Rentals',
        stock_quantity: 450,
        is_featured: true,
    },
    {
        id: 2,
        title: 'High-Peak Hexagonal Marquee Tent (250 Guests)',
        category: 'rentals',
        description: 'Waterproof heavy-duty PVC marquee with perimeter draping, poles, and setup crew included.',
        price: 'TZS 650,000 / day',
        client_price: 650000,
        pricing_unit: 'per day',
        location: 'Sinza, Dar es Salaam',
        image: '/images/card-alpine.jpg',
        providerName: 'Kilimanjaro Tents & Logistics',
        stock_quantity: 12,
        is_featured: true,
    },
    {
        id: 3,
        title: 'Full Concert Line Array PA System & Pro DJ',
        category: 'sound',
        description: 'JBL Line Array, wireless Shure mics, intelligent beam lighting rigs, and professional audio technician.',
        price: 'TZS 850,000 / event',
        client_price: 850000,
        pricing_unit: 'per event',
        location: 'Masaki, Dar es Salaam',
        image: '/images/hero-dar-night.jpg',
        providerName: 'BeatWave Pro Audio',
        stock_quantity: 4,
        is_featured: true,
    },
    {
        id: 4,
        title: 'Serena Grand Ballroom & Tropical Gardens',
        category: 'venues',
        description: 'Air-conditioned luxury hall for 600 seated banquet guests with stage, standby generator, and private parking.',
        price: 'TZS 3,500,000 / day',
        client_price: 3500000,
        pricing_unit: 'per day',
        location: 'City Centre, Dar es Salaam',
        image: '/images/card-resort.jpg',
        providerName: 'Dar Serena Hospitality',
        stock_quantity: 1,
        is_featured: true,
    },
];

const itemsList = computed(() => {
    if (props.featuredItems && props.featuredItems.length > 0) {
        return props.featuredItems;
    }
    return fallbackItems;
});

const filteredItems = computed(() => {
    if (activeFilter.value === 'all') {
        return itemsList.value;
    }
    return itemsList.value.filter((item) => {
        if (activeFilter.value === 'sound') {
            return item.category === 'sound' || item.category === 'photography' || item.category === 'sound-media';
        }
        if (activeFilter.value === 'transport') {
            return item.category === 'transport' || item.category === 'protocol';
        }
        return item.category === activeFilter.value;
    });
});

function handleBook(item: CatalogDisplayItem) {
    if (!user.value) {
        router.visit(`/login?redirect=/events/book?item=${item.id}`);
    } else {
        router.visit(`/events/book?item=${item.id}`);
    }
}
</script>

<template>
    <section id="explore" class="relative overflow-hidden py-16 sm:py-24">
        <div class="relative z-10 mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <!-- Section Header -->
            <div class="flex flex-col justify-between gap-6 pb-2 md:flex-row md:items-end">
                <div class="max-w-2xl">
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-red-500/10 px-3 py-1 text-xs font-bold uppercase tracking-wider text-[#ff545a]">
                        <Sparkles class="size-3.5" />
                        <span>{{ t('explore.eyebrow') }}</span>
                    </span>
                    <h2 class="mt-3 font-display text-2xl sm:text-4xl font-extrabold tracking-tight text-zinc-950 dark:text-white">
                        {{ t('explore.title') }}
                    </h2>
                    <p class="mt-2 text-xs sm:text-sm text-zinc-600 dark:text-zinc-300">
                        {{ t('explore.subtitle') }}
                    </p>
                </div>

                <!-- Filter Category Tabs -->
                <div class="flex flex-wrap items-center gap-1.5 rounded-2xl border border-zinc-200/80 bg-white/90 p-1.5 shadow-md backdrop-blur-xl dark:border-white/10 dark:bg-zinc-900/80">
                    <button
                        v-for="tab in filterTabs"
                        :key="tab.id"
                        @click="activeFilter = tab.id"
                        :class="[
                            'inline-flex items-center gap-1.5 rounded-xl px-3.5 py-2 text-xs font-bold transition-all',
                            activeFilter === tab.id
                                ? 'bg-[#ff545a] text-white shadow-md'
                                : 'text-zinc-700 hover:bg-zinc-100 dark:text-zinc-300 dark:hover:bg-white/10 dark:hover:text-white',
                        ]"
                        type="button"
                    >
                        <component :is="tab.icon" class="size-3.5" />
                        <span>{{ tab.label }}</span>
                    </button>
                </div>
            </div>

            <!-- Featured Items Cards Grid -->
            <div class="mt-10 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
                <div
                    v-for="item in filteredItems"
                    :key="item.id"
                    class="group flex flex-col justify-between overflow-hidden rounded-3xl border border-zinc-200/80 bg-white/95 shadow-lg backdrop-blur-2xl transition-all duration-300 hover:-translate-y-1.5 hover:border-[#ff545a]/50 hover:shadow-xl dark:border-white/10 dark:bg-zinc-900/90"
                >
                    <!-- Card Media -->
                    <div class="relative h-48 w-full overflow-hidden bg-zinc-100 dark:bg-zinc-800">
                        <img
                            :src="item.image"
                            :alt="item.title"
                            class="h-full w-full object-cover transition duration-500 group-hover:scale-105"
                        />
                        <div class="absolute inset-0 bg-gradient-to-t from-zinc-950/70 via-transparent to-transparent"></div>

                        <!-- Verified Badge -->
                        <span class="absolute top-3 left-3 inline-flex items-center gap-1 rounded-full border border-emerald-500/20 bg-emerald-600/90 px-2.5 py-0.5 text-[10px] font-bold tracking-wider text-white uppercase backdrop-blur-md shadow-xs">
                            <ShieldCheck class="size-3" />
                            <span>HEY Events Verified</span>
                        </span>

                        <!-- Location Pill -->
                        <span class="absolute bottom-2.5 left-3 inline-flex items-center gap-1 text-[11px] font-medium text-white/90">
                            <MapPin class="size-3 text-[#ff545a]" />
                            <span class="truncate max-w-[200px]">{{ item.location }}</span>
                        </span>
                    </div>

                    <!-- Card Body -->
                    <div class="flex flex-1 flex-col justify-between p-5 text-left space-y-4">
                        <div>
                            <span class="text-[10px] font-extrabold uppercase tracking-widest text-[#ff545a]">
                                {{ item.category }}
                            </span>
                            <h3 class="mt-1 font-display text-base font-bold text-zinc-950 line-clamp-1 group-hover:text-[#ff545a] transition dark:text-white">
                                {{ item.title }}
                            </h3>
                            <p class="mt-1.5 text-xs text-zinc-600 dark:text-zinc-300 line-clamp-2 leading-relaxed">
                                {{ item.description }}
                            </p>
                        </div>

                        <!-- Price & Booking Action -->
                        <div class="border-t border-zinc-100 dark:border-zinc-800 pt-3.5 space-y-3">
                            <div class="flex items-baseline justify-between">
                                <div>
                                    <span class="text-[10px] font-bold uppercase text-zinc-400 block tracking-wider">
                                        Client Price
                                    </span>
                                    <span class="text-base font-black text-[#ff545a]">
                                        {{ item.price }}
                                    </span>
                                </div>
                                <span v-if="item.stock_quantity" class="text-[10px] font-semibold text-emerald-600 dark:text-emerald-400">
                                    {{ item.stock_quantity }} {{ t('explore.unitsAvailable') }}
                                </span>
                            </div>

                            <button
                                type="button"
                                @click="handleBook(item)"
                                class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-zinc-900 py-2.5 px-4 text-xs font-bold text-white transition hover:bg-[#ff545a] shadow-xs dark:bg-white dark:text-zinc-900 dark:hover:bg-[#ff545a] dark:hover:text-white"
                            >
                                <span>{{ t('explore.bookNow') }}</span>
                                <ArrowRight class="size-3.5" />
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bottom CTA Banner: Middle Agent Guarantee & Complete Marketplace Link -->
            <div class="mt-12 flex flex-col sm:flex-row items-center justify-between gap-4 rounded-3xl border border-zinc-200/80 bg-white/90 p-6 shadow-md backdrop-blur-xl dark:border-white/10 dark:bg-zinc-900/80">
                <div class="flex items-center gap-3">
                    <div class="rounded-2xl bg-emerald-500/15 p-3 text-emerald-600 dark:text-emerald-400 shrink-0">
                        <CheckCircle2 class="size-6" />
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-zinc-950 dark:text-white">
                            {{ t('explore.guarantee') }}
                        </h4>
                        <p class="text-xs text-zinc-600 dark:text-zinc-300 mt-0.5">
                            We physically inspect all equipment, oversee on-site setup, and hold payment until you are satisfied.
                        </p>
                    </div>
                </div>

                <Link
                    href="/explore"
                    class="inline-flex items-center gap-2 shrink-0 rounded-2xl bg-[#ff545a] px-6 py-3 text-xs font-extrabold text-white shadow-md hover:bg-[#ff4047] transition hover:shadow-lg"
                >
                    <span>{{ t('explore.viewAll') }}</span>
                    <ArrowRight class="size-4" />
                </Link>
            </div>
        </div>
    </section>
</template>
