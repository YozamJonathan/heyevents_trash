<script setup lang="ts">
import { ref, computed } from 'vue';
import { MapPin, Users } from '@lucide/vue';

interface ListingItem {
    id: number;
    title: string;
    category: string;
    standard: 'social' | 'corporate' | 'entertainment' | 'community';
    image: string;
    status: string;
    location: string;
    capacity: string;
    description: string;
}

const activeFilter = ref('all');

const filterTabs = [
    { id: 'all', label: 'ALL STANDARDS' },
    { id: 'social', label: 'SOCIAL' },
    { id: 'corporate', label: 'CORPORATE' },
    { id: 'entertainment', label: 'ENTERTAINMENT' },
    { id: 'community', label: 'COMMUNITY' },
];

const listings = ref<ListingItem[]>([
    {
        id: 1,
        title: 'Amani & Zawadi Wedding Gala',
        category: 'Weddings & Celebrations',
        standard: 'social',
        image: '/images/card-resort.jpg',
        status: 'OPEN FOR RSVP',
        location: 'Serena Grand Ballroom',
        capacity: '400 Guests',
        description: 'Interactive table seating, digital meal preferences, and live wedding gift registry tracking.'
    },
    {
        id: 2,
        title: 'East Africa Tech Summit 2026',
        category: 'Conferences & Summits',
        standard: 'corporate',
        image: '/images/hero-city-night.jpg',
        status: 'REGISTRATION OPEN',
        location: 'Convention Center',
        capacity: '2,500 Delegates',
        description: '8 multi-track session halls, 65 keynote speakers, and automated QR badge lead retrieval.'
    },
    {
        id: 3,
        title: 'Safari Beats Music Festival',
        category: 'Festivals & Stadiums',
        standard: 'entertainment',
        image: '/images/card-alpine.jpg',
        status: 'EARLY BIRD ACTIVE',
        location: 'National Stadium Park',
        capacity: '15,000 Attendees',
        description: 'High-speed gate turnstiles with sub-second QR entry and cashless wristband zones.'
    },
    {
        id: 4,
        title: 'Education For All Benefit Gala',
        category: 'Fundraising & Galas',
        standard: 'community',
        image: '/images/card-resort.jpg',
        status: 'DONATION OPEN',
        location: 'Civic Center Grand Hall',
        capacity: '600 Donors',
        description: 'Real-time live pledge tallying, WhatsApp updates, and seamless volunteer coordination.'
    }
]);

const filteredListings = computed(() => {
    if (activeFilter.value === 'all') {
        return listings.value;
    }
    return listings.value.filter(item => item.standard === activeFilter.value);
});
</script>

<template>
    <section 
        id="explore" 
        class="relative overflow-hidden py-16 sm:py-24"
    >
        <div class="relative z-10 mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <!-- Header & Filter Tabs -->
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 pb-2">
                <div>
                    <span class="text-xs font-bold tracking-widest uppercase text-[#ff545a]">
                        DIRECTORY
                    </span>
                    <h2 class="mt-1 text-3xl font-black uppercase tracking-wider text-zinc-950 dark:text-white sm:text-4xl drop-shadow-xs dark:drop-shadow-md font-['Poppins',sans-serif]">
                        FEATURED EVENTS
                    </h2>
                </div>

                <!-- Filter Tabs -->
                <div class="flex flex-wrap items-center gap-1.5 p-1 rounded-xl bg-white/90 border border-zinc-200/80 shadow-md backdrop-blur-xl dark:bg-zinc-900/80 dark:border-white/15">
                    <button
                        v-for="tab in filterTabs"
                        :key="tab.id"
                        @click="activeFilter = tab.id"
                        :class="[
                            'rounded-lg px-4 py-2 text-xs font-bold uppercase tracking-wider transition-all',
                            activeFilter === tab.id
                                ? 'bg-[#ff545a] text-white shadow-md'
                                : 'text-zinc-700 hover:text-zinc-950 hover:bg-zinc-100 dark:text-zinc-300 dark:hover:text-white dark:hover:bg-white/10'
                        ]"
                        type="button"
                    >
                        {{ tab.label }}
                    </button>
                </div>
            </div>

            <!-- Featured Events Cards Grid with Frosted Glass -->
            <div class="mt-10 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
                <div
                    v-for="item in filteredListings"
                    :key="item.id"
                    class="group flex flex-col justify-between overflow-hidden rounded-2xl border border-zinc-200/80 bg-white/95 shadow-xl backdrop-blur-2xl transition-all duration-300 hover:-translate-y-2 hover:border-[#ff545a] hover:shadow-2xl dark:border-white/15 dark:bg-zinc-900/90 dark:hover:border-[#ff545a] dark:hover:shadow-[0_0_30px_rgba(255,84,90,0.25)]"
                >
                    <!-- Card Image -->
                    <div class="relative h-48 w-full overflow-hidden bg-zinc-100 dark:bg-zinc-800">
                        <img
                            :src="item.image"
                            :alt="item.title"
                            @error="(e: any) => (e.target.src = '/images/card-resort.jpg')"
                            class="h-full w-full object-cover transition duration-500 group-hover:scale-105"
                        />
                        <div class="absolute inset-0 bg-gradient-to-t from-zinc-950/40 via-transparent to-transparent"></div>
                        <span class="absolute top-3 left-3 rounded-full bg-zinc-950/85 backdrop-blur-md px-3 py-1 text-[10px] font-bold uppercase tracking-wider text-white border border-white/10">
                            {{ item.status }}
                        </span>
                    </div>

                    <!-- Card Body -->
                    <div class="flex flex-1 flex-col justify-between p-5 text-left">
                        <div>
                            <div class="text-[10px] font-bold uppercase tracking-widest text-[#ff545a]">
                                {{ item.category }}
                            </div>
                            <h3 class="mt-1 text-base font-bold text-zinc-900 dark:text-white font-['Poppins',sans-serif] group-hover:text-[#ff545a] transition-colors">
                                {{ item.title }}
                            </h3>
                            <p class="mt-2 text-xs leading-relaxed text-zinc-600 dark:text-zinc-300 line-clamp-2">
                                {{ item.description }}
                            </p>
                        </div>

                        <!-- Card Footer -->
                        <div class="mt-5 flex items-center justify-between border-t border-zinc-100 dark:border-zinc-800 pt-3 text-[11px] font-medium text-zinc-500 dark:text-zinc-400">
                            <div class="flex items-center gap-1.5 text-zinc-700 dark:text-zinc-300">
                                <MapPin class="size-3.5 text-[#ff545a]" />
                                <span>{{ item.location }}</span>
                            </div>
                            <div class="flex items-center gap-1.5 text-zinc-500 dark:text-zinc-400">
                                <Users class="size-3.5" />
                                <span>{{ item.capacity }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>
