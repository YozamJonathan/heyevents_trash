<script setup lang="ts">
import { computed, ref } from 'vue';
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
    AlertCircle,
    FileText,
    ExternalLink,
    RefreshCw,
    Lock,
    ShieldCheck,
    CheckCircle2,
    Sparkles,
    MessageSquare,
    ArrowRight,
    X,
} from '@lucide/vue';
import { Link } from '@inertiajs/vue3';
import { useLanguage } from '@/composables/useLanguage';

const props = defineProps<{
    providerProfile?: {
        id: number;
        brand_name: string;
        contact_name: string;
        nida_number: string;
        primary_phone?: string;
        secondary_phone?: string;
        categories?: string[];
        instagram?: string;
        whatsapp?: string;
        tiktok?: string;
        business_licence_path?: string;
        business_licence_url?: string;
        social_media?: string;
        category?: string;
        description?: string;
        status: 'pending' | 'approved' | 'rejected';
        rejection_reason?: string | null;
        admin_feedback?: string | null;
        feedback_sent_at?: string | null;
        feedback_sender?: {
            id: number;
            name: string;
            email: string;
        } | null;
        verified_at?: string | null;
        verifier?: {
            id: number;
            name: string;
            email: string;
        } | null;
    } | null;
}>();

const { t } = useLanguage();

const hasProfile = computed(() => !!props.providerProfile);
const isPending = computed(() => props.providerProfile?.status === 'pending');
const isRejected = computed(() => props.providerProfile?.status === 'rejected');
const isApproved = computed(() => props.providerProfile?.status === 'approved');
const isFeedbackDismissed = ref(false);

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

const metrics = computed(() => [
    {
        title: t('providerDashboard.earnings'),
        value: 'TZS 0',
        change: isApproved.value ? 'Active account' : 'Locked until verified',
        icon: Banknote,
    },
    {
        title: t('providerDashboard.upcomingBookings'),
        value: t('providerDashboard.events'),
        change: isApproved.value ? t('providerDashboard.scheduleClear') : 'Pending verification',
        icon: Calendar,
    },
    {
        title: t('providerDashboard.packages'),
        value: t('providerDashboard.listed'),
        change: isApproved.value ? t('providerDashboard.addPackage') : 'Catalog locked',
        icon: Briefcase,
    },
    {
        title: t('providerDashboard.rating'),
        value: isApproved.value ? '5.0 ★' : t('providerDashboard.newProvider'),
        change: isApproved.value ? 'Official Partner' : t('providerDashboard.awaitReviews'),
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
        <!-- CLEAN COMPACT ADMIN DIRECT FEEDBACK STRIP -->
        <div
            v-if="providerProfile?.admin_feedback && !isFeedbackDismissed"
            class="flex flex-col gap-2.5 rounded-xl border border-blue-500/30 bg-blue-50/70 p-3 shadow-2xs sm:flex-row sm:items-center sm:justify-between dark:border-blue-500/25 dark:bg-blue-950/25"
        >
            <div class="flex items-start gap-2.5 min-w-0">
                <div class="mt-0.5 rounded-lg bg-blue-500/15 p-1.5 text-blue-600 dark:text-blue-400 shrink-0">
                    <MessageSquare class="size-4" />
                </div>
                <div class="min-w-0">
                    <div class="flex flex-wrap items-center gap-1.5 text-xs">
                        <span class="rounded bg-blue-500/20 px-1.5 py-0.5 text-[9px] font-bold uppercase tracking-wider text-blue-800 dark:text-blue-300">
                            Action Required
                        </span>
                        <span class="font-bold text-foreground">
                            Admin Feedback:
                        </span>
                        <span class="text-zinc-800 dark:text-zinc-200">
                            "{{ providerProfile.admin_feedback }}"
                        </span>
                    </div>
                    <div v-if="providerProfile?.feedback_sent_at" class="text-[10px] text-muted-foreground mt-0.5">
                        {{ formatDate(providerProfile.feedback_sent_at) }}
                        <template v-if="providerProfile.feedback_sender?.name"> · by {{ providerProfile.feedback_sender.name }} (Admin)</template>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-1.5 self-end sm:self-center shrink-0">
                <Link
                    href="/onboarding"
                    class="inline-flex items-center gap-1 rounded-lg bg-[#ff545a] px-3 py-1.5 text-[11px] font-bold text-white shadow-2xs transition hover:bg-[#e0454b]"
                >
                    <span>Rectify Details</span>
                    <ArrowRight class="size-3" />
                </Link>
                <button
                    type="button"
                    @click="isFeedbackDismissed = true"
                    class="rounded-lg p-1 text-muted-foreground transition hover:bg-muted/80 hover:text-foreground"
                    title="Dismiss notice"
                >
                    <X class="size-3.5" />
                </button>
            </div>
        </div>

        <!-- 0. NO KYC SUBMITTED YET -->
        <div
            v-if="!hasProfile"
            class="flex flex-col gap-4 rounded-2xl border border-blue-500/30 bg-blue-500/10 p-5 shadow-sm dark:border-blue-500/20 dark:bg-blue-500/5 sm:flex-row sm:items-center sm:justify-between"
        >
            <div class="flex items-start gap-3.5">
                <div class="rounded-full bg-blue-500/20 p-2.5 text-blue-600 dark:text-blue-400">
                    <ShieldCheck class="size-6" />
                </div>
                <div>
                    <h3 class="text-foreground text-base font-bold">
                        {{ t('providerDashboard.completeKycTitle') }}
                    </h3>
                    <p class="text-muted-foreground mt-1 max-w-xl text-xs leading-relaxed">
                        {{ t('providerDashboard.completeKycDesc') }}
                    </p>
                </div>
            </div>

            <Link
                href="/onboarding"
                class="inline-flex shrink-0 items-center gap-2 rounded-xl bg-[#ff545a] px-5 py-2.5 text-xs font-bold text-white shadow-md transition hover:bg-[#e0454b]"
            >
                <Sparkles class="size-3.5" />
                <span>{{ t('providerDashboard.completeKycAction') }}</span>
            </Link>
        </div>

        <!-- 1. PENDING KYC BANNER & ACCESS PRIVILEGES PREVIEW -->
        <div
            v-else-if="isPending"
            class="space-y-4"
        >
            <!-- Primary Pending Banner -->
            <div
                class="flex flex-col gap-4 rounded-2xl border border-amber-500/40 bg-amber-500/10 p-5 shadow-sm dark:border-amber-500/30 dark:bg-amber-500/5"
            >
                <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                    <div class="flex items-start gap-3.5">
                        <div
                            class="mt-0.5 rounded-full bg-amber-500/20 p-2.5 text-amber-600 dark:text-amber-400"
                        >
                            <Clock class="size-6 animate-pulse" />
                        </div>
                        <div>
                            <div class="flex flex-wrap items-center gap-2">
                                <h3 class="text-foreground text-base font-bold">
                                    {{ t('providerDashboard.kycPendingTitle') }}
                                </h3>
                                <span
                                    class="rounded-full bg-amber-500/20 px-2.5 py-0.5 text-[11px] font-bold text-amber-800 uppercase dark:text-amber-300"
                                >
                                    {{ t('providerDashboard.pendingStatus') }}
                                </span>
                            </div>
                            <p class="text-muted-foreground mt-1 max-w-2xl text-xs leading-relaxed">
                                {{ t('providerDashboard.kycPendingDesc') }}
                            </p>
                        </div>
                    </div>

                    <Link
                        href="/onboarding"
                        class="inline-flex shrink-0 items-center gap-1.5 self-start rounded-xl border border-amber-500/40 bg-amber-500/10 px-3.5 py-2 text-xs font-semibold text-amber-800 transition hover:bg-amber-500/20 dark:text-amber-300"
                    >
                        <RefreshCw class="size-3.5" />
                        <span>{{ t('providerDashboard.updateKyc') }}</span>
                    </Link>
                </div>

                <!-- KYC Details Summary Grid -->
                <div
                    v-if="providerProfile"
                    class="border-amber-500/20 mt-2 space-y-2.5 border-t pt-3 text-xs"
                >
                    <div class="grid gap-2.5 sm:grid-cols-2 md:grid-cols-4">
                        <div class="rounded-lg bg-background/60 p-2.5 border border-border/40 min-w-0">
                            <span class="text-muted-foreground text-[10px] block uppercase font-medium">{{ t('providerDashboard.brandLabel') }}</span>
                            <span class="text-foreground font-semibold truncate block">{{ providerProfile.brand_name }}</span>
                        </div>
                        <div class="rounded-lg bg-background/60 p-2.5 border border-border/40 min-w-0">
                            <span class="text-muted-foreground text-[10px] block uppercase font-medium">{{ t('providerDashboard.contactLabel') }}</span>
                            <span class="text-foreground font-semibold truncate block">{{ providerProfile.contact_name }}</span>
                        </div>
                        <div class="rounded-lg bg-background/60 p-2.5 border border-border/40 min-w-0">
                            <span class="text-muted-foreground text-[10px] block uppercase font-medium">{{ t('providerDashboard.phoneLabel') }}</span>
                            <span class="text-foreground font-medium truncate block">
                                {{ providerProfile.primary_phone || '—' }}
                                <span v-if="providerProfile.secondary_phone" class="text-muted-foreground text-[11px]"> · {{ providerProfile.secondary_phone }}</span>
                            </span>
                        </div>
                        <div class="rounded-lg bg-background/60 p-2.5 border border-border/40 min-w-0">
                            <span class="text-muted-foreground text-[10px] block uppercase font-medium">{{ t('providerDashboard.nidaLabel') }}</span>
                            <span class="font-mono text-foreground font-medium truncate block">{{ providerProfile.nida_number }}</span>
                        </div>
                    </div>

                    <!-- Services & Social Media Row -->
                    <div class="grid gap-2.5 sm:grid-cols-2">
                        <div class="rounded-lg bg-background/60 p-2.5 border border-border/40 min-w-0">
                            <span class="text-muted-foreground text-[10px] block uppercase font-medium mb-1">{{ t('providerDashboard.servicesProvided') }}</span>
                            <div class="flex flex-wrap gap-1">
                                <template v-if="providerProfile.categories && providerProfile.categories.length > 0">
                                    <span
                                        v-for="cat in providerProfile.categories"
                                        :key="cat"
                                        class="inline-flex items-center rounded-md bg-[#ff545a]/10 px-2 py-0.5 text-[11px] font-medium text-[#ff545a]"
                                    >
                                        {{ cat }}
                                    </span>
                                </template>
                                <span
                                    v-else-if="providerProfile.category"
                                    class="inline-flex items-center rounded-md bg-[#ff545a]/10 px-2 py-0.5 text-[11px] font-medium text-[#ff545a]"
                                >
                                    {{ providerProfile.category }}
                                </span>
                                <span v-else class="text-muted-foreground text-[11px]">—</span>
                            </div>
                        </div>

                        <div class="rounded-lg bg-background/60 p-2.5 border border-border/40 min-w-0">
                            <span class="text-muted-foreground text-[10px] block uppercase font-medium mb-1">{{ t('providerDashboard.socialChannels') }}</span>
                            <div class="flex flex-wrap items-center gap-1.5 text-[11px]">
                                <span v-if="providerProfile.instagram" class="inline-flex items-center gap-1 rounded bg-purple-500/10 px-2 py-0.5 text-purple-700 dark:text-purple-300">
                                    IG: {{ providerProfile.instagram }}
                                </span>
                                <span v-if="providerProfile.whatsapp" class="inline-flex items-center gap-1 rounded bg-emerald-500/10 px-2 py-0.5 text-emerald-700 dark:text-emerald-300">
                                    WA: {{ providerProfile.whatsapp }}
                                </span>
                                <span v-if="providerProfile.tiktok" class="inline-flex items-center gap-1 rounded bg-cyan-500/10 px-2 py-0.5 text-cyan-700 dark:text-cyan-300">
                                    TT: {{ providerProfile.tiktok }}
                                </span>
                                <span v-if="!providerProfile.instagram && !providerProfile.whatsapp && !providerProfile.tiktok" class="text-muted-foreground">
                                    {{ providerProfile.social_media || 'Standard' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Access Privileges Locked Warning Box -->
            <div class="rounded-2xl border border-border/80 bg-card p-5 shadow-sm">
                <div class="flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-muted-foreground">
                    <Lock class="size-4 text-amber-500" />
                    <span>{{ t('providerDashboard.accessPrivileges') }}</span>
                </div>
                <div class="mt-3 grid gap-3 sm:grid-cols-3 text-xs">
                    <div class="rounded-xl border border-border/60 bg-muted/20 p-3">
                        <div class="flex items-center gap-1.5 font-bold text-amber-700 dark:text-amber-400">
                            <Lock class="size-3.5" />
                            <span>{{ t('providerDashboard.catalogLocked') }}</span>
                        </div>
                        <p class="mt-1 text-muted-foreground text-[11px] leading-relaxed">
                            {{ t('providerDashboard.catalogLockedDesc') }}
                        </p>
                    </div>

                    <div class="rounded-xl border border-border/60 bg-muted/20 p-3">
                        <div class="flex items-center gap-1.5 font-bold text-amber-700 dark:text-amber-400">
                            <Lock class="size-3.5" />
                            <span>{{ t('providerDashboard.bookingsLocked') }}</span>
                        </div>
                        <p class="mt-1 text-muted-foreground text-[11px] leading-relaxed">
                            {{ t('providerDashboard.bookingsLockedDesc') }}
                        </p>
                    </div>

                    <div class="rounded-xl border border-border/60 bg-muted/20 p-3">
                        <div class="flex items-center gap-1.5 font-bold text-amber-700 dark:text-amber-400">
                            <Lock class="size-3.5" />
                            <span>{{ t('providerDashboard.payoutsLocked') }}</span>
                        </div>
                        <p class="mt-1 text-muted-foreground text-[11px] leading-relaxed">
                            {{ t('providerDashboard.payoutsLockedDesc') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. REJECTED KYC BANNER -->
        <div
            v-else-if="isRejected"
            class="flex flex-col gap-4 rounded-2xl border border-rose-500/40 bg-rose-500/10 p-5 shadow-sm dark:border-rose-500/30 dark:bg-rose-500/5"
        >
            <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                <div class="flex items-start gap-3.5">
                    <div
                        class="mt-0.5 rounded-full bg-rose-500/20 p-2.5 text-rose-600 dark:text-rose-400"
                    >
                        <AlertCircle class="size-6" />
                    </div>
                    <div>
                        <div class="flex flex-wrap items-center gap-2">
                            <h3 class="text-foreground text-base font-bold">
                                {{ t('providerDashboard.kycRejectedTitle') }}
                            </h3>
                            <span
                                class="rounded-full bg-rose-500/20 px-2.5 py-0.5 text-[11px] font-bold text-rose-800 uppercase dark:text-rose-300"
                            >
                                {{ t('providerDashboard.rejectedStatus') }}
                            </span>
                        </div>
                        <p class="text-muted-foreground mt-1 max-w-2xl text-xs leading-relaxed">
                            {{ t('providerDashboard.kycRejectedDesc') }}
                        </p>
                        <div
                            v-if="providerProfile?.rejection_reason"
                            class="mt-2.5 rounded-lg border border-rose-500/30 bg-background/80 p-3 text-xs"
                        >
                            <span class="font-semibold text-rose-600 dark:text-rose-400">
                                {{ t('providerDashboard.rejectionReason') }}:
                            </span>
                            <span class="text-foreground ml-1.5">{{ providerProfile.rejection_reason }}</span>
                        </div>
                    </div>
                </div>

                <Link
                    href="/onboarding"
                    class="inline-flex shrink-0 items-center gap-2 rounded-xl bg-[#ff545a] px-4.5 py-2 text-xs font-semibold text-white shadow-sm transition hover:bg-[#e0454b]"
                >
                    <RefreshCw class="size-3.5" />
                    <span>{{ t('providerDashboard.updateKyc') }}</span>
                </Link>
            </div>
        </div>

        <!-- 3. VERIFIED / APPROVED STATUS BANNER (TRIUMPHANT OFFICIAL PARTNER) -->
        <div
            v-else-if="isApproved"
            class="relative overflow-hidden rounded-2xl border border-emerald-500/40 bg-gradient-to-r from-emerald-500/15 via-emerald-500/5 to-transparent p-5 shadow-sm dark:border-emerald-500/30"
        >
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-start sm:items-center gap-3.5">
                    <div
                        class="rounded-2xl bg-emerald-500/20 p-3 text-emerald-600 dark:text-emerald-400 shrink-0"
                    >
                        <BadgeCheck class="size-7" />
                    </div>
                    <div>
                        <div class="flex flex-wrap items-center gap-2">
                            <h3 class="text-foreground text-base font-extrabold tracking-tight">
                                {{ t('providerDashboard.verified') }}
                            </h3>
                            <span
                                class="rounded-full bg-emerald-500/25 px-2.5 py-0.5 text-[11px] font-bold text-emerald-800 uppercase dark:text-emerald-300"
                            >
                                {{ t('providerDashboard.active') }}
                            </span>
                        </div>
                        <p class="text-muted-foreground mt-1 text-xs max-w-xl leading-relaxed">
                            {{ t('providerDashboard.ready') }}
                        </p>
                        <div class="mt-2 flex flex-wrap items-center gap-x-3 text-[11px] text-emerald-700 dark:text-emerald-400 font-medium">
                            <span v-if="providerProfile?.verified_at">
                                ✓ {{ t('providerDashboard.verifiedOn') }} {{ formatDate(providerProfile.verified_at) }}
                            </span>
                            <span v-if="providerProfile?.verifier?.name">
                                · {{ t('providerDashboard.verifiedBy') }} {{ providerProfile.verifier.name }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-2 shrink-0">
                    <button
                        type="button"
                        class="inline-flex items-center gap-1.5 rounded-xl bg-[#ff545a] px-4 py-2.5 text-xs font-bold text-white shadow-md transition hover:bg-[#e0454b]"
                    >
                        <Plus class="size-3.5" />
                        <span>{{ t('providerDashboard.addService') }}</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Metrics Grid -->
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div
                v-for="metric in metrics"
                :key="metric.title"
                class="border-border/70 bg-card rounded-xl border p-5 shadow-sm transition-all"
                :class="isApproved ? 'hover:border-emerald-500/40' : 'opacity-85'"
            >
                <div class="flex items-center justify-between">
                    <span
                        class="text-muted-foreground text-xs font-medium tracking-wider uppercase"
                        >{{ metric.title }}</span
                    >
                    <div
                        class="rounded-lg p-2"
                        :class="isApproved ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400' : 'bg-muted text-muted-foreground'"
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
                            :disabled="!isApproved"
                            class="mt-4 inline-flex items-center gap-1.5 rounded-lg bg-emerald-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-emerald-700 disabled:opacity-50 disabled:cursor-not-allowed"
                            :title="!isApproved ? 'Requires admin verification' : ''"
                        >
                            <Lock v-if="!isApproved" class="size-3" />
                            <Plus v-else class="size-3" />
                            <span>{{ t('providerDashboard.createPackage') }}</span>
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
                            :disabled="!isApproved"
                            class="bg-muted text-muted-foreground rounded-lg px-3 py-2 text-xs font-medium"
                            :class="isApproved ? 'hover:bg-muted/80' : 'cursor-not-allowed opacity-60'"
                        >
                            {{ t('providerDashboard.requestPayout') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
