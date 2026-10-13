<script setup lang="ts">
import { computed } from 'vue';
import {
    Briefcase,
    Calendar,
    Banknote,
    Star,
    Plus,
    Clock,
    BadgeCheck,
    CalendarCheck2,
    PackagePlus,
} from '@lucide/vue';

const metrics = computed(() => [
    { title: 'Total Net Earnings', value: 'TZS 0', change: 'New Account', icon: Banknote },
    { title: 'Upcoming Bookings', value: '0 Events', change: 'Schedule Clear', icon: Calendar },
    { title: 'Active Service Packages', value: '0 Listed', change: 'Add First Package', icon: Briefcase },
    { title: 'Client Rating', value: 'New Provider', change: 'Awaiting Reviews', icon: Star },
]);

// Real items (starts empty)
const upcomingBookings = computed<Array<{ client: string; event: string; date: string; time: string; location: string; amount: string; status: string }>>(() => []);
const myServices = computed<Array<{ title: string; price: string; bookings: string; status: string }>>(() => []);
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
                    <p class="text-xs text-muted-foreground">Your account is ready. List your services and packages to start receiving bookings and client inquiries.</p>
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
                    <span class="text-xs font-medium text-muted-foreground">
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
                </div>

                <!-- Empty state when no bookings -->
                <div v-if="upcomingBookings.length === 0" class="flex flex-col items-center justify-center py-16 text-center">
                    <div class="rounded-full bg-emerald-500/10 p-3.5 text-emerald-600 dark:text-emerald-400 mb-3">
                        <CalendarCheck2 class="size-7" />
                    </div>
                    <h3 class="text-sm font-semibold text-foreground">No Bookings Scheduled Yet</h3>
                    <p class="mt-1 text-xs text-muted-foreground max-w-sm">
                        You have no confirmed gigs or client reservations currently. Once clients select your packages, your timeline will appear here.
                    </p>
                </div>

                <!-- Populated bookings list -->
                <div v-else class="mt-4 space-y-3">
                    <div
                        v-for="(booking, idx) in upcomingBookings"
                        :key="idx"
                        class="rounded-lg border border-border/60 bg-muted/20 p-4"
                    >
                        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <h4 class="text-sm font-semibold text-foreground">{{ booking.event }}</h4>
                                <p class="text-xs text-muted-foreground">Client: <span class="font-medium text-foreground">{{ booking.client }}</span></p>
                            </div>
                            <span class="text-sm font-bold text-foreground">{{ booking.amount }}</span>
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

                    <!-- Empty state when no services listed -->
                    <div v-if="myServices.length === 0" class="flex flex-col items-center justify-center py-10 text-center">
                        <div class="rounded-full bg-muted p-2.5 text-muted-foreground mb-2.5">
                            <PackagePlus class="size-5" />
                        </div>
                        <h4 class="text-xs font-semibold text-foreground">No Packages Created</h4>
                        <p class="mt-0.5 text-[11px] text-muted-foreground max-w-[200px]">
                            Publish your service packages with pricing so clients can discover and book you.
                        </p>
                        <button class="mt-4 inline-flex items-center gap-1.5 rounded-lg bg-emerald-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-emerald-700">
                            <Plus class="size-3" /> Create First Package
                        </button>
                    </div>

                    <!-- Populated services list -->
                    <div v-else class="mt-4 space-y-3">
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
                </div>

                <div class="rounded-xl border border-border/70 bg-card p-5 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <span class="text-xs text-muted-foreground">Available Payout Balance</span>
                            <p class="text-2xl font-bold text-foreground">TZS 0</p>
                        </div>
                        <button disabled class="rounded-lg bg-muted px-3 py-2 text-xs font-medium text-muted-foreground cursor-not-allowed">
                            Request Payout
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
