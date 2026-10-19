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
import { useLanguage } from '@/composables/useLanguage';

const { t } = useLanguage();

const metrics = computed(() => [
    {
        title: t('providerDashboard.earnings'),
        value: 'TZS 0',
        change: t('providerDashboard.newAccount'),
        icon: Banknote,
    },
    {
        title: t('providerDashboard.upcomingBookings'),
        value: t('providerDashboard.events'),
        change: t('providerDashboard.scheduleClear'),
        icon: Calendar,
    },
    {
        title: t('providerDashboard.packages'),
        value: t('providerDashboard.listed'),
        change: t('providerDashboard.addPackage'),
        icon: Briefcase,
    },
    {
        title: t('providerDashboard.rating'),
        value: t('providerDashboard.newProvider'),
        change: t('providerDashboard.awaitReviews'),
        icon: Star,
    },
]);

// Real items (starts empty)
const upcomingBookings = computed<
    Array<{
        client: string;
        event: string;
        date: string;
        time: string;
        location: string;
        amount: string;
        status: string;
    }>
>(() => []);
const myServices = computed<
    Array<{ title: string; price: string; bookings: string; status: string }>
>(() => []);
</script>

<template>
    <div class="space-y-6">
        <!-- Vendor Profile Status Card -->
        <div
            class="flex flex-col gap-4 rounded-xl border border-emerald-500/30 bg-emerald-500/5 p-4 sm:flex-row sm:items-center sm:justify-between dark:border-emerald-500/20"
        >
            <div class="flex items-center gap-3">
                <div
                    class="rounded-full bg-emerald-500/10 p-2.5 text-emerald-600 dark:text-emerald-400"
                >
                    <BadgeCheck class="size-6" />
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h3 class="text-foreground text-sm font-semibold">
                            {{ t('providerDashboard.verified') }}
                        </h3>
                        <span
                            class="rounded bg-emerald-500/20 px-2 py-0.5 text-[10px] font-bold text-emerald-700 uppercase dark:text-emerald-300"
                            >{{ t('providerDashboard.active') }}</span
                        >
                    </div>
                    <p class="text-muted-foreground text-xs">
                        {{ t('providerDashboard.ready') }}
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <button
                    class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-600 px-3 py-1.5 text-xs font-medium text-white shadow-sm hover:bg-emerald-700"
                >
                    <Plus class="size-3.5" />
                    {{ t('providerDashboard.addService') }}
                </button>
            </div>
        </div>

        <!-- Metrics Grid -->
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div
                v-for="metric in metrics"
                :key="metric.title"
                class="border-border/70 bg-card rounded-xl border p-5 shadow-sm transition-all hover:border-emerald-500/40"
            >
                <div class="flex items-center justify-between">
                    <span
                        class="text-muted-foreground text-xs font-medium tracking-wider uppercase"
                        >{{ metric.title }}</span
                    >
                    <div
                        class="rounded-lg bg-emerald-500/10 p-2 text-emerald-600 dark:text-emerald-400"
                    >
                        <component :is="metric.icon" class="size-4" />
                    </div>
                </div>
                <div class="mt-3 flex items-baseline gap-2">
                    <span
                        class="text-foreground text-2xl font-bold tracking-tight"
                        >{{ metric.value }}</span
                    >
                    <span class="text-muted-foreground text-xs font-medium">
                        {{ metric.change }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Provider Main Sections -->
        <div class="grid gap-6 lg:grid-cols-3">
            <!-- Upcoming Gigs & Bookings -->
            <div
                class="border-border/70 bg-card rounded-xl border p-6 shadow-sm lg:col-span-2"
            >
                <div
                    class="border-border/50 flex items-center justify-between border-b pb-4"
                >
                    <div>
                        <h2 class="text-foreground text-lg font-semibold">
                            {{ t('providerDashboard.schedule') }}
                        </h2>
                        <p class="text-muted-foreground text-xs">
                            {{ t('providerDashboard.scheduleDescription') }}
                        </p>
                    </div>
                </div>

                <!-- Empty state when no bookings -->
                <div
                    v-if="upcomingBookings.length === 0"
                    class="flex flex-col items-center justify-center py-16 text-center"
                >
                    <div
                        class="mb-3 rounded-full bg-emerald-500/10 p-3.5 text-emerald-600 dark:text-emerald-400"
                    >
                        <CalendarCheck2 class="size-7" />
                    </div>
                    <h3 class="text-foreground text-sm font-semibold">
                        {{ t('providerDashboard.noBookings') }}
                    </h3>
                    <p class="text-muted-foreground mt-1 max-w-sm text-xs">
                        {{ t('providerDashboard.noBookingsDescription') }}
                    </p>
                </div>

                <!-- Populated bookings list -->
                <div v-else class="mt-4 space-y-3">
                    <div
                        v-for="(booking, idx) in upcomingBookings"
                        :key="idx"
                        class="border-border/60 bg-muted/20 rounded-lg border p-4"
                    >
                        <div
                            class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between"
                        >
                            <div>
                                <h4
                                    class="text-foreground text-sm font-semibold"
                                >
                                    {{ booking.event }}
                                </h4>
                                <p class="text-muted-foreground text-xs">
                                    {{ t('providerDashboard.client') }}:
                                    <span class="text-foreground font-medium">{{
                                        booking.client
                                    }}</span>
                                </p>
                            </div>
                            <span class="text-foreground text-sm font-bold">{{
                                booking.amount
                            }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- My Service Listings & Payouts -->
            <div class="space-y-6">
                <div
                    class="border-border/70 bg-card rounded-xl border p-6 shadow-sm"
                >
                    <div
                        class="border-border/50 flex items-center justify-between border-b pb-4"
                    >
                        <h3 class="text-foreground text-base font-semibold">
                            {{ t('providerDashboard.catalog') }}
                        </h3>
                        <Briefcase
                            class="size-4 text-emerald-600 dark:text-emerald-400"
                        />
                    </div>

                    <!-- Empty state when no services listed -->
                    <div
                        v-if="myServices.length === 0"
                        class="flex flex-col items-center justify-center py-10 text-center"
                    >
                        <div
                            class="bg-muted text-muted-foreground mb-2.5 rounded-full p-2.5"
                        >
                            <PackagePlus class="size-5" />
                        </div>
                        <h4 class="text-foreground text-xs font-semibold">
                            {{ t('providerDashboard.noPackages') }}
                        </h4>
                        <p
                            class="text-muted-foreground mt-0.5 max-w-[200px] text-[11px]"
                        >
                            {{ t('providerDashboard.noPackagesDescription') }}
                        </p>
                        <button
                            class="mt-4 inline-flex items-center gap-1.5 rounded-lg bg-emerald-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-emerald-700"
                        >
                            <Plus class="size-3" />
                            {{ t('providerDashboard.createPackage') }}
                        </button>
                    </div>

                    <!-- Populated services list -->
                    <div v-else class="mt-4 space-y-3">
                        <div
                            v-for="(svc, idx) in myServices"
                            :key="idx"
                            class="border-border/60 bg-muted/30 rounded-lg border p-3"
                        >
                            <div class="flex items-start justify-between">
                                <h4
                                    class="text-foreground line-clamp-1 text-xs font-semibold"
                                >
                                    {{ svc.title }}
                                </h4>
                                <span
                                    class="rounded bg-emerald-500/10 px-1.5 py-0.5 text-[10px] font-medium text-emerald-600"
                                    >{{ svc.status }}</span
                                >
                            </div>
                            <div
                                class="mt-2 flex items-center justify-between text-xs"
                            >
                                <span class="text-foreground font-bold">{{
                                    svc.price
                                }}</span>
                                <span
                                    class="text-muted-foreground text-[11px]"
                                    >{{ svc.bookings }}</span
                                >
                            </div>
                        </div>
                    </div>
                </div>

                <div
                    class="border-border/70 bg-card rounded-xl border p-5 shadow-sm"
                >
                    <div class="flex items-center justify-between">
                        <div>
                            <span class="text-muted-foreground text-xs">{{
                                t('providerDashboard.payoutBalance')
                            }}</span>
                            <p class="text-foreground text-2xl font-bold">
                                TZS 0
                            </p>
                        </div>
                        <button
                            disabled
                            class="bg-muted text-muted-foreground cursor-not-allowed rounded-lg px-3 py-2 text-xs font-medium"
                        >
                            {{ t('providerDashboard.requestPayout') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
