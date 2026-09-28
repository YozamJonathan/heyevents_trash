<script setup lang="ts">
import { Calendar, User, ArrowRight } from '@lucide/vue';

interface Article {
    id: number;
    title: string;
    date: string;
    author: string;
    image: string;
    excerpt: string;
    tag: string;
}

const articles: Article[] = [
    {
        id: 1,
        title: 'Mastering Seating Layouts and RSVP Tracking for 500+ Guest Weddings',
        date: 'September 18, 2026',
        author: 'Zawadi Mwema',
        image: '/images/card-bar.jpg',
        excerpt: 'How interactive table drag-and-drop tools and dietary preference surveys eliminate reception confusion and delight guests.',
        tag: 'Social & Celebrations'
    },
    {
        id: 2,
        title: 'Mastering Gate Logistics for 20,000+ Stadium Crowds & Festivals',
        date: 'September 14, 2026',
        author: 'Rachel Adams',
        image: '/images/hero-city-night.jpg',
        excerpt: 'How multi-lane offline-first QR scanning and zone-based entry prevent bottlenecks at major concerts and multi-day sports tournaments.',
        tag: 'Entertainment & Festivals'
    },
    {
        id: 3,
        title: 'Automating Agendas & Sponsor Lead Capture at Tech Summits',
        date: 'September 08, 2026',
        author: 'David Chen',
        image: '/images/card-resort.jpg',
        excerpt: 'Transform executive conferences with instant QR badge syncing, multi-track schedule updates, and live attendee analytics.',
        tag: 'Corporate & Business'
    }
];

// Fallback image handling
const handleImageError = (e: Event) => {
    const target = e.target as HTMLImageElement;
    target.src = '/images/hero-city-night.jpg';
};
</script>

<template>
    <section id="blog" class="bg-white py-16 sm:py-24 dark:bg-zinc-950">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <!-- Section Header -->
            <div class="text-center">
                <span class="text-xs font-bold uppercase tracking-widest text-[#ff545a]">
                    Industry Insights
                </span>
                <h2 class="mt-1 text-2xl font-black uppercase tracking-wider text-zinc-900 sm:text-3xl dark:text-white">
                    News & Event Management Articles
                </h2>
                <p class="mx-auto mt-2 max-w-2xl text-sm text-zinc-500 dark:text-zinc-400">
                    Insights, trend reports, and guidebooks for modern event directors and hosts.
                </p>
            </div>

            <!-- Articles Grid -->
            <div class="mt-12 grid grid-cols-1 gap-8 md:grid-cols-3">
                <article
                    v-for="art in articles"
                    :key="art.id"
                    class="group flex flex-col overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-sm transition-all duration-300 hover:-translate-y-1 hover:border-zinc-300 hover:shadow-xl dark:border-zinc-800 dark:bg-zinc-900"
                >
                    <div class="relative h-48 w-full overflow-hidden bg-zinc-100 dark:bg-zinc-800">
                        <img
                            :src="art.image"
                            :alt="art.title"
                            @error="handleImageError"
                            class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105"
                        />
                        <span class="absolute left-3 top-3 rounded bg-zinc-900/80 px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider text-white backdrop-blur-sm">
                            {{ art.tag }}
                        </span>
                    </div>

                    <div class="flex flex-1 flex-col p-6">
                        <!-- Meta Info -->
                        <div class="flex items-center gap-4 text-[11px] text-zinc-400 dark:text-zinc-500">
                            <span class="flex items-center gap-1">
                                <Calendar class="h-3 w-3 text-red-500" />
                                {{ art.date }}
                            </span>
                            <span class="flex items-center gap-1">
                                <User class="h-3 w-3 text-red-500" />
                                {{ art.author }}
                            </span>
                        </div>

                        <!-- Title -->
                        <h3 class="mt-3 text-base font-bold text-zinc-900 transition group-hover:text-[#ff545a] dark:text-zinc-100">
                            {{ art.title }}
                        </h3>

                        <!-- Excerpt -->
                        <p class="mt-2 text-xs leading-relaxed text-zinc-600 line-clamp-3 dark:text-zinc-400">
                            {{ art.excerpt }}
                        </p>

                        <div class="flex-1"></div>

                        <div class="mt-5 border-t border-zinc-100 pt-3 dark:border-zinc-800">
                            <a
                                href="#explore"
                                class="inline-flex items-center gap-1.5 text-xs font-bold text-[#ff545a] transition hover:text-red-700"
                            >
                                <span>Read Full Story</span>
                                <ArrowRight class="h-3.5 w-3.5 transition group-hover:translate-x-1" />
                            </a>
                        </div>
                    </div>
                </article>
            </div>
        </div>
    </section>
</template>
