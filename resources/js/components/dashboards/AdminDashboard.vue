<script setup lang="ts">
import { computed } from 'vue';
import {
    Users,
    Calendar,
    ShieldCheck,
    MessageSquare,
    CheckCircle2,
    BadgeCheck,
    ArrowUpRight,
    Shield,
    TrendingUp,
    Sliders,
} from '@lucide/vue';
import { Link } from '@inertiajs/vue3';
import { useLanguage } from '@/composables/useLanguage';

interface VendorProfile {
    id: number;
    user_id: number;
    brand_name: string;
    contact_name: string;
    status: 'pending' | 'approved' | 'rejected';
}

const props = defineProps<{
    stats?: {
        users_count: number;
        roles_count: number;
        permissions_count: number;
        pending_kyc_count?: number;
        verified_vendors_count?: number;
    };
    pendingVendors?: VendorProfile[];
    verifiedVendors?: VendorProfile[];
    rejectedVendors?: VendorProfile[];
}>();

const { t } = useLanguage();

const pendingList = computed(() => props.pendingVendors ?? []);
const verifiedList = computed(() => props.verifiedVendors ?? []);

const metrics = computed(() => [
    {
        title: t('adminDashboard.pendingKyc') || 'Pending KYC',
        value: `${pendingList.value.length} ${t('adminDashboard.awaitingReview') || 'Pending'}`,
        change:
            pendingList.value.length > 0
                ? `${pendingList.value.length} need approval →`
                : t('adminDashboard.allClear') || 'All Clear',
        icon: ShieldCheck,
        alert: pendingList.value.length > 0,
        href: '/admin/vendor-approvals',
    },
    {
        title: t('adminDashboard.tabVerified') || 'Verified Vendors',
        value: `${verifiedList.value.length} Active`,
        change: 'Official Partners →',
        icon: BadgeCheck,
        alert: false,
        href: '/admin/vendor-approvals',
    },
    {
        title: t('adminDashboard.moderation') || 'Event Moderation',
        value: t('adminDashboard.flagged') || '0 Flagged',
        change: t('adminDashboard.queueClear') || 'Queue Clear',
        icon: Calendar,
        alert: false,
    },
    {
        title: t('adminDashboard.accounts') || 'Platform Accounts',
        value: `${props.stats?.users_count ?? 4} ${t('common.users') || 'Users'}`,
        change: t('adminDashboard.active') || 'Active',
        icon: Users,
        alert: false,
    },
]);

const flaggedEvents = computed<
    Array<{ title: string; reason: string; date: string }>
>(() => []);
</script>

<template>
    <div class="space-y-6">
        <!-- Moderation Metrics Bar (Clicking Pending KYC directs straight to Vendor Approvals) -->
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <component
                :is="metric.href ? Link : 'div'"
                v-for="metric in metrics"
                :key="metric.title"
                :href="metric.href"
                class="group border-border/70 bg-card rounded-2xl border p-5 shadow-sm transition-all"
                :class="[
                    metric.alert
                        ? 'border-amber-500/50 bg-amber-500/10 hover:border-amber-500/80 hover:shadow-md'
                        : 'hover:border-border/90 hover:shadow-md',
                    metric.href ? 'cursor-pointer' : '',
                ]"
            >
                <div class="flex items-center justify-between">
                    <span
                        class="text-muted-foreground text-xs font-semibold tracking-wider uppercase"
                    >
                        {{ metric.title }}
                    </span>
                    <div
                        class="rounded-xl p-2 transition group-hover:scale-105"
                        :class="
                            metric.alert
                                ? 'bg-amber-500/20 text-amber-700 dark:text-amber-400'
                                : 'bg-primary/10 text-primary'
                        "
                    >
                        <component :is="metric.icon" class="size-4" />
                    </div>
                </div>
                <div class="mt-3 flex items-baseline justify-between">
                    <span class="text-foreground text-2xl font-black tracking-tight">
                        {{ metric.value }}
                    </span>
                    <span
                        class="inline-flex items-center gap-1 text-xs font-semibold"
                        :class="metric.alert ? 'text-amber-700 dark:text-amber-400' : 'text-emerald-600 dark:text-emerald-400'"
                    >
                        {{ metric.change }}
                    </span>
                </div>
            </component>
        </div>

        <!-- Operations & Governance Grid -->
        <div class="grid gap-6 md:grid-cols-2">
            <!-- Event Moderation Queue -->
            <div class="border-border/70 bg-card rounded-2xl border p-6 shadow-sm">
                <div class="border-border/50 flex items-center justify-between border-b pb-4">
                    <div>
                        <h2 class="text-foreground text-base font-semibold">
                            {{ t('adminDashboard.moderationQueue') }}
                        </h2>
                        <p class="text-muted-foreground text-xs">
                            {{ t('adminDashboard.reviewListings') }}
                        </p>
                    </div>
                    <Calendar class="size-4 text-blue-600 dark:text-blue-400" />
                </div>

                <!-- Empty state when no flagged events -->
                <div
                    v-if="flaggedEvents.length === 0"
                    class="flex flex-col items-center justify-center py-12 text-center"
                >
                    <div
                        class="mb-2.5 rounded-full bg-emerald-500/10 p-2.5 text-emerald-600 dark:text-emerald-400"
                    >
                        <CheckCircle2 class="size-5" />
                    </div>
                    <h4 class="text-foreground text-xs font-semibold">
                        {{ t('adminDashboard.moderationClear') }}
                    </h4>
                    <p class="text-muted-foreground mt-0.5 max-w-[220px] text-[11px]">
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
                            <h4 class="text-foreground text-xs font-semibold">
                                {{ ev.title }}
                            </h4>
                            <span class="text-muted-foreground text-[10px]">{{ ev.date }}</span>
                        </div>
                        <p class="mt-1 text-[11px] font-medium text-amber-600 dark:text-amber-400">
                            {{ t('adminDashboard.reason') }}: {{ ev.reason }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Dispute Resolution Desk -->
            <div class="border-border/70 bg-card flex flex-col justify-between rounded-2xl border p-6 shadow-sm">
                <div>
                    <div class="border-border/50 flex items-center justify-between border-b pb-4">
                        <div>
                            <h2 class="text-foreground text-base font-semibold">
                                {{ t('adminDashboard.disputeDesk') }}
                            </h2>
                            <p class="text-muted-foreground text-xs">
                                Escrow protection and client-vendor conflict arbitration.
                            </p>
                        </div>
                        <Shield class="size-4 text-emerald-600 dark:text-emerald-400" />
                    </div>

                    <div class="flex flex-col items-center justify-center py-12 text-center">
                        <div class="mb-2.5 rounded-full bg-emerald-500/10 p-2.5 text-emerald-600 dark:text-emerald-400">
                            <MessageSquare class="size-5" />
                        </div>
                        <h4 class="text-foreground text-xs font-semibold">
                            No Open Conflict Cases
                        </h4>
                        <p class="text-muted-foreground mt-0.5 max-w-[220px] text-[11px]">
                            {{ t('adminDashboard.noDisputes') }}
                        </p>
                    </div>
                </div>

                <div class="border-t border-border/50 pt-3 text-[11px] text-muted-foreground flex items-center justify-between">
                    <span>Dispute Desk Guard Active</span>
                    <span class="text-emerald-600 dark:text-emerald-400 font-semibold">100% Peaceful</span>
                </div>
            </div>
        </div>
    </div>
</template>