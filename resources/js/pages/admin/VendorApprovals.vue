<script setup lang="ts">
import { Head, router, Link } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import {
    ShieldCheck,
    CheckCircle,
    CheckCircle2,
    Clock,
    BadgeCheck,
    X,
    Check,
    Loader2,
    Phone,
    FileText,
    MessageSquare,
    RotateCcw,
    Send,
    Inbox,
    Search,
    ArrowLeft,
    ExternalLink,
} from '@lucide/vue';
import { useLanguage } from '@/composables/useLanguage';

interface VendorProfile {
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
    status: 'pending' | 'approved' | 'rejected';
    rejection_reason?: string | null;
    admin_feedback?: string | null;
    feedback_sent_at?: string | null;
    feedback_sent_by?: number | null;
    feedback_sender?: {
        id: number;
        name: string;
        email: string;
    };
    verified_at?: string | null;
    created_at?: string;
    user?: {
        id: number;
        name: string;
        email: string;
    };
    verifier?: {
        id: number;
        name: string;
        email: string;
    };
}

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Dashboard',
                href: '/dashboard',
            },
            {
                title: 'Vendor Approvals (KYC)',
                href: '/admin/vendor-approvals',
            },
        ],
    },
});

const props = defineProps<{
    pendingVendors?: VendorProfile[];
    verifiedVendors?: VendorProfile[];
    rejectedVendors?: VendorProfile[];
    stats?: {
        pending_count: number;
        verified_count: number;
        rejected_count: number;
    };
}>();

const { t } = useLanguage();

const activeTab = ref<'pending' | 'verified' | 'rejected'>('pending');
const processingId = ref<number | null>(null);
const searchQuery = ref('');

// Message / Feedback Modal state
const isMessageModalOpen = ref(false);
const selectedVendor = ref<VendorProfile | null>(null);
const messageSubject = ref('KYC Verification Mistakes / Action Required');
const messageBody = ref('');
const isSendingMessage = ref(false);

const pendingList = computed(() => {
    const list = props.pendingVendors ?? [];
    if (!searchQuery.value.trim()) return list;
    const q = searchQuery.value.toLowerCase();
    return list.filter(
        (v) =>
            v.brand_name.toLowerCase().includes(q) ||
            v.contact_name.toLowerCase().includes(q) ||
            v.user?.email.toLowerCase().includes(q) ||
            v.nida_number.toLowerCase().includes(q)
    );
});

const verifiedList = computed(() => {
    const list = props.verifiedVendors ?? [];
    if (!searchQuery.value.trim()) return list;
    const q = searchQuery.value.toLowerCase();
    return list.filter(
        (v) =>
            v.brand_name.toLowerCase().includes(q) ||
            v.contact_name.toLowerCase().includes(q) ||
            v.user?.email.toLowerCase().includes(q)
    );
});

const rejectedList = computed(() => {
    const list = props.rejectedVendors ?? [];
    if (!searchQuery.value.trim()) return list;
    const q = searchQuery.value.toLowerCase();
    return list.filter(
        (v) =>
            v.brand_name.toLowerCase().includes(q) ||
            v.contact_name.toLowerCase().includes(q) ||
            v.user?.email.toLowerCase().includes(q)
    );
});

function approveVendor(vendor: VendorProfile) {
    if (!confirm(t('adminDashboard.confirmApprove') || `Are you sure you want to approve and verify "${vendor.brand_name}"?`)) return;

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

function rejectVendor(vendor: VendorProfile) {
    const reason = prompt(
        t('adminDashboard.rejectPrompt') || 'Reason for rejection (e.g. invalid document or NIDA details):',
        'Business licence document or credentials could not be verified.',
    );
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

function revokeVendor(vendor: VendorProfile) {
    if (!confirm(t('adminDashboard.confirmRevoke') || `Are you sure you want to revoke verification for "${vendor.brand_name}" and return to pending?`)) return;

    processingId.value = vendor.id;
    router.post(
        `/admin/vendors/${vendor.id}/revoke`,
        {},
        {
            preserveScroll: true,
            onFinish: () => {
                processingId.value = null;
            },
        },
    );
}

function openMessageModal(vendor: VendorProfile) {
    selectedVendor.value = vendor;
    messageSubject.value = vendor.status === 'rejected'
        ? 'KYC Verification Mistakes / Action Required'
        : 'Important: Regarding Your Service Provider Application';
    messageBody.value = vendor.admin_feedback || vendor.rejection_reason || '';
    isMessageModalOpen.value = true;
}

function closeMessageModal() {
    isMessageModalOpen.value = false;
    selectedVendor.value = null;
    messageBody.value = '';
}

function sendMessage() {
    if (!selectedVendor.value || !messageBody.value.trim()) return;
    isSendingMessage.value = true;
    router.post(
        `/admin/vendors/${selectedVendor.value.id}/message`,
        {
            subject: messageSubject.value,
            message: messageBody.value.trim(),
        },
        {
            preserveScroll: true,
            onFinish: () => {
                isSendingMessage.value = false;
                closeMessageModal();
            },
        },
    );
}

const formatDate = (dateString?: string | null) => {
    if (!dateString) return '';
    try {
        return new Date(dateString).toLocaleDateString(undefined, {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
        });
    } catch {
        return dateString;
    }
};
</script>

<template>
    <Head title="Vendor Approvals (KYC)" />

    <div class="flex h-full flex-1 flex-col gap-6 p-4 md:p-6">
        <!-- Page Header with Return Button -->
        <div class="flex flex-col gap-3 border-b border-border/60 pb-5 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <div class="flex items-center gap-3">
                    <Link
                        href="/dashboard"
                        class="rounded-xl border border-border/80 bg-background p-2 text-muted-foreground transition hover:bg-muted hover:text-foreground"
                        title="Back to Dashboard"
                    >
                        <ArrowLeft class="size-4" />
                    </Link>
                    <div>
                        <h1 class="text-xl font-bold tracking-tight text-foreground sm:text-2xl">
                            {{ t('adminDashboard.verifications') }}
                        </h1>
                        <p class="text-xs text-muted-foreground mt-0.5">
                            {{ t('adminDashboard.verificationDescription') }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Search box -->
            <div class="relative w-full max-w-xs">
                <Search class="absolute left-3 top-1/2 size-3.5 -translate-y-1/2 text-muted-foreground" />
                <input
                    v-model="searchQuery"
                    type="text"
                    placeholder="Search brand, name, or NIDA..."
                    class="w-full rounded-xl border border-border bg-muted/30 py-1.5 pl-8 pr-3 text-xs text-foreground placeholder:text-muted-foreground focus:border-[#ff545a] focus:outline-hidden"
                />
            </div>
        </div>

        <!-- 3 Top Metric Count Cards -->
        <div class="grid gap-4 sm:grid-cols-3">
            <!-- Pending Box -->
            <button
                type="button"
                @click="activeTab = 'pending'"
                class="rounded-2xl border p-4 text-left transition-all shadow-xs"
                :class="[
                    activeTab === 'pending'
                        ? 'border-amber-500/50 bg-amber-500/10 ring-2 ring-amber-500/20'
                        : 'border-border/70 bg-card hover:border-amber-500/30'
                ]"
            >
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-muted-foreground uppercase tracking-wider">
                        {{ t('adminDashboard.tabPending') }}
                    </span>
                    <div class="rounded-xl bg-amber-500/15 p-2 text-amber-700 dark:text-amber-400">
                        <Clock class="size-4.5" />
                    </div>
                </div>
                <div class="mt-2 flex items-baseline gap-2">
                    <span class="text-3xl font-black text-foreground">
                        {{ props.pendingVendors?.length ?? 0 }}
                    </span>
                    <span class="text-xs font-semibold text-amber-700 dark:text-amber-400">
                        Awaiting Verification
                    </span>
                </div>
            </button>

            <!-- Verified Box -->
            <button
                type="button"
                @click="activeTab = 'verified'"
                class="rounded-2xl border p-4 text-left transition-all shadow-xs"
                :class="[
                    activeTab === 'verified'
                        ? 'border-emerald-500/50 bg-emerald-500/10 ring-2 ring-emerald-500/20'
                        : 'border-border/70 bg-card hover:border-emerald-500/30'
                ]"
            >
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-muted-foreground uppercase tracking-wider">
                        {{ t('adminDashboard.tabVerified') }}
                    </span>
                    <div class="rounded-xl bg-emerald-500/15 p-2 text-emerald-700 dark:text-emerald-400">
                        <BadgeCheck class="size-4.5" />
                    </div>
                </div>
                <div class="mt-2 flex items-baseline gap-2">
                    <span class="text-3xl font-black text-foreground">
                        {{ props.verifiedVendors?.length ?? 0 }}
                    </span>
                    <span class="text-xs font-semibold text-emerald-700 dark:text-emerald-400">
                        Active Partners
                    </span>
                </div>
            </button>

            <!-- Rejected Box -->
            <button
                type="button"
                @click="activeTab = 'rejected'"
                class="rounded-2xl border p-4 text-left transition-all shadow-xs"
                :class="[
                    activeTab === 'rejected'
                        ? 'border-rose-500/50 bg-rose-500/10 ring-2 ring-rose-500/20'
                        : 'border-border/70 bg-card hover:border-rose-500/30'
                ]"
            >
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-muted-foreground uppercase tracking-wider">
                        {{ t('adminDashboard.tabRejected') }}
                    </span>
                    <div class="rounded-xl bg-rose-500/15 p-2 text-rose-700 dark:text-rose-400">
                        <X class="size-4.5" />
                    </div>
                </div>
                <div class="mt-2 flex items-baseline gap-2">
                    <span class="text-3xl font-black text-foreground">
                        {{ props.rejectedVendors?.length ?? 0 }}
                    </span>
                    <span class="text-xs font-semibold text-rose-700 dark:text-rose-400">
                        Needs Attention
                    </span>
                </div>
            </button>
        </div>

        <!-- Main Card Container -->
        <div class="rounded-2xl border border-border/80 bg-card p-6 shadow-sm">
            <!-- Tabs Bar -->
            <div class="flex items-center justify-between border-b border-border/60 pb-4">
                <div class="flex items-center gap-2">
                    <button
                        type="button"
                        @click="activeTab = 'pending'"
                        :class="[
                            'inline-flex items-center gap-1.5 rounded-xl px-3 py-1.5 text-xs font-bold transition-all',
                            activeTab === 'pending'
                                ? 'bg-amber-500/15 text-amber-800 dark:text-amber-300 shadow-2xs'
                                : 'text-muted-foreground hover:text-foreground',
                        ]"
                    >
                        <Clock class="size-3.5" />
                        <span>{{ t('adminDashboard.tabPending') }} ({{ pendingList.length }})</span>
                    </button>
                    <button
                        type="button"
                        @click="activeTab = 'verified'"
                        :class="[
                            'inline-flex items-center gap-1.5 rounded-xl px-3 py-1.5 text-xs font-bold transition-all',
                            activeTab === 'verified'
                                ? 'bg-emerald-500/15 text-emerald-800 dark:text-emerald-300 shadow-2xs'
                                : 'text-muted-foreground hover:text-foreground',
                        ]"
                    >
                        <BadgeCheck class="size-3.5" />
                        <span>{{ t('adminDashboard.tabVerified') }} ({{ verifiedList.length }})</span>
                    </button>
                    <button
                        type="button"
                        @click="activeTab = 'rejected'"
                        :class="[
                            'inline-flex items-center gap-1.5 rounded-xl px-3 py-1.5 text-xs font-bold transition-all',
                            activeTab === 'rejected'
                                ? 'bg-rose-500/15 text-rose-800 dark:text-rose-300 shadow-2xs'
                                : 'text-muted-foreground hover:text-foreground',
                        ]"
                    >
                        <X class="size-3.5" />
                        <span>{{ t('adminDashboard.tabRejected') }} ({{ rejectedList.length }})</span>
                    </button>
                </div>
            </div>

            <!-- TAB 1: PENDING VENDORS -->
            <div v-if="activeTab === 'pending'">
                <div
                    v-if="pendingList.length === 0"
                    class="flex flex-col items-center justify-center py-20 text-center"
                >
                    <div class="mb-3 rounded-full bg-emerald-500/10 p-4 text-emerald-600 dark:text-emerald-400">
                        <CheckCircle class="size-8" />
                    </div>
                    <h3 class="text-foreground text-base font-semibold">
                        {{ t('adminDashboard.reviewed') }}
                    </h3>
                    <p class="text-muted-foreground mt-1 max-w-md text-xs">
                        {{ t('adminDashboard.noPending') }}
                    </p>
                </div>

                <div v-else class="divide-border/40 mt-4 divide-y">
                    <div
                        v-for="vendor in pendingList"
                        :key="vendor.id"
                        class="flex flex-col gap-4 py-5 first:pt-2"
                    >
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                            <div class="space-y-1.5">
                                <div class="flex flex-wrap items-center gap-2">
                                    <h3 class="text-foreground text-base font-bold">
                                        {{ vendor.brand_name }}
                                    </h3>
                                    <span class="rounded-full bg-amber-500/20 px-2.5 py-0.5 text-[10px] font-bold text-amber-800 dark:text-amber-300 uppercase">
                                        Awaiting Review
                                    </span>
                                    <div class="flex flex-wrap items-center gap-1">
                                        <span
                                            v-for="cat in (vendor.categories && vendor.categories.length > 0 ? vendor.categories : [vendor.category || 'Service Provider'])"
                                            :key="cat"
                                            class="bg-blue-500/10 text-blue-700 dark:text-blue-300 rounded-md px-2 py-0.5 text-[10px] font-medium"
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
                            <div class="flex flex-wrap items-center gap-2 self-start sm:self-auto shrink-0">
                                <button
                                    type="button"
                                    :disabled="processingId === vendor.id"
                                    @click="approveVendor(vendor)"
                                    class="inline-flex items-center gap-1.5 rounded-xl bg-emerald-600 px-4 py-2 text-xs font-bold text-white shadow-sm transition hover:bg-emerald-700 disabled:opacity-50"
                                >
                                    <Loader2 v-if="processingId === vendor.id" class="size-3.5 animate-spin" />
                                    <Check v-else class="size-3.5" />
                                    {{ t('adminDashboard.approve') }}
                                </button>
                                <button
                                    type="button"
                                    :disabled="processingId === vendor.id"
                                    @click="rejectVendor(vendor)"
                                    class="inline-flex items-center gap-1.5 rounded-xl border border-rose-500/40 bg-rose-500/10 px-4 py-2 text-xs font-semibold text-rose-700 transition hover:bg-rose-500/20 disabled:opacity-50 dark:text-rose-300"
                                >
                                    <X class="size-3.5" />
                                    {{ t('adminDashboard.reject') }}
                                </button>
                                <button
                                    type="button"
                                    @click="openMessageModal(vendor)"
                                    class="inline-flex items-center gap-1.5 rounded-xl border border-blue-500/40 bg-blue-500/10 px-4 py-2 text-xs font-semibold text-blue-700 transition hover:bg-blue-500/20 dark:text-blue-300"
                                    title="Send message or flag mistakes"
                                >
                                    <MessageSquare class="size-3.5" />
                                    <span>Message / Mistakes</span>
                                </button>
                            </div>
                        </div>

                        <!-- Submitted KYC verification attributes -->
                        <div class="grid gap-3 rounded-xl border border-border/60 bg-muted/20 p-3.5 sm:grid-cols-4 text-xs">
                            <div>
                                <span class="text-muted-foreground text-[10px] uppercase font-medium block">
                                    {{ t('adminDashboard.nida') }}
                                </span>
                                <span class="font-mono text-foreground font-semibold">{{ vendor.nida_number }}</span>
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
                                <div class="space-y-0.5 text-foreground">
                                    <div v-if="vendor.instagram">IG: @{{ vendor.instagram }}</div>
                                    <div v-if="vendor.whatsapp">WA: {{ vendor.whatsapp }}</div>
                                    <div v-if="vendor.tiktok">TikTok: @{{ vendor.tiktok }}</div>
                                    <div v-if="!vendor.instagram && !vendor.whatsapp && !vendor.tiktok">
                                        {{ vendor.social_media || 'Standard' }}
                                    </div>
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
                                    class="inline-flex items-center gap-1.5 font-bold text-[#ff545a] hover:underline mt-0.5"
                                >
                                    <FileText class="size-4" />
                                    <span>{{ t('adminDashboard.viewLicence') }}</span>
                                    <ExternalLink class="size-3" />
                                </a>
                                <span v-else class="text-muted-foreground italic">None uploaded</span>
                            </div>
                        </div>

                        <!-- Previous Admin Message / Feedback -->
                        <div
                            v-if="vendor.admin_feedback"
                            class="rounded-xl border border-blue-500/30 bg-blue-50/50 p-3 text-xs dark:bg-blue-950/20"
                        >
                            <div class="flex items-center gap-1.5 font-semibold text-blue-700 dark:text-blue-400">
                                <MessageSquare class="size-3.5" />
                                <span>Last Admin Message:</span>
                                <span v-if="vendor.feedback_sent_at" class="text-[10px] font-normal text-muted-foreground">
                                    ({{ formatDate(vendor.feedback_sent_at) }}<template v-if="vendor.feedback_sender?.name"> by {{ vendor.feedback_sender.name }}</template>)
                                </span>
                            </div>
                            <p class="mt-1 text-zinc-700 dark:text-zinc-300 italic">"{{ vendor.admin_feedback }}"</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB 2: VERIFIED VENDORS -->
            <div v-else-if="activeTab === 'verified'">
                <div
                    v-if="verifiedList.length === 0"
                    class="flex flex-col items-center justify-center py-20 text-center"
                >
                    <div class="mb-3 rounded-full bg-muted/60 p-4 text-muted-foreground">
                        <ShieldCheck class="size-8" />
                    </div>
                    <h3 class="text-foreground text-base font-semibold">
                        {{ t('adminDashboard.noVerified') }}
                    </h3>
                </div>

                <div v-else class="divide-border/40 mt-4 divide-y">
                    <div
                        v-for="vendor in verifiedList"
                        :key="vendor.id"
                        class="flex flex-col gap-4 py-5 first:pt-2"
                    >
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                            <div class="space-y-1.5">
                                <div class="flex flex-wrap items-center gap-2">
                                    <h3 class="text-foreground text-base font-bold">
                                        {{ vendor.brand_name }}
                                    </h3>
                                    <span class="inline-flex items-center gap-1 rounded-full bg-emerald-500/15 px-2.5 py-0.5 text-[11px] font-bold text-emerald-800 dark:text-emerald-300">
                                        <BadgeCheck class="size-3.5 text-emerald-600 dark:text-emerald-400" />
                                        <span>Verified Partner</span>
                                    </span>
                                    <div class="flex flex-wrap items-center gap-1">
                                        <span
                                            v-for="cat in (vendor.categories && vendor.categories.length > 0 ? vendor.categories : [vendor.category || 'Service Provider'])"
                                            :key="cat"
                                            class="bg-blue-500/10 text-blue-700 dark:text-blue-300 rounded-md px-2 py-0.5 text-[10px] font-medium"
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
                                    <span v-if="vendor.primary_phone" class="font-mono text-foreground">
                                        Phone: {{ vendor.primary_phone }}
                                    </span>
                                    <span v-if="vendor.verified_at" class="text-emerald-700 dark:text-emerald-400 font-medium">
                                        · {{ t('adminDashboard.verifiedOn') }} {{ formatDate(vendor.verified_at) }}
                                        <template v-if="vendor.verifier?.name">by {{ vendor.verifier.name }}</template>
                                    </span>
                                </div>
                            </div>

                            <div class="flex flex-wrap items-center gap-2 self-start sm:self-auto shrink-0">
                                <button
                                    type="button"
                                    @click="openMessageModal(vendor)"
                                    class="inline-flex items-center gap-1.5 rounded-xl border border-blue-500/40 bg-blue-500/10 px-4 py-2 text-xs font-semibold text-blue-700 transition hover:bg-blue-500/20 dark:text-blue-300"
                                    title="Send message to verified vendor"
                                >
                                    <MessageSquare class="size-3.5" />
                                    <span>Message</span>
                                </button>
                                <button
                                    type="button"
                                    :disabled="processingId === vendor.id"
                                    @click="revokeVendor(vendor)"
                                    class="inline-flex items-center gap-1.5 rounded-xl border border-amber-500/40 bg-amber-500/10 px-4 py-2 text-xs font-semibold text-amber-800 transition hover:bg-amber-500/20 disabled:opacity-50 dark:text-amber-300"
                                    :title="t('adminDashboard.revoke')"
                                >
                                    <Loader2 v-if="processingId === vendor.id" class="size-3.5 animate-spin" />
                                    <RotateCcw v-else class="size-3.5" />
                                    <span>{{ t('adminDashboard.revoke') }}</span>
                                </button>
                            </div>
                        </div>

                        <!-- Details pill -->
                        <div class="grid gap-3 rounded-xl border border-border/60 bg-muted/20 p-3 sm:grid-cols-3 text-xs">
                            <div>
                                <span class="text-muted-foreground text-[10px] uppercase font-medium block">NIDA</span>
                                <span class="font-mono text-foreground">{{ vendor.nida_number }}</span>
                            </div>
                            <div>
                                <span class="text-muted-foreground text-[10px] uppercase font-medium block">Licence</span>
                                <a
                                    v-if="vendor.business_licence_url || vendor.business_licence_path"
                                    :href="vendor.business_licence_url || `/storage/${vendor.business_licence_path}`"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="inline-flex items-center gap-1 text-[#ff545a] hover:underline"
                                >
                                    <FileText class="size-3.5" />
                                    <span>{{ t('adminDashboard.viewLicence') }}</span>
                                </a>
                                <span v-else class="text-muted-foreground italic">None</span>
                            </div>
                            <div>
                                <span class="text-muted-foreground text-[10px] uppercase font-medium block">Contact</span>
                                <span class="text-foreground">{{ vendor.instagram ? `IG: @${vendor.instagram}` : (vendor.whatsapp ? `WA: ${vendor.whatsapp}` : 'Standard') }}</span>
                            </div>
                        </div>

                        <div
                            v-if="vendor.admin_feedback"
                            class="rounded-xl border border-blue-500/30 bg-blue-50/50 p-3 text-xs dark:bg-blue-950/20"
                        >
                            <div class="flex items-center gap-1.5 font-semibold text-blue-700 dark:text-blue-400">
                                <MessageSquare class="size-3.5" />
                                <span>Last Admin Message:</span>
                                <span v-if="vendor.feedback_sent_at" class="text-[10px] font-normal text-muted-foreground">
                                    ({{ formatDate(vendor.feedback_sent_at) }})
                                </span>
                            </div>
                            <p class="mt-1 text-zinc-700 dark:text-zinc-300 italic">"{{ vendor.admin_feedback }}"</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB 3: REJECTED APPLICATIONS -->
            <div v-else-if="activeTab === 'rejected'">
                <div
                    v-if="rejectedList.length === 0"
                    class="flex flex-col items-center justify-center py-20 text-center"
                >
                    <div class="mb-3 rounded-full bg-muted/60 p-4 text-muted-foreground">
                        <Inbox class="size-8" />
                    </div>
                    <h3 class="text-foreground text-base font-semibold">
                        {{ t('adminDashboard.noRejected') }}
                    </h3>
                </div>

                <div v-else class="divide-border/40 mt-4 divide-y">
                    <div
                        v-for="vendor in rejectedList"
                        :key="vendor.id"
                        class="flex flex-col gap-4 py-5 first:pt-2"
                    >
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                            <div class="space-y-1.5">
                                <div class="flex flex-wrap items-center gap-2">
                                    <h3 class="text-foreground text-base font-bold">
                                        {{ vendor.brand_name }}
                                    </h3>
                                    <span class="rounded-full bg-rose-500/20 px-2.5 py-0.5 text-[10px] font-bold text-rose-800 dark:text-rose-300 uppercase">
                                        Rejected
                                    </span>
                                </div>
                                <p class="text-xs text-muted-foreground">
                                    {{ vendor.contact_name }} ({{ vendor.user?.email }})
                                </p>
                                <p v-if="vendor.rejection_reason" class="text-xs font-medium text-rose-600 dark:text-rose-400 mt-1">
                                    Reason: {{ vendor.rejection_reason }}
                                </p>
                            </div>

                            <div class="flex flex-wrap items-center gap-2 self-start sm:self-auto shrink-0">
                                <button
                                    type="button"
                                    @click="openMessageModal(vendor)"
                                    class="inline-flex items-center gap-1.5 rounded-xl border border-blue-500/40 bg-blue-500/10 px-4 py-2 text-xs font-semibold text-blue-700 transition hover:bg-blue-500/20 dark:text-blue-300"
                                >
                                    <MessageSquare class="size-3.5" />
                                    <span>Message / Flag Mistakes</span>
                                </button>
                                <button
                                    type="button"
                                    :disabled="processingId === vendor.id"
                                    @click="approveVendor(vendor)"
                                    class="inline-flex items-center gap-1.5 rounded-xl bg-emerald-600 px-4 py-2 text-xs font-bold text-white shadow-sm transition hover:bg-emerald-700 disabled:opacity-50"
                                >
                                    <Check class="size-3.5" />
                                    <span>{{ t('adminDashboard.reverify') }}</span>
                                </button>
                            </div>
                        </div>

                        <div
                            v-if="vendor.admin_feedback"
                            class="rounded-xl border border-blue-500/30 bg-blue-50/50 p-3 text-xs dark:bg-blue-950/20"
                        >
                            <div class="flex items-center gap-1.5 font-semibold text-blue-700 dark:text-blue-400">
                                <MessageSquare class="size-3.5" />
                                <span>Last Admin Message:</span>
                                <span v-if="vendor.feedback_sent_at" class="text-[10px] font-normal text-muted-foreground">
                                    ({{ formatDate(vendor.feedback_sent_at) }})
                                </span>
                            </div>
                            <p class="mt-1 text-zinc-700 dark:text-zinc-300 italic">"{{ vendor.admin_feedback }}"</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Admin Feedback / Mistake Flagging Modal -->
        <transition
            enter-active-class="transition duration-150 ease-out"
            enter-from-class="opacity-0 scale-95"
            enter-to-class="opacity-100 scale-100"
            leave-active-class="transition duration-100 ease-in"
            leave-from-class="opacity-100 scale-100"
            leave-to-class="opacity-0 scale-95"
        >
            <div
                v-if="isMessageModalOpen && selectedVendor"
                class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs"
                @click.self="closeMessageModal"
            >
                <div class="w-full max-w-lg rounded-3xl border border-border/80 bg-background p-6 shadow-2xl dark:bg-zinc-900">
                    <div class="flex items-center justify-between border-b border-border/60 pb-3">
                        <div class="flex items-center gap-2.5">
                            <div class="rounded-xl bg-blue-500/15 p-2 text-blue-600 dark:text-blue-400">
                                <MessageSquare class="size-4.5" />
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-foreground">
                                    Send Feedback / Flag Mistakes
                                </h3>
                                <p class="text-[11px] text-muted-foreground">
                                    Vendor: <strong class="text-foreground">{{ selectedVendor.brand_name }}</strong> ({{ selectedVendor.contact_name }})
                                </p>
                            </div>
                        </div>
                        <button
                            type="button"
                            @click="closeMessageModal"
                            class="rounded-lg p-1 text-muted-foreground hover:bg-muted"
                        >
                            <X class="size-4" />
                        </button>
                    </div>

                    <div class="mt-4 space-y-3.5">
                        <div>
                            <label class="block text-xs font-semibold text-foreground mb-1">
                                Subject / Notification Title
                            </label>
                            <input
                                v-model="messageSubject"
                                type="text"
                                class="w-full rounded-xl border border-border bg-muted/30 px-3 py-2 text-xs font-medium text-foreground focus:border-[#ff545a] focus:outline-hidden"
                                placeholder="e.g. KYC Verification Mistakes / Action Required"
                            />
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-foreground mb-1">
                                Message / Mistakes or Reasons to Rectify
                            </label>
                            <textarea
                                v-model="messageBody"
                                rows="5"
                                class="w-full rounded-xl border border-border bg-muted/30 p-3 text-xs text-foreground focus:border-[#ff545a] focus:outline-hidden"
                                placeholder="Explain the reasons, errors, or mistakes clearly (e.g. NIDA number does not match your registered name, licence is expired or unreadable, etc.). The vendor will see this in their notification bell and vendor workspace."
                            ></textarea>
                        </div>
                    </div>

                    <div class="mt-5 flex items-center justify-end gap-2 border-t border-border/60 pt-4">
                        <button
                            type="button"
                            @click="closeMessageModal"
                            class="rounded-xl border border-border px-4 py-2 text-xs font-semibold text-muted-foreground hover:bg-muted"
                        >
                            Cancel
                        </button>
                        <button
                            type="button"
                            :disabled="isSendingMessage || !messageBody.trim()"
                            @click="sendMessage"
                            class="inline-flex items-center gap-1.5 rounded-xl bg-[#ff545a] px-4.5 py-2 text-xs font-bold text-white shadow-md hover:bg-[#e0454b] disabled:opacity-50"
                        >
                            <Loader2 v-if="isSendingMessage" class="size-3.5 animate-spin" />
                            <Send v-else class="size-3.5" />
                            <span>Send Message</span>
                        </button>
                    </div>
                </div>
            </div>
        </transition>
    </div>
</template>
