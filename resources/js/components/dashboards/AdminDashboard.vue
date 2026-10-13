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

const props = defineProps<{
    stats?: {
        users_count: number;
        roles_count: number;
        permissions_count: number;
    };
}>();

const metrics = computed(() => [
    { title: 'Pending Vendor KYC', value: '0 Pending', change: 'All Clear', icon: ShieldCheck, alert: false },
    { title: 'Events in Moderation', value: '0 Flagged', change: 'Queue Clear', icon: Calendar, alert: false },
    { title: 'Open Dispute Tickets', value: '0 Open', change: 'No Conflicts', icon: MessageSquare, alert: false },
    {
        title: 'Platform Accounts',
        value: props.stats?.users_count !== undefined ? `${props.stats.users_count} Users` : '4 Users',
        change: 'Active',
        icon: Users,
        alert: false,
    },
]);

// Real items (starts empty)
const pendingVendors = computed<Array<{ name: string; owner: string; category: string; submitted: string }>>(() => []);
const flaggedEvents = computed<Array<{ title: string; organizer: string; reason: string; date: string }>>(() => []);
</script>

<template>
    <div class="space-y-6">
        <!-- Metrics Row -->
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div
                v-for="metric in metrics"
                :key="metric.title"
                class="rounded-xl border border-border/70 bg-card p-5 shadow-sm transition-all hover:border-blue-500/40"
            >
                <div class="flex items-center justify-between">
                    <span class="text-xs font-medium uppercase tracking-wider text-muted-foreground">{{ metric.title }}</span>
                    <div class="rounded-lg p-2 bg-blue-500/10 text-blue-600 dark:text-blue-400">
                        <component :is="metric.icon" class="size-4" />
                    </div>
                </div>
                <div class="mt-3 flex items-baseline gap-2">
                    <span class="text-2xl font-bold tracking-tight text-foreground">{{ metric.value }}</span>
                    <span class="inline-flex items-center text-xs font-medium text-emerald-600 dark:text-emerald-400">
                        {{ metric.change }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Admin Operations Hub -->
        <div class="grid gap-6 lg:grid-cols-3">
            <!-- Pending Vendor Verifications -->
            <div class="rounded-xl border border-border/70 bg-card p-6 shadow-sm lg:col-span-2">
                <div class="flex items-center justify-between border-b border-border/50 pb-4">
                    <div>
                        <h2 class="text-lg font-semibold text-foreground">Service Provider Verifications (KYC)</h2>
                        <p class="text-xs text-muted-foreground">Approve or reject service provider onboarding applications.</p>
                    </div>
                    <span class="rounded-full bg-emerald-500/10 px-2.5 py-1 text-xs font-semibold text-emerald-600 dark:text-emerald-400">
                        0 Awaiting Review
                    </span>
                </div>

                <!-- Empty state when no pending KYC -->
                <div v-if="pendingVendors.length === 0" class="flex flex-col items-center justify-center py-16 text-center">
                    <div class="rounded-full bg-blue-500/10 p-3.5 text-blue-600 dark:text-blue-400 mb-3">
                        <CheckCircle class="size-7" />
                    </div>
                    <h3 class="text-sm font-semibold text-foreground">All Provider Applications Reviewed</h3>
                    <p class="mt-1 text-xs text-muted-foreground max-w-sm">
                        There are currently no pending vendor KYC verifications. New provider submissions will appear here for review and onboarding.
                    </p>
                </div>

                <!-- Populated list if vendors present -->
                <div v-else class="mt-4 divide-y divide-border/40">
                    <div
                        v-for="(vendor, idx) in pendingVendors"
                        :key="idx"
                        class="flex flex-col gap-3 py-4 first:pt-2 sm:flex-row sm:items-center sm:justify-between"
                    >
                        <div class="space-y-1">
                            <div class="flex items-center gap-2">
                                <h3 class="text-sm font-semibold text-foreground">{{ vendor.name }}</h3>
                                <span class="rounded bg-muted px-2 py-0.5 text-[10px] font-medium text-muted-foreground">{{ vendor.category }}</span>
                            </div>
                            <p class="text-xs text-muted-foreground">
                                Owner: <span class="text-foreground">{{ vendor.owner }}</span> • Applied {{ vendor.submitted }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Event Moderation & Disputes -->
            <div class="space-y-6">
                <div class="rounded-xl border border-border/70 bg-card p-6 shadow-sm">
                    <div class="flex items-center justify-between border-b border-border/50 pb-4">
                        <div>
                            <h2 class="text-base font-semibold text-foreground">Event Moderation Queue</h2>
                            <p class="text-xs text-muted-foreground">Review flagged listings</p>
                        </div>
                        <Calendar class="size-4 text-blue-600 dark:text-blue-400" />
                    </div>

                    <!-- Empty state when no flagged events -->
                    <div v-if="flaggedEvents.length === 0" class="flex flex-col items-center justify-center py-10 text-center">
                        <div class="rounded-full bg-emerald-500/10 p-2.5 text-emerald-600 dark:text-emerald-400 mb-2.5">
                            <CheckCircle2 class="size-5" />
                        </div>
                        <h4 class="text-xs font-semibold text-foreground">Moderation Queue Clear</h4>
                        <p class="mt-0.5 text-[11px] text-muted-foreground max-w-[200px]">
                            No community flags or listings requiring review.
                        </p>
                    </div>

                    <!-- Populated list if flagged events present -->
                    <div v-else class="mt-4 space-y-3">
                        <div
                            v-for="(ev, idx) in flaggedEvents"
                            :key="idx"
                            class="rounded-lg border border-border/60 bg-muted/30 p-3"
                        >
                            <div class="flex items-start justify-between">
                                <h4 class="text-xs font-semibold text-foreground">{{ ev.title }}</h4>
                                <span class="text-[10px] text-muted-foreground">{{ ev.date }}</span>
                            </div>
                            <p class="mt-1 text-[11px] text-amber-600 dark:text-amber-400 font-medium">
                                Reason: {{ ev.reason }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="rounded-xl border border-border/70 bg-card p-5 shadow-sm">
                    <div class="flex items-center gap-3">
                        <div class="rounded-lg bg-emerald-500/10 p-2 text-emerald-600 dark:text-emerald-400">
                            <MessageSquare class="size-4" />
                        </div>
                        <div>
                            <h3 class="text-sm font-semibold text-foreground">Dispute Resolution Desk</h3>
                            <p class="text-xs text-muted-foreground">0 open dispute cases. All client-vendor transactions peaceful.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
