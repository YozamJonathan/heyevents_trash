<script setup lang="ts">
import {
    Users,
    CheckCircle,
    XCircle,
    AlertCircle,
    Calendar,
    Briefcase,
    TrendingUp,
    ShieldCheck,
    ArrowUpRight,
    MessageSquare,
    Eye,
} from '@lucide/vue';

const metrics = [
    { title: 'Pending Vendor KYC', value: '7 Applications', change: 'Action Needed', icon: ShieldCheck, alert: true },
    { title: 'Events in Moderation', value: '14 Active', change: '3 flagged', icon: Calendar, alert: false },
    { title: 'Open Dispute Tickets', value: '2 Pending', change: '-50% this week', icon: AlertCircle, alert: true },
    { title: 'Monthly Booking GMV', value: '$34,120', change: '+14.5%', icon: TrendingUp, alert: false },
];

const pendingVendors = [
    { name: 'Aurora Sound & Lighting', owner: 'Michael Chen', category: 'Audio/Visual & DJ', submitted: '2 hours ago', kycStatus: 'Documents Uploaded' },
    { name: 'Grand Royale Catering', owner: 'Chef Isabella', category: 'Catering & Dining', submitted: '5 hours ago', kycStatus: 'Pending Review' },
    { name: 'Luxe Floral Artistry', owner: 'Amara Okafor', category: 'Floral & Decor', submitted: 'Yesterday', kycStatus: 'Documents Uploaded' },
];

const flaggedEvents = [
    { title: 'Neon Nights Summer Gala 2026', organizer: 'Club Pulse', reason: 'Venue capacity check', date: 'Oct 24, 2026' },
    { title: 'Global Tech Leadership Expo', organizer: 'Apex Media', reason: 'Pricing clarification', date: 'Nov 12, 2026' },
];
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
                    <div
                        class="rounded-lg p-2"
                        :class="metric.alert ? 'bg-amber-500/10 text-amber-600 dark:text-amber-400' : 'bg-blue-500/10 text-blue-600 dark:text-blue-400'"
                    >
                        <component :is="metric.icon" class="size-4" />
                    </div>
                </div>
                <div class="mt-3 flex items-baseline gap-2">
                    <span class="text-2xl font-bold tracking-tight text-foreground">{{ metric.value }}</span>
                    <span
                        class="inline-flex items-center text-xs font-medium"
                        :class="metric.alert ? 'text-amber-600 dark:text-amber-400' : 'text-emerald-600 dark:text-emerald-400'"
                    >
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
                    <span class="rounded-full bg-blue-500/10 px-2.5 py-1 text-xs font-semibold text-blue-600 dark:text-blue-400">
                        3 Awaiting Review
                    </span>
                </div>

                <div class="mt-4 divide-y divide-border/40">
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

                        <div class="flex items-center gap-2">
                            <button class="inline-flex items-center gap-1 rounded-lg border border-border bg-background px-2.5 py-1.5 text-xs font-medium text-foreground hover:bg-muted">
                                <Eye class="size-3.5" /> Review Files
                            </button>
                            <button class="inline-flex items-center gap-1 rounded-lg bg-emerald-600 px-2.5 py-1.5 text-xs font-medium text-white hover:bg-emerald-700">
                                <CheckCircle class="size-3.5" /> Approve
                            </button>
                            <button class="inline-flex items-center gap-1 rounded-lg border border-rose-500/30 bg-rose-500/10 px-2 py-1.5 text-xs font-medium text-rose-600 hover:bg-rose-500/20 dark:text-rose-400">
                                <XCircle class="size-3.5" />
                            </button>
                        </div>
                    </div>
                </div>

                <div class="mt-4 border-t border-border/50 pt-4 flex justify-end">
                    <button class="text-xs font-medium text-primary hover:underline">
                        View all 7 verification requests &rarr;
                    </button>
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

                    <div class="mt-4 space-y-3">
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
                            <div class="mt-2 flex items-center justify-end gap-2">
                                <button class="text-[11px] font-medium text-primary hover:underline">Approve</button>
                                <button class="text-[11px] font-medium text-rose-600 hover:underline">Inquire</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="rounded-xl border border-border/70 bg-card p-5 shadow-sm">
                    <div class="flex items-center gap-3">
                        <div class="rounded-lg bg-blue-500/10 p-2 text-blue-600 dark:text-blue-400">
                            <MessageSquare class="size-4" />
                        </div>
                        <div>
                            <h3 class="text-sm font-semibold text-foreground">Dispute Resolution Desk</h3>
                            <p class="text-xs text-muted-foreground">2 client-vendor dispute cases need mediation.</p>
                        </div>
                    </div>
                    <button class="mt-4 w-full rounded-lg bg-primary py-2 text-xs font-medium text-primary-foreground hover:bg-primary/90">
                        Open Resolution Desk
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
