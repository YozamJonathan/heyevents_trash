<script setup lang="ts">
import { 
    Heart, 
    Briefcase, 
    Music2, 
    Users2,
    ArrowRight
} from '@lucide/vue';

interface StandardCategory {
    id: string;
    title: string;
    badge: string;
    listings: string;
    subtitle: string;
    icon: any;
}

const categories: StandardCategory[] = [
    {
        id: 'social',
        title: 'Social & Celebrations',
        badge: 'Weddings & Galas',
        listings: 'Weddings, Birthdays & Galas',
        subtitle: 'Interactive seating charts, RSVP dietary tracking & contribution planners.',
        icon: Heart
    },
    {
        id: 'corporate',
        title: 'Corporate & Business',
        badge: 'Conferences & Summits',
        listings: 'Conferences & Summits',
        subtitle: 'Multi-track session agendas, speaker directories & automated QR badge generation.',
        icon: Briefcase
    },
    {
        id: 'entertainment',
        title: 'Entertainment & Festivals',
        badge: 'Festivals & Stadiums',
        listings: 'Concerts & Stadiums',
        subtitle: 'High-volume tiered ticketing, real-time gate flow & cashless system.',
        icon: Music2
    },
    {
        id: 'community',
        title: 'Community & Non-Profit',
        badge: 'Galas & Ministries',
        listings: 'Fundraising & Galas',
        subtitle: 'Donor tier tracking, pledge reconciliation & volunteer shift coordination.',
        icon: Users2
    }
];

const scrollToExplore = () => {
    const el = document.getElementById('explore');
    if (el) el.scrollIntoView({ behavior: 'smooth' });
};
</script>

<template>
    <section id="standards" class="relative z-20 -mt-14 sm:-mt-20 mb-16">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                <div
                    v-for="cat in categories"
                    :key="cat.id"
                    @click="scrollToExplore"
                    class="group relative flex cursor-pointer flex-col justify-between rounded-2xl border border-zinc-200/90 bg-white/95 p-6 shadow-xl backdrop-blur-xl transition-all duration-300 hover:-translate-y-2 hover:border-[#ff545a] hover:shadow-2xl dark:border-zinc-700/80 dark:bg-zinc-900/95 dark:hover:border-[#ff545a] dark:hover:bg-zinc-900 dark:hover:shadow-[0_0_30px_rgba(255,84,90,0.18)]"
                >
                    <div>
                        <!-- Top Icon & Badge with High Contrast -->
                        <div class="flex items-center justify-between">
                            <div class="flex size-14 items-center justify-center rounded-2xl bg-red-50/90 border border-red-100 text-[#ff545a] shadow-xs transition-transform duration-300 group-hover:scale-110 group-hover:bg-[#ff545a] group-hover:text-white dark:bg-zinc-800 dark:border-zinc-700 dark:text-red-400 dark:group-hover:bg-[#ff545a] dark:group-hover:text-white">
                                <component :is="cat.icon" class="size-6 stroke-[2]" />
                            </div>
                            <span class="rounded-full bg-zinc-100 px-3 py-1 text-[11px] font-bold text-zinc-700 dark:bg-zinc-800 dark:text-zinc-200 border border-zinc-200/60 dark:border-zinc-700">
                                {{ cat.badge }}
                            </span>
                        </div>

                        <!-- Category Title -->
                        <h3 class="mt-5 text-base font-extrabold text-zinc-900 transition group-hover:text-[#ff545a] dark:text-white font-['Poppins',sans-serif]">
                            {{ cat.title }}
                        </h3>

                        <!-- Listings / Domain Label -->
                        <p class="mt-1 text-xs font-semibold text-[#ff545a] dark:text-red-400">
                            {{ cat.listings }}
                        </p>

                        <!-- Subtitle Detail -->
                        <p class="mt-2.5 text-xs leading-relaxed text-zinc-600 dark:text-zinc-300">
                            {{ cat.subtitle }}
                        </p>
                    </div>

                    <!-- Bottom Action Bar -->
                    <div class="mt-6 flex items-center justify-between border-t border-zinc-100 pt-3 dark:border-zinc-800 text-xs font-bold text-zinc-800 dark:text-zinc-200 group-hover:text-[#ff545a] dark:group-hover:text-[#ff545a]">
                        <span>Explore Standard</span>
                        <ArrowRight class="size-4 transition group-hover:translate-x-1" />
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>
