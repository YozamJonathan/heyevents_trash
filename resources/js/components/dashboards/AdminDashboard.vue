<script setup lang="ts">
import { computed } from 'vue';
import {
    Users,
    CheckCircle,
    Calendar,
    Briefcase,
    ShieldCheck,
    MessageSquare,
    CheckCircle2,
    Inbox,
} from '@lucide/vue';
import { useLanguage } from '@/composables/useLanguage';

const props = defineProps<{
    stats?: {
        users_count: number;
        roles_count: number;
        permissions_count: number;
    };
}>();

const { t } = useLanguage();

const metrics = computed(() => [
    {
        title: t('adminDashboard.pendingKyc'),
        value: t('adminDashboard.pending'),
        change: t('adminDashboard.allClear'),
        icon: ShieldCheck,
        alert: false,
    },
    {
        title: t('adminDashboard.moderation'),
        value: t('adminDashboard.flagged'),
        change: t('adminDashboard.queueClear'),
        icon: Calendar,
        alert: false,
    },
    {
        title: t('adminDashboard.disputes'),
        value: t('adminDashboard.open'),
        change: t('adminDashboard.noConflicts'),
        icon: MessageSquare,
        alert: false,
    },
    {
        title: t('adminDashboard.accounts'),
        value: `${props.stats?.users_count ?? 4} ${t('common.users')}`,
        change: t('adminDashboard.active'),
        icon: Users,
        alert: false,
    },
]);

// Real items (starts empty)
const pendingVendors = computed<
    Array<{ name: string; owner: string; category: string; submitted: string }>
>(() => []);
const flaggedEvents = computed<
    Array<{ title: string; organizer: string; reason: string; date: string }>
>(() => []);
</script>

<template>
    <div class="space-y-6">
        <!-- Metrics Row -->
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div
                v-for="metric in metrics"
                :key="metric.title"
                class="border-border/70 bg-card rounded-xl border p-5 shadow-sm transition-all hover:border-blue-500/40"
            >
                <div class="flex items-center justify-between">
                    <span
                        class="text-muted-foreground text-xs font-medium tracking-wider uppercase"
                        >{{ metric.title }}</span
                    >
                    <div
                        class="rounded-lg bg-blue-500/10 p-2 text-blue-600 dark:text-blue-400"
                    >
                        <component :is="metric.icon" class="size-4" />
                    </div>
                </div>
                <div class="mt-3 flex items-baseline gap-2">
                    <span
                        class="text-foreground text-2xl font-bold tracking-tight"
                        >{{ metric.value }}</span
                    >
                    <span
                        class="inline-flex items-center text-xs font-medium text-emerald-600 dark:text-emerald-400"
                    >
                        {{ metric.change }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Admin Operations Hub -->
        <div class="grid gap-6 lg:grid-cols-3">
            <!-- Pending Vendor Verifications -->
            <div
                class="border-border/70 bg-card rounded-xl border p-6 shadow-sm lg:col-span-2"
            >
                <div
                    class="border-border/50 flex items-center justify-between border-b pb-4"
                >
                    <div>
                        <h2 class="text-foreground text-lg font-semibold">
                            {{ t('adminDashboard.verifications') }}
                        </h2>
                        <p class="text-muted-foreground text-xs">
                            {{ t('adminDashboard.verificationDescription') }}
                        </p>
                    </div>
                    <span
                        class="rounded-full bg-emerald-500/10 px-2.5 py-1 text-xs font-semibold text-emerald-600 dark:text-emerald-400"
                    >
                        {{ t('adminDashboard.awaitingReview') }}
                    </span>
                </div>

                <!-- Empty state when no pending KYC -->
                <div
                    v-if="pendingVendors.length === 0"
                    class="flex flex-col items-center justify-center py-16 text-center"
                >
                    <div
                        class="mb-3 rounded-full bg-blue-500/10 p-3.5 text-blue-600 dark:text-blue-400"
                    >
                        <CheckCircle class="size-7" />
                    </div>
                    <h3 class="text-foreground text-sm font-semibold">
                        {{ t('adminDashboard.reviewed') }}
                    </h3>
                    <p class="text-muted-foreground mt-1 max-w-sm text-xs">
                        {{ t('adminDashboard.noPending') }}
                    </p>
                </div>

                <!-- Populated list if vendors present -->
                <div v-else class="divide-border/40 mt-4 divide-y">
                    <div
                        v-for="(vendor, idx) in pendingVendors"
                        :key="idx"
                        class="flex flex-col gap-3 py-4 first:pt-2 sm:flex-row sm:items-center sm:justify-between"
                    >
                        <div class="space-y-1">
                            <div class="flex items-center gap-2">
                                <h3
                                    class="text-foreground text-sm font-semibold"
                                >
                                    {{ vendor.name }}
                                </h3>
                                <span
                                    class="bg-muted text-muted-foreground rounded px-2 py-0.5 text-[10px] font-medium"
                                    >{{ vendor.category }}</span
                                >
                            </div>
                            <p class="text-muted-foreground text-xs">
                                Owner:
                                <span class="text-foreground">{{
                                    vendor.owner
                                }}</span>
                                • Applied {{ vendor.submitted }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Event Moderation & Disputes -->
            <div class="space-y-6">
                <div
                    class="border-border/70 bg-card rounded-xl border p-6 shadow-sm"
                >
                    <div
                        class="border-border/50 flex items-center justify-between border-b pb-4"
                    >
                        <div>
                            <h2 class="text-foreground text-base font-semibold">
                                {{ t('adminDashboard.moderationQueue') }}
                            </h2>
                            <p class="text-muted-foreground text-xs">
                                {{ t('adminDashboard.reviewListings') }}
                            </p>
                        </div>
                        <Calendar
                            class="size-4 text-blue-600 dark:text-blue-400"
                        />
                    </div>

                    <!-- Empty state when no flagged events -->
                    <div
                        v-if="flaggedEvents.length === 0"
                        class="flex flex-col items-center justify-center py-10 text-center"
                    >
                        <div
                            class="mb-2.5 rounded-full bg-emerald-500/10 p-2.5 text-emerald-600 dark:text-emerald-400"
                        >
                            <CheckCircle2 class="size-5" />
                        </div>
                        <h4 class="text-foreground text-xs font-semibold">
                            {{ t('adminDashboard.moderationClear') }}
                        </h4>
                        <p
                            class="text-muted-foreground mt-0.5 max-w-[200px] text-[11px]"
                        >
                            {{ t('adminDashboard.noFlags') }}
                        </p>
                    </div>

                    <!-- Populated list if flagged events present -->
                    <div v-else class="mt-4 space-y-3">
                        <div
                            v-for="(ev, idx) in flaggedEvents"
                            :key="idx"
                            class="border-border/60 bg-muted/30 rounded-lg border p-3"
                        >
                            <div class="flex items-start justify-between">
                                <h4
                                    class="text-foreground text-xs font-semibold"
                                >
                                    {{ ev.title }}
                                </h4>
                                <span
                                    class="text-muted-foreground text-[10px]"
                                    >{{ ev.date }}</span
                                >
                            </div>
                            <p
                                class="mt-1 text-[11px] font-medium text-amber-600 dark:text-amber-400"
                            >
                                {{ t('adminDashboard.reason') }}:
                                {{ ev.reason }}
                            </p>
                        </div>
                    </div>
                </div>

                <div
                    class="border-border/70 bg-card rounded-xl border p-5 shadow-sm"
                >
                    <div class="flex items-center gap-3">
                        <div
                            class="rounded-lg bg-emerald-500/10 p-2 text-emerald-600 dark:text-emerald-400"
                        >
                            <MessageSquare class="size-4" />
                        </div>
                        <div>
                            <h3 class="text-foreground text-sm font-semibold">
                                {{ t('adminDashboard.disputeDesk') }}
                            </h3>
                            <p class="text-muted-foreground text-xs">
                                {{ t('adminDashboard.noDisputes') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
