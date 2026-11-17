<script setup lang="ts">
import { computed, ref } from 'vue';
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
    Truck,
    PackageCheck,
    Clock,
    MapPin,
    Phone,
    Send,
    DollarSign,
    UserCheck,
    AlertCircle,
    X,
} from '@lucide/vue';
import { Link, useForm, router } from '@inertiajs/vue3';
import { toast } from 'vue-sonner';
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
        bookings_count?: number;
    };
    allBookings?: any[];
    pendingVendors?: VendorProfile[];
    verifiedVendors?: VendorProfile[];
    rejectedVendors?: VendorProfile[];
}>();

const { t } = useLanguage();

const pendingList = computed(() => props.pendingVendors ?? []);
const verifiedList = computed(() => props.verifiedVendors ?? []);
const bookingsList = computed(() => props.allBookings ?? []);

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
        title: 'Event Orders & Fulfillment',
        value: `${bookingsList.value.length} Bookings`,
        change: `${bookingsList.value.filter(b => b.dispatch_status === 'pending_dispatch').length} to dispatch →`,
        icon: Truck,
        alert: bookingsList.value.filter(b => b.dispatch_status === 'pending_dispatch').length > 0,
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
]);

// Dispatch Modal state
const isDispatchModalOpen = ref(false);
const selectedBooking = ref<any>(null);

const dispatchForm = useForm({
    provider_id: '' as string | number,
    provider_payout_amount: '' as string | number,
});

const openDispatchModal = (booking: any) => {
    selectedBooking.value = booking;
    dispatchForm.provider_id = booking.assigned_provider_id || (verifiedList.value[0]?.id ?? '');
    dispatchForm.provider_payout_amount = booking.provider_payout_amount || Math.round(Number(booking.total_client_amount || booking.budget || 0) * 0.8);
    isDispatchModalOpen.value = true;
};

const submitDispatch = () => {
    if (!selectedBooking.value) return;
    dispatchForm.post(`/admin/bookings/${selectedBooking.value.id}/dispatch`, {
        preserveScroll: true,
        onSuccess: () => {
            isDispatchModalOpen.value = false;
            selectedBooking.value = null;
            toast.success('Gig successfully dispatched to provider.');
        },
    });
};

const updateBookingStatus = (bookingId: number, dispatchStatus: string, payoutStatus?: string) => {
    router.post(`/admin/bookings/${bookingId}/status`, {
        dispatch_status: dispatchStatus,
        payout_status: payoutStatus,
    }, {
        preserveScroll: true,
        onSuccess: () => {
            toast.success('Status updated successfully.');
        },
    });
};

const formatCurrency = (val: any) => {
    return 'TZS ' + Number(val || 0).toLocaleString();
};

const flaggedEvents = computed<
    Array<{ title: string; reason: string; date: string }>
>(() => []);
</script>

<template>
    <div class="space-y-6">
        <!-- Moderation & Operations Metrics Bar -->
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

        <!-- HEY EVENTS MANAGED DISPATCH & FULFILLMENT DESK -->
        <div class="rounded-2xl border border-border/70 bg-card p-6 shadow-sm space-y-5">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border-b border-border/50 pb-4">
                <div>
                    <div class="flex items-center gap-2">
                        <span class="size-2 rounded-full bg-[#ff545a] animate-pulse"></span>
                        <h2 class="text-foreground text-base sm:text-lg font-bold">
                            Event Orders & Managed Dispatch Desk
                        </h2>
                    </div>
                    <p class="text-muted-foreground text-xs mt-0.5">
                        Assign client equipment & service requests to verified providers and monitor setup fulfillment.
                    </p>
                </div>
                <div class="flex items-center gap-2">
                    <span class="rounded-full bg-primary/10 px-3 py-1 text-xs font-semibold text-primary">
                        {{ bookingsList.length }} Total Orders
                    </span>
                </div>
            </div>

            <!-- Empty state when no orders -->
            <div
                v-if="bookingsList.length === 0"
                class="flex flex-col items-center justify-center py-12 text-center"
            >
                <div class="rounded-full bg-muted p-3 text-muted-foreground mb-2">
                    <Truck class="size-6" />
                </div>
                <h4 class="text-xs font-semibold text-foreground">No Event Bookings Yet</h4>
                <p class="text-[11px] text-muted-foreground max-w-sm mt-0.5">
                    When clients book equipment, sound, or catering through the catalog, requests will appear here for HEY Events dispatch.
                </p>
            </div>

            <!-- Orders Table / List -->
            <div v-else class="space-y-4">
                <div
                    v-for="booking in bookingsList"
                    :key="booking.id"
                    class="rounded-xl border border-border/60 bg-muted/20 p-4 sm:p-5 transition hover:border-border/90 space-y-4"
                >
                    <!-- Row 1: Header (Client & Event Info + Status) -->
                    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3 border-b border-border/40 pb-3">
                        <div>
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="rounded bg-primary/10 px-2 py-0.5 text-[10px] font-bold text-primary uppercase">
                                    {{ booking.event_type }}
                                </span>
                                <h3 class="text-sm font-bold text-foreground">
                                    {{ booking.name }}
                                </h3>
                            </div>
                            <div class="mt-1 flex flex-wrap items-center gap-3 text-xs text-muted-foreground">
                                <span>Client: <strong class="text-foreground">{{ booking.user?.name || 'Client' }}</strong></span>
                                <span>•</span>
                                <span v-if="booking.client_phone" class="inline-flex items-center gap-1 font-semibold text-[#ff545a]">
                                    <Phone class="size-3" /> {{ booking.client_phone }}
                                </span>
                                <span>•</span>
                                <span class="inline-flex items-center gap-1">
                                    <Calendar class="size-3 text-primary" />
                                    {{ booking.event_date ? new Date(booking.event_date).toLocaleDateString() : 'Date TBD' }}
                                </span>
                                <span>•</span>
                                <span class="inline-flex items-center gap-1">
                                    <MapPin class="size-3 text-primary" />
                                    {{ booking.venue_name ? `${booking.venue_name}, ${booking.location || ''}` : (booking.location || 'Location TBD') }}
                                </span>
                            </div>
                        </div>

                        <!-- Status Pill -->
                        <div class="flex items-center gap-2 self-start">
                            <span
                                class="inline-flex items-center gap-1 rounded-full px-2.5 py-1 text-xs font-bold"
                                :class="{
                                    'bg-amber-500/15 text-amber-700 dark:text-amber-400': booking.dispatch_status === 'pending_dispatch',
                                    'bg-blue-500/15 text-blue-700 dark:text-blue-400': booking.dispatch_status === 'assigned',
                                    'bg-purple-500/15 text-purple-700 dark:text-purple-400': booking.dispatch_status === 'in_transit',
                                    'bg-teal-500/15 text-teal-700 dark:text-teal-400': booking.dispatch_status === 'delivered_and_setup',
                                    'bg-emerald-500/15 text-emerald-700 dark:text-emerald-400': booking.dispatch_status === 'completed',
                                }"
                            >
                                <Clock class="size-3" v-if="booking.dispatch_status === 'pending_dispatch'" />
                                <Truck class="size-3" v-else-if="booking.dispatch_status === 'assigned' || booking.dispatch_status === 'in_transit'" />
                                <CheckCircle2 class="size-3" v-else />
                                <span>
                                    {{
                                        booking.dispatch_status === 'pending_dispatch'
                                            ? 'Awaiting Dispatch'
                                            : booking.dispatch_status === 'assigned'
                                            ? 'Assigned to Provider'
                                            : booking.dispatch_status === 'in_transit'
                                            ? 'In Transit'
                                            : booking.dispatch_status === 'delivered_and_setup'
                                            ? 'Delivered & Set Up'
                                            : 'Completed'
                                    }}
                                </span>
                            </span>
                        </div>
                    </div>

                    <!-- Row 2: Equipment / Item Details & Financials -->
                    <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3 text-xs">
                        <!-- Requested Item / Services -->
                        <div class="rounded-lg bg-card/60 p-3 border border-border/40">
                            <span class="text-[10px] font-bold text-muted-foreground uppercase block mb-1">Requested Gear / Package</span>
                            <div v-if="booking.catalog_item" class="flex items-center gap-2">
                                <img
                                    :src="booking.catalog_item.image_url || 'https://images.unsplash.com/photo-1519741497674-611481863552?auto=format&fit=crop&w=800&q=80'"
                                    class="size-10 rounded-md object-cover"
                                />
                                <div>
                                    <h4 class="font-bold text-foreground text-xs leading-tight">{{ booking.catalog_item.title }}</h4>
                                    <span class="text-muted-foreground text-[11px]">Qty: {{ booking.item_quantity || 1 }} unit(s)</span>
                                </div>
                            </div>
                            <div v-else class="text-foreground font-medium">
                                {{ booking.services?.join(', ') || 'Custom Event Package' }}
                            </div>
                        </div>

                        <!-- Assigned Provider -->
                        <div class="rounded-lg bg-card/60 p-3 border border-border/40">
                            <span class="text-[10px] font-bold text-muted-foreground uppercase block mb-1">Assigned Service Provider</span>
                            <div v-if="booking.assigned_provider" class="flex items-center gap-1.5">
                                <BadgeCheck class="size-4 text-emerald-500 shrink-0" />
                                <div>
                                    <h4 class="font-bold text-foreground text-xs">{{ booking.assigned_provider.brand_name }}</h4>
                                    <span class="text-muted-foreground text-[11px]">{{ booking.assigned_provider.primary_phone || 'Verified' }}</span>
                                </div>
                            </div>
                            <div v-else class="flex items-center gap-1.5 text-amber-600 dark:text-amber-400">
                                <AlertCircle class="size-4 shrink-0" />
                                <span class="font-semibold text-xs">Not Dispatched Yet</span>
                            </div>
                        </div>

                        <!-- Financials / Managed Escrow -->
                        <div class="rounded-lg bg-card/60 p-3 border border-border/40 sm:col-span-2 lg:col-span-1 flex items-center justify-between">
                            <div>
                                <span class="text-[10px] font-bold text-muted-foreground uppercase block">Client Order Value</span>
                                <span class="text-sm font-extrabold text-foreground">
                                    {{ formatCurrency(booking.total_client_amount || booking.budget) }}
                                </span>
                            </div>
                            <div class="text-right">
                                <span class="text-[10px] font-bold text-muted-foreground uppercase block">Provider Payout</span>
                                <span class="text-sm font-extrabold text-emerald-600 dark:text-emerald-400">
                                    {{ formatCurrency(booking.provider_payout_amount) }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Row 3: Admin Actions Bar -->
                    <div class="flex flex-wrap items-center justify-between gap-3 border-t border-border/40 pt-3">
                        <div class="flex items-center gap-2">
                            <!-- Payout Status Pill -->
                            <span
                                v-if="booking.dispatch_status === 'completed'"
                                class="inline-flex items-center gap-1 rounded-md px-2 py-0.5 text-[11px] font-bold"
                                :class="booking.payout_status === 'paid' ? 'bg-emerald-500/20 text-emerald-700 dark:text-emerald-300' : 'bg-amber-500/20 text-amber-800 dark:text-amber-300'"
                            >
                                <DollarSign class="size-3" />
                                Payout: {{ booking.payout_status === 'paid' ? 'Paid to Provider' : 'Pending Disbursement' }}
                            </span>
                        </div>

                        <div class="flex flex-wrap items-center gap-2">
                            <!-- Button 1: Dispatch to Provider -->
                            <button
                                type="button"
                                @click="openDispatchModal(booking)"
                                class="inline-flex items-center gap-1.5 rounded-lg bg-primary px-3 py-1.5 text-xs font-bold text-primary-foreground shadow-xs hover:bg-primary/90 transition"
                            >
                                <Truck class="size-3.5" />
                                <span>{{ booking.assigned_provider ? 'Change Provider' : 'Dispatch Gig to Provider' }}</span>
                            </button>

                            <!-- Button 2: Progress Status -->
                            <button
                                v-if="booking.dispatch_status === 'assigned'"
                                type="button"
                                @click="updateBookingStatus(booking.id, 'in_transit')"
                                class="inline-flex items-center gap-1.5 rounded-lg border border-border bg-background px-3 py-1.5 text-xs font-semibold text-foreground hover:bg-muted transition"
                            >
                                <span>Mark In Transit</span>
                            </button>

                            <button
                                v-if="booking.dispatch_status === 'in_transit'"
                                type="button"
                                @click="updateBookingStatus(booking.id, 'delivered_and_setup')"
                                class="inline-flex items-center gap-1.5 rounded-lg border border-teal-500/40 bg-teal-500/10 px-3 py-1.5 text-xs font-semibold text-teal-700 dark:text-teal-300 hover:bg-teal-500/20 transition"
                            >
                                <CheckCircle2 class="size-3.5" />
                                <span>Verify Delivered & Set Up</span>
                            </button>

                            <button
                                v-if="booking.dispatch_status === 'delivered_and_setup'"
                                type="button"
                                @click="updateBookingStatus(booking.id, 'completed', 'pending_payout')"
                                class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-600 px-3 py-1.5 text-xs font-bold text-white shadow-xs hover:bg-emerald-700 transition"
                            >
                                <CheckCircle2 class="size-3.5" />
                                <span>Complete Gig</span>
                            </button>

                            <!-- Button 3: Release Payout -->
                            <button
                                v-if="booking.dispatch_status === 'completed' && booking.payout_status !== 'paid'"
                                type="button"
                                @click="updateBookingStatus(booking.id, 'completed', 'paid')"
                                class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-600 px-3 py-1.5 text-xs font-bold text-white shadow-xs hover:bg-emerald-700 transition"
                            >
                                <DollarSign class="size-3.5" />
                                <span>Disburse Payout via M-Pesa</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
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

        <!-- DISPATCH MODAL -->
        <div
            v-if="isDispatchModalOpen"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4"
        >
            <div class="w-full max-w-md rounded-2xl border border-border bg-card p-6 shadow-2xl space-y-5 animate-in zoom-in-95">
                <div class="flex items-center justify-between border-b border-border/50 pb-3">
                    <div class="flex items-center gap-2">
                        <div class="rounded-lg bg-primary/10 p-2 text-primary">
                            <Truck class="size-4" />
                        </div>
                        <h3 class="text-sm font-bold text-foreground">Dispatch Gig to Verified Provider</h3>
                    </div>
                    <button
                        type="button"
                        @click="isDispatchModalOpen = false"
                        class="rounded-lg p-1 text-muted-foreground hover:bg-muted"
                    >
                        <X class="size-4" />
                    </button>
                </div>

                <div v-if="selectedBooking" class="rounded-lg bg-muted/40 p-3 text-xs space-y-1">
                    <div class="font-bold text-foreground">{{ selectedBooking.name }}</div>
                    <div class="text-muted-foreground">
                        Location: {{ selectedBooking.venue_name || selectedBooking.location || 'Tanzania' }}
                    </div>
                    <div class="text-muted-foreground">
                        Client Order: {{ formatCurrency(selectedBooking.total_client_amount || selectedBooking.budget) }}
                    </div>
                </div>

                <form @submit.prevent="submitDispatch" class="space-y-4">
                    <div class="space-y-1.5">
                        <label class="text-xs font-semibold text-foreground uppercase tracking-wider">
                            Select Verified Service Provider
                        </label>
                        <select
                            v-model="dispatchForm.provider_id"
                            required
                            class="w-full rounded-xl border border-border bg-background px-3 py-2 text-xs font-semibold text-foreground focus:border-primary focus:outline-none"
                        >
                            <option value="" disabled>Choose a verified partner...</option>
                            <option
                                v-for="vendor in verifiedList"
                                :key="vendor.id"
                                :value="vendor.id"
                            >
                                {{ vendor.brand_name }} ({{ vendor.contact_name }})
                            </option>
                        </select>
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-xs font-semibold text-foreground uppercase tracking-wider">
                            Agreed Provider Payout Amount (TZS)
                        </label>
                        <input
                            v-model.number="dispatchForm.provider_payout_amount"
                            type="number"
                            min="0"
                            required
                            placeholder="e.g. 350000"
                            class="w-full rounded-xl border border-border bg-background px-3 py-2 text-xs font-bold text-foreground focus:border-primary focus:outline-none"
                        />
                        <span class="text-[10px] text-muted-foreground">
                            Amount to disburse to vendor after delivery and client confirmation.
                        </span>
                    </div>

                    <div class="flex items-center justify-end gap-2.5 pt-2">
                        <button
                            type="button"
                            @click="isDispatchModalOpen = false"
                            class="rounded-xl border border-border px-4 py-2 text-xs font-semibold text-foreground hover:bg-muted"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            :disabled="dispatchForm.processing || !dispatchForm.provider_id"
                            class="rounded-xl bg-primary px-5 py-2 text-xs font-bold text-primary-foreground shadow hover:bg-primary/90 disabled:opacity-50"
                        >
                            {{ dispatchForm.processing ? 'Dispatching...' : 'Confirm Dispatch & Notify Provider' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
