<script setup lang="ts">
import {
    Calendar,
    Sparkles,
    Search,
    MapPin,
    CreditCard,
    CheckCircle2,
    Heart,
    Clock,
    ArrowUpRight,
    Music,
    Utensils,
    Camera,
    PartyPopper,
} from '@lucide/vue';

const metrics = [
    { title: 'My Booked Events', value: '3 Upcoming', subtitle: 'Next on Nov 15', icon: Calendar },
    { title: 'Reserved Vendors', value: '5 Providers', subtitle: 'Catering, DJ, Photo', icon: Sparkles },
    { title: 'Total Investment', value: '$4,350', subtitle: 'All deposits secured', icon: CreditCard },
    { title: 'Saved Favorites', value: '12 Vendors', subtitle: 'Shortlisted', icon: Heart },
];

const myBookedEvents = [
    {
        title: 'Golden Jubilee Gala Dinner',
        date: 'Sat, Nov 15, 2026',
        time: '6:30 PM',
        location: 'Grand Opal Ballroom',
        vendors: ['Elena Rostova (DJ & Sound)', 'Grand Royale (Catering)'],
        status: 'Confirmed',
    },
    {
        title: 'Annual Founders Retreat',
        date: 'Fri, Dec 04, 2026',
        time: '10:00 AM',
        location: 'The Ridgeview Estate',
        vendors: ['Luxe Floral Artistry'],
        status: 'In Planning',
    },
];

const recommendedVendors = [
    { name: 'Elena Rostova DJ & Sound', category: 'Music & Audio', rating: '4.95 (36 reviews)', price: 'from $400', icon: Music },
    { name: 'Gourmet Artisans Culinary', category: 'Catering & Beverages', rating: '4.90 (52 reviews)', price: 'from $45/person', icon: Utensils },
    { name: 'Vivid Memories Photography', category: 'Photo & Drone Video', rating: '5.0 (29 reviews)', price: 'from $800', icon: Camera },
];
</script>

<template>
    <div class="space-y-6">
        <!-- Quick Action Discovery Banner -->
        <div class="relative flex flex-col gap-4 overflow-hidden rounded-xl border border-border/80 bg-gradient-to-r from-amber-500/10 via-orange-500/5 to-transparent p-5 sm:flex-row sm:items-center sm:justify-between">
            <div class="space-y-1">
                <h3 class="text-base font-semibold text-foreground">Planning a new event?</h3>
                <p class="text-xs text-muted-foreground">Discover top-rated verified service providers, check live calendar availability, and book in minutes.</p>
            </div>
            <div class="flex items-center gap-2">
                <button class="inline-flex items-center gap-1.5 rounded-lg bg-primary px-3.5 py-2 text-xs font-semibold text-primary-foreground shadow-sm hover:bg-primary/90">
                    <Search class="size-3.5" /> Explore Service Providers
                </button>
            </div>
        </div>

        <!-- Metrics Grid -->
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div
                v-for="metric in metrics"
                :key="metric.title"
                class="rounded-xl border border-border/70 bg-card p-5 shadow-sm transition-all hover:border-amber-500/40"
            >
                <div class="flex items-center justify-between">
                    <span class="text-xs font-medium uppercase tracking-wider text-muted-foreground">{{ metric.title }}</span>
                    <div class="rounded-lg bg-amber-500/10 p-2 text-amber-600 dark:text-amber-400">
                        <component :is="metric.icon" class="size-4" />
                    </div>
                </div>
                <div class="mt-3">
                    <div class="text-2xl font-bold tracking-tight text-foreground">{{ metric.value }}</div>
                    <span class="text-xs text-muted-foreground">{{ metric.subtitle }}</span>
                </div>
            </div>
        </div>

        <!-- Client Content -->
        <div class="grid gap-6 lg:grid-cols-3">
            <!-- My Bookings -->
            <div class="rounded-xl border border-border/70 bg-card p-6 shadow-sm lg:col-span-2">
                <div class="flex items-center justify-between border-b border-border/50 pb-4">
                    <div>
                        <h2 class="text-lg font-semibold text-foreground">My Upcoming Events & Bookings</h2>
                        <p class="text-xs text-muted-foreground">Your scheduled events and hired service providers.</p>
                    </div>
                    <button class="text-xs font-medium text-primary hover:underline">
                        View All (3) &rarr;
                    </button>
                </div>

                <div class="mt-4 space-y-4">
                    <div
                        v-for="(ev, idx) in myBookedEvents"
                        :key="idx"
                        class="rounded-lg border border-border/60 bg-muted/20 p-4 transition-all hover:border-border"
                    >
                        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                            <div class="space-y-1">
                                <div class="flex items-center gap-2">
                                    <PartyPopper class="size-4 text-amber-500" />
                                    <h4 class="text-sm font-semibold text-foreground">{{ ev.title }}</h4>
                                    <span class="rounded-full bg-emerald-500/10 px-2 py-0.5 text-[10px] font-semibold text-emerald-600 dark:text-emerald-400">
                                        {{ ev.status }}
                                    </span>
                                </div>
                                <p class="text-xs text-muted-foreground">
                                    Hired: <span class="font-medium text-foreground">{{ ev.vendors.join(', ') }}</span>
                                </p>
                            </div>
                            <button class="inline-flex items-center gap-1 rounded-lg border border-border bg-background px-2.5 py-1.5 text-xs font-medium text-foreground hover:bg-muted">
                                Manage Event
                            </button>
                        </div>

                        <div class="mt-3 flex flex-wrap items-center gap-4 border-t border-border/40 pt-3 text-xs text-muted-foreground">
                            <span class="inline-flex items-center gap-1">
                                <Calendar class="size-3.5 text-primary" /> {{ ev.date }}
                            </span>
                            <span class="inline-flex items-center gap-1">
                                <Clock class="size-3.5 text-primary" /> {{ ev.time }}
                            </span>
                            <span class="inline-flex items-center gap-1">
                                <MapPin class="size-3.5 text-primary" /> {{ ev.location }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recommended Vendors -->
            <div class="space-y-6">
                <div class="rounded-xl border border-border/70 bg-card p-6 shadow-sm">
                    <div class="flex items-center justify-between border-b border-border/50 pb-4">
                        <h3 class="text-base font-semibold text-foreground">Top Rated For You</h3>
                        <Sparkles class="size-4 text-amber-500" />
                    </div>

                    <div class="mt-4 space-y-3">
                        <div
                            v-for="(vendor, idx) in recommendedVendors"
                            :key="idx"
                            class="rounded-lg border border-border/60 bg-muted/30 p-3 transition-colors hover:bg-muted/50"
                        >
                            <div class="flex items-start gap-3">
                                <div class="rounded-lg bg-amber-500/10 p-2 text-amber-600 dark:text-amber-400">
                                    <component :is="vendor.icon" class="size-4" />
                                </div>
                                <div class="flex-1 space-y-0.5">
                                    <h4 class="text-xs font-semibold text-foreground">{{ vendor.name }}</h4>
                                    <p class="text-[11px] text-muted-foreground">{{ vendor.category }}</p>
                                    <div class="mt-2 flex items-center justify-between text-[11px]">
                                        <span class="text-amber-600 font-medium dark:text-amber-400">★ {{ vendor.rating }}</span>
                                        <span class="font-semibold text-foreground">{{ vendor.price }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <button class="mt-4 w-full rounded-lg border border-border bg-background py-2 text-center text-xs font-medium text-foreground hover:bg-muted">
                        Browse All Categories
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
