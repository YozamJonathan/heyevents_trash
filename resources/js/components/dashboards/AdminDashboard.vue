<script setup lang="ts">
import { computed, ref } from 'vue';
import {
    Users,
    CheckCircle,
    Calendar,
    Briefcase,
    ShieldCheck,
    MessageSquare,
    CheckCircle2,
    Inbox,
    FileText,
    ExternalLink,
    Check,
    X,
    Loader2,
    Phone,
    Camera,
    MessageCircle,
    Video,
} from '@lucide/vue';
import { router } from '@inertiajs/vue3';
import { useLanguage } from '@/composables/useLanguage';

interface PendingVendor {
    id: number;
    user_id: number;
    brand_name: string;
    contact_name: string;
    nida_number: string;
    primary_phone?: string;
    secondary_phone?: string;
    business_licence_path?: string;
    business_licence_url?: string;
    social_media?: string;
    instagram?: string;
    whatsapp?: string;
    tiktok?: string;
    category?: string;
    categories?: string[];
    description?: string;
    status: string;
    created_at?: string;
    user?: {
        id: number;
        name: string;
        email: string;
    };
}

const props = defineProps<{
    stats?: {
        users_count: number;
        roles_count: number;
        permissions_count: number;
        pending_kyc_count?: number;
    };
    pendingVendors?: PendingVendor[];
}>();

const { t } = useLanguage();

const processingId = ref<number | null>(null);

function approveVendor(vendor: PendingVendor) {
    if (!confirm(t('adminDashboard.confirmApprove'))) return;
    processingId.value = vendor.id;
    router.post(
        `/admin/vendors/${vendor.id}/approve`,
        {},
        {
            preserveScroll: true,
            onFinish: () => {
                processingId.value = null;
            },
        },
    );
}

function rejectVendor(vendor: PendingVendor) {
    const reason = window.prompt(t('adminDashboard.rejectPrompt'), '');
    if (reason === null) return;
    processingId.value = vendor.id;
    router.post(
        `/admin/vendors/${vendor.id}/reject`,
        { reason },
        {
            preserveScroll: true,
            onFinish: () => {
                processingId.value = null;
            },
        },
    );
}

const vendorList = computed(() => props.pendingVendors ?? []);

const metrics = computed(() => [
    {
        title: t('adminDashboard.pendingKyc'),
        value: `${vendorList.value.length} ${t('adminDashboard.awaitingReview')}`,
        change:
            vendorList.value.length > 0
                ? `${vendorList.value.length} pending`
                : t('adminDashboard.allClear'),
        icon: ShieldCheck,
        alert: vendorList.value.length > 0,
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
                        class="inline-flex items-center text-xs font-medium"
                        :class="metric.alert ? 'text-amber-600 dark:text-amber-400 font-semibold' : 'text-emerald-600 dark:text-emerald-400'"
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
                        class="rounded-full px-2.5 py-1 text-xs font-semibold"
                        :class="
                            vendorList.length > 0
                                ? 'bg-amber-500/15 text-amber-700 dark:text-amber-300'
                                : 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400'
                        "
                    >
                        {{ vendorList.length }} {{ t('adminDashboard.awaitingReview') }}
                    </span>
                </div>

                <!-- Empty state when no pending KYC -->
                <div
                    v-if="vendorList.length === 0"
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
                        v-for="vendor in vendorList"
                        :key="vendor.id"
                        class="flex flex-col gap-3 py-4 first:pt-2"
                    >
                        <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
                            <div class="space-y-1">
                                <div class="flex flex-wrap items-center gap-2">
                                    <h3 class="text-foreground text-sm font-semibold">
                                        {{ vendor.brand_name }}
                                    </h3>
                                    <!-- Service category tags -->
                                    <div class="flex flex-wrap items-center gap-1">
                                        <span
                                            v-for="cat in (vendor.categories && vendor.categories.length > 0 ? vendor.categories : [vendor.category || 'Service Provider'])"
                                            :key="cat"
                                            class="bg-blue-500/10 text-blue-700 dark:text-blue-300 rounded px-2 py-0.5 text-[10px] font-medium"
                                        >
                                            {{ cat }}
                                        </span>
                                    </div>
                                </div>
                                <div class="flex flex-wrap items-center gap-x-3 gap-y-1 text-muted-foreground text-xs">
                                    <span>
                                        {{ t('adminDashboard.owner') }}:
                                        <strong class="text-foreground">{{ vendor.contact_name }}</strong>
                                    </span>
                                    <span v-if="vendor.user?.email" class="font-mono">
                                        ({{ vendor.user.email }})
                                    </span>
                                    <a
                                        v-if="vendor.primary_phone"
                                        :href="`tel:${vendor.primary_phone}`"
                                        class="inline-flex items-center gap-1 font-mono text-foreground hover:text-[#ff545a] transition"
                                    >
                                        <Phone class="size-3 text-emerald-600 dark:text-emerald-400" />
                                        <span>{{ vendor.primary_phone }}</span>
                                    </a>
                                    <span v-if="vendor.secondary_phone" class="font-mono text-muted-foreground text-[11px]">
                                        (Alt: {{ vendor.secondary_phone }})
                                    </span>
                                </div>
                            </div>

                            <!-- Action buttons -->
                            <div class="flex items-center gap-2 self-start sm:self-auto shrink-0">
                                <button
                                    type="button"
                                    :disabled="processingId === vendor.id"
                                    @click="approveVendor(vendor)"
                                    class="inline-flex items-center gap-1.5 rounded-xl bg-emerald-600 px-3.5 py-1.5 text-xs font-semibold text-white shadow-sm transition hover:bg-emerald-700 disabled:opacity-50"
                                >
                                    <Loader2 v-if="processingId === vendor.id" class="size-3.5 animate-spin" />
                                    <Check v-else class="size-3.5" />
                                    {{ t('adminDashboard.approve') }}
                                </button>
                                <button
                                    type="button"
                                    :disabled="processingId === vendor.id"
                                    @click="rejectVendor(vendor)"
                                    class="inline-flex items-center gap-1.5 rounded-xl border border-rose-500/40 bg-rose-500/10 px-3 py-1.5 text-xs font-medium text-rose-700 transition hover:bg-rose-500/20 disabled:opacity-50 dark:text-rose-300"
                                >
                                    <X class="size-3.5" />
                                    {{ t('adminDashboard.reject') }}
                                </button>
                            </div>
                        </div>

                        <!-- Submitted KYC verification attributes -->
                        <div class="grid gap-2.5 rounded-lg border border-border/50 bg-muted/20 p-3 sm:grid-cols-4 text-xs">
                            <div>
                                <span class="text-muted-foreground text-[10px] uppercase font-medium block">
                                    {{ t('adminDashboard.nida') }}
                                </span>
                                <span class="font-mono text-foreground font-medium">{{ vendor.nida_number }}</span>
                            </div>

                            <div>
                                <span class="text-muted-foreground text-[10px] uppercase font-medium block">
                                    Contact Phones
                                </span>
                                <span class="font-mono text-foreground block">{{ vendor.primary_phone || '—' }}</span>
                                <span v-if="vendor.secondary_phone" class="font-mono text-muted-foreground text-[11px] block">
                                    Alt: {{ vendor.secondary_phone }}
                                </span>
                            </div>

                            <div>
                                <span class="text-muted-foreground text-[10px] uppercase font-medium block">
                                    Social Channels
                                </span>
                                <div class="flex flex-wrap items-center gap-2 mt-0.5">
                                    <span v-if="vendor.instagram" class="inline-flex items-center gap-1 text-[11px] text-pink-600 dark:text-pink-400">
                                        <Camera class="size-3" />
                                        <span>{{ vendor.instagram }}</span>
                                    </span>
                                    <span v-if="vendor.whatsapp" class="inline-flex items-center gap-1 text-[11px] text-emerald-600 dark:text-emerald-400">
                                        <MessageCircle class="size-3" />
                                        <span>{{ vendor.whatsapp }}</span>
                                    </span>
                                    <span v-if="vendor.tiktok" class="inline-flex items-center gap-1 text-[11px] text-indigo-600 dark:text-indigo-400">
                                        <Video class="size-3" />
                                        <span>{{ vendor.tiktok }}</span>
                                    </span>
                                    <span v-if="!vendor.instagram && !vendor.whatsapp && !vendor.tiktok" class="text-muted-foreground text-[11px]">
                                        {{ vendor.social_media || 'None' }}
                                    </span>
                                </div>
                            </div>

                            <div>
                                <span class="text-muted-foreground text-[10px] uppercase font-medium block">
                                    {{ t('adminDashboard.licenceDoc') }}
                                </span>
                                <a
                                    v-if="vendor.business_licence_url || vendor.business_licence_path"
                                    :href="vendor.business_licence_url || `/storage/${vendor.business_licence_path}`"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="inline-flex items-center gap-1 font-medium text-[#ff545a] hover:underline"
                                >
                                    <FileText class="size-3.5" />
                                    <span>{{ t('adminDashboard.viewLicence') }}</span>
                                    <ExternalLink class="size-3" />
                                </a>
                                <span v-else class="text-muted-foreground italic">Not attached</span>
                            </div>
                        </div>

                        <p v-if="vendor.description" class="text-muted-foreground text-xs italic">
                            "{{ vendor.description }}"
                        </p>
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
