<script setup lang="ts">
import { computed, ref } from 'vue';
import {
    Calendar,
    CalendarCheck,
    Clock,
    CreditCard,
    Heart,
    Search,
    MapPin,
    ArrowUpRight,
} from '@lucide/vue';
import { useLanguage, type TranslationKey } from '@/composables/useLanguage';

const { t } = useLanguage();

// 1. Client Bookings State (Starts clean and empty)
const myBookedEvents = ref<
    Array<{
        title: string;
        date: string;
        time: string;
        location: string;
        status: string;
    }>
>([]);

// 2. Metrics (Clean and accurate)
const metrics = computed(() => [
    {
        title: t('clientDashboard.metricEvents'),
        value: t('clientDashboard.metricEventsValue'),
        subtitle: t('clientDashboard.metricEventsNote'),
        icon: Calendar,
    },
    {
        title: t('clientDashboard.metricInquiries'),
        value: t('clientDashboard.metricInquiriesValue'),
        subtitle: t('clientDashboard.metricInquiriesNote'),
        icon: Clock,
    },
    {
        title: t('clientDashboard.metricInvestment'),
        value: t('clientDashboard.metricInvestmentValue'),
        subtitle: t('clientDashboard.metricInvestmentNote'),
        icon: CreditCard,
    },
    {
        title: t('clientDashboard.metricFavorites'),
        value: t('clientDashboard.metricFavoritesValue'),
        subtitle: t('clientDashboard.metricFavoritesNote'),
        icon: Heart,
    },
]);

// 3. Portfolio Showcase ("Kazi Ambazo Tumezisimamia")
const managedProjects = [
    {
        title: 'Serengeti Corporate Leadership Gala',
        typeKey: 'clientDashboard.project1Type',
        locationKey: 'clientDashboard.project1Location',
        serviceKeys: [
            'clientDashboard.project1Service1',
            'clientDashboard.project1Service2',
            'clientDashboard.project1Service3',
        ],
        image: '/images/hero-city-night.jpg',
        praiseKey: 'clientDashboard.project1Praise',
    },
    {
        title: 'Zanzibar Luxury Destination Wedding',
        typeKey: 'clientDashboard.project2Type',
        locationKey: 'clientDashboard.project2Location',
        serviceKeys: [
            'clientDashboard.project2Service1',
            'clientDashboard.project2Service2',
            'clientDashboard.project2Service3',
        ],
        image: '/images/card-resort.jpg',
        praiseKey: 'clientDashboard.project2Praise',
    },
    {
        title: 'Kilimanjaro Annual Founders Retreat',
        typeKey: 'clientDashboard.project3Type',
        locationKey: 'clientDashboard.project3Location',
        serviceKeys: [
            'clientDashboard.project3Service1',
            'clientDashboard.project3Service2',
            'clientDashboard.project3Service3',
        ],
        image: '/images/card-alpine.jpg',
        praiseKey: 'clientDashboard.project3Praise',
    },
] satisfies {
    title: string;
    typeKey: TranslationKey;
    locationKey: TranslationKey;
    serviceKeys: TranslationKey[];
    image: string;
    praiseKey: TranslationKey;
}[];
</script>

<template>
    <div class="space-y-6">
        <!-- Discovery & Planning Banner -->
        <div
            class="border-border/80 relative flex flex-col gap-4 overflow-hidden rounded-xl border bg-gradient-to-r from-amber-500/10 via-orange-500/5 to-transparent p-5 sm:flex-row sm:items-center sm:justify-between"
        >
            <div class="space-y-1">
                <h3 class="text-foreground text-base font-semibold">
                    {{ t('clientDashboard.welcome') }}
                </h3>
                <p class="text-muted-foreground text-xs">
                    {{ t('clientDashboard.intro') }}
                </p>
            </div>
            <div class="flex items-center gap-2">
                <a
                    href="/events"
                    class="bg-primary text-primary-foreground hover:bg-primary/90 inline-flex items-center gap-1.5 rounded-lg px-3.5 py-2 text-xs font-semibold shadow-sm transition-all"
                >
                    <Search class="size-3.5" />
                    {{ t('clientDashboard.explore') }}
                </a>
            </div>
        </div>

        <!-- 1. Metrics Grid -->
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div
                v-for="metric in metrics"
                :key="metric.title"
                class="border-border/70 bg-card rounded-xl border p-5 shadow-sm transition-all hover:border-amber-500/40"
            >
                <div class="flex items-center justify-between">
                    <span
                        class="text-muted-foreground text-xs font-medium tracking-wider uppercase"
                        >{{ metric.title }}</span
                    >
                    <div
                        class="rounded-lg bg-amber-500/10 p-2 text-amber-600 dark:text-amber-400"
                    >
                        <component :is="metric.icon" class="size-4" />
                    </div>
                </div>
                <div class="mt-3">
                    <div
                        class="text-foreground text-2xl font-bold tracking-tight"
                    >
                        {{ metric.value }}
                    </div>
                    <span class="text-muted-foreground text-xs">{{
                        metric.subtitle
                    }}</span>
                </div>
            </div>
        </div>

        <!-- 2. Upcoming Bookings Section -->
        <div class="border-border/70 bg-card rounded-xl border p-6 shadow-sm">
            <div
                class="border-border/50 flex items-center justify-between border-b pb-4"
            >
                <div>
                    <h2 class="text-foreground text-lg font-semibold">
                        {{ t('clientDashboard.upcoming') }}
                    </h2>
                    <p class="text-muted-foreground text-xs">
                        {{ t('clientDashboard.upcomingDescription') }}
                    </p>
                </div>
            </div>

            <!-- Clean Empty State when no events are booked -->
            <div
                v-if="myBookedEvents.length === 0"
                class="flex flex-col items-center justify-center py-12 text-center"
            >
                <div
                    class="mb-3 rounded-full bg-amber-500/10 p-3.5 text-amber-600 dark:text-amber-400"
                >
                    <CalendarCheck class="size-7" />
                </div>
                <h3 class="text-foreground text-sm font-semibold">
                    {{ t('clientDashboard.noEvents') }}
                </h3>
                <p class="text-muted-foreground mt-1 max-w-sm text-xs">
                    {{ t('clientDashboard.noEventsDescription') }}
                </p>
                <a
                    href="/events"
                    class="border-border bg-background text-foreground hover:bg-muted mt-4 inline-flex items-center gap-1.5 rounded-lg border px-3 py-1.5 text-xs font-medium transition-colors"
                >
                    <Search class="size-3.5" />
                    {{ t('clientDashboard.browse') }}
                </a>
            </div>

            <!-- Populated list if bookings exist in the future -->
            <div v-else class="mt-4 space-y-3">
                <div
                    v-for="(ev, idx) in myBookedEvents"
                    :key="idx"
                    class="border-border/60 bg-muted/20 rounded-lg border p-4"
                >
                    <div class="flex items-center justify-between">
                        <h4 class="text-foreground text-sm font-semibold">
                            {{ ev.title }}
                        </h4>
                        <span
                            class="rounded bg-emerald-500/10 px-2 py-0.5 text-[10px] font-semibold text-emerald-600"
                            >{{ ev.status }}</span
                        >
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. Showcase: Kazi Ambazo Tumezisimamia -->
        <div
            class="border-border/80 bg-card space-y-5 rounded-xl border p-6 shadow-sm"
        >
            <div
                class="border-border/60 flex items-center justify-between border-b pb-4"
            >
                <div>
                    <h2 class="text-foreground text-lg font-bold">
                        {{ t('clientDashboard.portfolio') }}
                    </h2>
                    <p class="text-muted-foreground text-xs">
                        {{ t('clientDashboard.portfolioDescription') }}
                    </p>
                </div>
                <span
                    class="bg-primary/10 text-primary rounded-full px-3 py-1 text-xs font-semibold"
                >
                    {{ t('clientDashboard.managed') }}
                </span>
            </div>

            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                <div
                    v-for="(proj, idx) in managedProjects"
                    :key="idx"
                    class="group border-border/70 bg-card hover:border-primary/50 overflow-hidden rounded-xl border shadow-sm transition-all hover:shadow-md"
                >
                    <div class="bg-muted relative h-44 overflow-hidden">
                        <img
                            :src="proj.image"
                            :alt="proj.title"
                            class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105"
                        />
                        <div
                            class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent"
                        ></div>
                        <span
                            class="absolute bottom-3 left-3 rounded-md bg-black/60 px-2 py-0.5 text-[10px] font-semibold text-white backdrop-blur-sm"
                        >
                            {{ t(proj.typeKey) }}
                        </span>
                    </div>

                    <div class="space-y-3 p-4">
                        <div>
                            <h3
                                class="text-foreground line-clamp-1 text-sm font-bold"
                            >
                                {{ proj.title }}
                            </h3>
                            <p
                                class="text-muted-foreground mt-0.5 flex items-center gap-1 text-xs"
                            >
                                <MapPin class="text-muted-foreground size-3" />
                                {{ t(proj.locationKey) }}
                            </p>
                        </div>

                        <!-- Services Delivered -->
                        <div class="flex flex-wrap gap-1">
                            <span
                                v-for="(serviceKey, sIdx) in proj.serviceKeys"
                                :key="sIdx"
                                class="bg-muted text-foreground rounded px-2 py-0.5 text-[10px] font-medium"
                            >
                                {{ t(serviceKey) }}
                            </span>
                        </div>

                        <p
                            class="border-border/40 text-muted-foreground border-t pt-2.5 text-[11px] italic"
                        >
                            "{{ t(proj.praiseKey) }}"
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
