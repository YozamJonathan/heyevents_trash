<script setup lang="ts">
import {
    Briefcase,
    Calendar,
    DollarSign,
    Star,
    Plus,
    Clock,
    CheckCircle2,
    ArrowUpRight,
    MapPin,
    BadgeCheck,
} from '@lucide/vue';

const metrics = [
    { title: 'Total Net Earnings', value: '$12,450', change: '+$1,800 this month', icon: DollarSign },
    { title: 'Upcoming Bookings', value: '8 Events', change: 'Next: Oct 12', icon: Calendar },
    { title: 'Active Service Packages', value: '4 Listed', change: 'All Published', icon: Briefcase },
    { title: 'Client Rating', value: '4.95 / 5.0', change: '36 verified reviews', icon: Star },
];

const upcomingBookings = [
    { client: 'Samantha & Alex', event: 'Wedding Reception DJ & Sound', date: 'Sat, Oct 12, 2026', time: '5:00 PM - 11:00 PM', location: 'Sunset Bay Resort', amount: '$1,800', status: 'Confirmed' },
    { client: 'Nexus Tech Global', event: 'Annual Tech Summit Keynote AV', date: 'Wed, Oct 16, 2026', time: '9:00 AM - 4:00 PM', location: 'Grand Convention Center', amount: '$2,400', status: 'Confirmed' },
    { client: 'Liam Sterling', event: 'Private Birthday Lighting Set', date: 'Fri, Oct 24, 2026', time: '7:00 PM - 1:00 AM', location: 'The Glasshouse', amount: '$950', status: 'Pending Deposit' },
];

const myServices = [
    { title: 'Full-Day Premium DJ & Master of Ceremonies', price: '$1,800 / event', bookings: '18 completed', status: 'Active' },
    { title: 'Concert Grade Acoustic & Ambient Lighting Rig', price: '$950 / day', bookings: '12 completed', status: 'Active' },
    { title: 'Wireless Microphone & Podcast Recording Suite', price: '$400 / session', bookings: '6 completed', status: 'Active' },
];
</script>

<template>
    <div class="space-y-6">
        <!-- Vendor Profile Status Card -->
        <div class="flex flex-col gap-4 rounded-xl border border-emerald-500/30 bg-emerald-500/5 p-4 sm:flex-row sm:items-center sm:justify-between dark:border-emerald-500/20">
            <div class="flex items-center gap-3">
                <div class="rounded-full bg-emerald-500/10 p-2.5 text-emerald-600 dark:text-emerald-400">
                    <BadgeCheck class="size-6" />
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h3 class="text-sm font-semibold text-foreground">Verified Service Provider</h3>
                        <span class="rounded bg-emerald-500/20 px-2 py-0.5 text-[10px] font-bold uppercase text-emerald-700 dark:text-emerald-300">Active</span>
                    </div>
                    <p class="text-xs text-muted-foreground">Your KYC documentation is approved. You are eligible to receive instant bookings and payouts.</p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <button class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-600 px-3 py-1.5 text-xs font-medium text-white shadow-sm hover:bg-emerald-700">
                    <Plus class="size-3.5" /> Add New Service
                </button>
            </div>
        </div>

        <!-- Metrics Grid -->
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div
                v-for="metric in metrics"
                :key="metric.title"
                class="rounded-xl border border-border/70 bg-card p-5 shadow-sm transition-all hover:border-emerald-500/40"
            >
                <div class="flex items-center justify-between">
                    <span class="text-xs font-medium uppercase tracking-wider text-muted-foreground">{{ metric.title }}</span>
                    <div class="rounded-lg bg-emerald-500/10 p-2 text-emerald-600 dark:text-emerald-400">
                        <component :is="metric.icon" class="size-4" />
                    </div>
                </div>
                <div class="mt-3 flex items-baseline gap-2">
                    <span class="text-2xl font-bold tracking-tight text-foreground">{{ metric.value }}</span>
                    <span class="text-xs font-medium text-emerald-600 dark:text-emerald-400">
                        {{ metric.change }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Provider Main Sections -->
        <div class="grid gap-6 lg:grid-cols-3">
            <!-- Upcoming Gigs & Bookings -->
            <div class="rounded-xl border border-border/70 bg-card p-6 shadow-sm lg:col-span-2">
                <div class="flex items-center justify-between border-b border-border/50 pb-4">
                    <div>
                        <h2 class="text-lg font-semibold text-foreground">Upcoming Event Schedule</h2>
                        <p class="text-xs text-muted-foreground">Direct client bookings assigned to your vendor schedule.</p>
                    </div>
                    <button class="text-xs font-medium text-primary hover:underline">
                        View Calendar &rarr;
                    </button>
                </div>

                <div class="mt-4 space-y-3">
                    <div
                        v-for="(booking, idx) in upcomingBookings"
                        :key="idx"
                        class="rounded-lg border border-border/60 bg-muted/20 p-4 transition-all hover:border-border"
                    >
                        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <div class="flex items-center gap-2">
                                    <h4 class="text-sm font-semibold text-foreground">{{ booking.event }}</h4>
                                    <span
                                        class="rounded-full px-2 py-0.5 text-[10px] font-semibold"
                                        :class="booking.status === 'Confirmed' ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400' : 'bg-amber-500/10 text-amber-600 dark:text-amber-400'"
                                    >
                                        {{ booking.status }}
                                    </span>
                                </div>
                                <p class="text-xs text-muted-foreground">Client: <span class="font-medium text-foreground">{{ booking.client }}</span></p>
                            </div>
                            <div class="text-right">
                                <span class="text-sm font-bold text-foreground">{{ booking.amount }}</span>
                            </div>
                        </div>

                        <div class="mt-3 flex flex-wrap items-center gap-4 border-t border-border/40 pt-3 text-xs text-muted-foreground">
                            <span class="inline-flex items-center gap-1">
                                <Calendar class="size-3.5 text-primary" /> {{ booking.date }}
                            </span>
                            <span class="inline-flex items-center gap-1">
                                <Clock class="size-3.5 text-primary" /> {{ booking.time }}
                            </span>
                            <span class="inline-flex items-center gap-1">
                                <MapPin class="size-3.5 text-primary" /> {{ booking.location }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- My Service Listings & Payouts -->
            <div class="space-y-6">
                <div class="rounded-xl border border-border/70 bg-card p-6 shadow-sm">
                    <div class="flex items-center justify-between border-b border-border/50 pb-4">
                        <h3 class="text-base font-semibold text-foreground">My Service Catalog</h3>
                        <Briefcase class="size-4 text-emerald-600 dark:text-emerald-400" />
                    </div>

                    <div class="mt-4 space-y-3">
                        <div
                            v-for="(svc, idx) in myServices"
                            :key="idx"
                            class="rounded-lg border border-border/60 bg-muted/30 p-3"
                        >
                            <div class="flex items-start justify-between">
                                <h4 class="text-xs font-semibold text-foreground line-clamp-1">{{ svc.title }}</h4>
                                <span class="rounded bg-emerald-500/10 px-1.5 py-0.5 text-[10px] font-medium text-emerald-600">{{ svc.status }}</span>
                            </div>
                            <div class="mt-2 flex items-center justify-between text-xs">
                                <span class="font-bold text-foreground">{{ svc.price }}</span>
                                <span class="text-muted-foreground text-[11px]">{{ svc.bookings }}</span>
                            </div>
                        </div>
                    </div>

                    <button class="mt-4 w-full rounded-lg border border-border bg-background py-2 text-center text-xs font-medium text-foreground hover:bg-muted">
                        Manage All Packages
                    </button>
                </div>

                <div class="rounded-xl border border-border/70 bg-card p-5 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <span class="text-xs text-muted-foreground">Available Payout Balance</span>
                            <p class="text-2xl font-bold text-foreground">$3,240.00</p>
                        </div>
                        <button class="rounded-lg bg-emerald-600 px-3 py-2 text-xs font-medium text-white hover:bg-emerald-700">
                            Request Payout
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
