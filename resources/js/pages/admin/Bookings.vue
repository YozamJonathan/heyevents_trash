<script setup lang="ts">
import { ref, computed } from 'vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import {
    Truck,
    Calendar,
    Clock,
    CheckCircle2,
    DollarSign,
    Phone,
    MapPin,
    AlertCircle,
    BadgeCheck,
    X,
} from '@lucide/vue';
import { toast } from 'vue-sonner';

const props = defineProps<{
    bookings: any[];
    verifiedVendors: any[];
}>();

const filterStatus = ref('all');

const filteredBookings = computed(() => {
    if (filterStatus.value === 'all') return props.bookings;
    return props.bookings.filter(b => b.dispatch_status === filterStatus.value);
});

// Dispatch Modal state
const isDispatchModalOpen = ref(false);
const selectedBooking = ref<any>(null);

const dispatchForm = useForm({
    provider_id: '' as string | number,
    provider_payout_amount: '' as string | number,
});

const openDispatchModal = (booking: any) => {
    selectedBooking.value = booking;
    dispatchForm.provider_id = booking.assigned_provider_id || (props.verifiedVendors?.[0]?.id ?? '');
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

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard', href: '/dashboard' },
            { title: 'Client Bookings & Orders', href: '/admin/bookings' },
        ],
    },
});
</script>

<template>
    <Head title="Client Bookings & Fulfillment Desk — HEY Events" />

    <div class="space-y-6 p-4 md:p-6">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border-b border-border/60 pb-5">
                <div>
                    <div class="flex items-center gap-2">
                        <div class="rounded-lg bg-primary/10 p-2 text-primary">
                            <Truck class="size-5" />
                        </div>
                        <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-foreground">
                            Client Event Bookings & Fulfillment Desk
                        </h1>
                    </div>
                    <p class="text-xs sm:text-sm text-muted-foreground mt-1">
                        Oversee all client bookings, coordinate equipment delivery, dispatch gigs to verified providers, and release payouts.
                    </p>
                </div>

                <!-- Filter Chips -->
                <div class="flex flex-wrap items-center gap-1.5 rounded-xl border border-border bg-card p-1 text-xs">
                    <button
                        type="button"
                        @click="filterStatus = 'all'"
                        class="rounded-lg px-3 py-1.5 font-bold transition"
                        :class="filterStatus === 'all' ? 'bg-primary text-primary-foreground shadow-xs' : 'text-muted-foreground hover:text-foreground'"
                    >
                        All ({{ props.bookings.length }})
                    </button>
                    <button
                        type="button"
                        @click="filterStatus = 'pending_dispatch'"
                        class="rounded-lg px-3 py-1.5 font-bold transition"
                        :class="filterStatus === 'pending_dispatch' ? 'bg-amber-500 text-white shadow-xs' : 'text-muted-foreground hover:text-foreground'"
                    >
                        Awaiting Dispatch ({{ props.bookings.filter(b => b.dispatch_status === 'pending_dispatch').length }})
                    </button>
                    <button
                        type="button"
                        @click="filterStatus = 'delivered_and_setup'"
                        class="rounded-lg px-3 py-1.5 font-bold transition"
                        :class="filterStatus === 'delivered_and_setup' ? 'bg-teal-600 text-white shadow-xs' : 'text-muted-foreground hover:text-foreground'"
                    >
                        Delivered & Set Up
                    </button>
                    <button
                        type="button"
                        @click="filterStatus = 'completed'"
                        class="rounded-lg px-3 py-1.5 font-bold transition"
                        :class="filterStatus === 'completed' ? 'bg-emerald-600 text-white shadow-xs' : 'text-muted-foreground hover:text-foreground'"
                    >
                        Completed
                    </button>
                </div>
            </div>

            <!-- Empty state -->
            <div
                v-if="filteredBookings.length === 0"
                class="rounded-2xl border border-border/70 bg-card p-12 text-center shadow-xs"
            >
                <div class="rounded-full bg-muted p-3 text-muted-foreground w-fit mx-auto mb-2">
                    <Truck class="size-6" />
                </div>
                <h3 class="text-sm font-bold text-foreground">No Event Bookings Found</h3>
                <p class="text-xs text-muted-foreground mt-0.5">There are no client bookings matching this status.</p>
            </div>

            <!-- Populated list -->
            <div v-else class="space-y-4">
                <div
                    v-for="booking in filteredBookings"
                    :key="booking.id"
                    class="rounded-2xl border border-border/70 bg-card p-5 shadow-xs space-y-4 transition hover:border-border/90"
                >
                    <!-- Header: Client & Event Info -->
                    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3 border-b border-border/40 pb-3">
                        <div>
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="rounded bg-primary/10 px-2 py-0.5 text-[10px] font-bold text-primary uppercase">
                                    {{ booking.event_type }}
                                </span>
                                <h3 class="text-base font-bold text-foreground">
                                    {{ booking.name }}
                                </h3>
                            </div>
                            <div class="mt-1 flex flex-wrap items-center gap-3 text-xs text-muted-foreground">
                                <span>Host: <strong class="text-foreground">{{ booking.user?.name || 'Client' }}</strong></span>
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
                        <span
                            class="inline-flex items-center gap-1 rounded-full px-3 py-1 text-xs font-bold self-start"
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

                    <!-- Row 2: Equipment / Item Details & Financials -->
                    <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3 text-xs">
                        <!-- Requested Item / Services -->
                        <div class="rounded-xl bg-muted/40 p-3.5 border border-border/40">
                            <span class="text-[10px] font-bold text-muted-foreground uppercase block mb-1">Requested Gear / Package</span>
                            <div v-if="booking.order_items && booking.order_items.length > 0" class="space-y-1.5 max-h-28 overflow-y-auto pr-1">
                                <div
                                    v-for="oItem in booking.order_items"
                                    :key="oItem.id"
                                    class="flex items-center justify-between text-[11px] gap-2 py-0.5 border-b border-border/30 last:border-0"
                                >
                                    <div class="flex items-center gap-1.5 min-w-0">
                                        <span class="font-extrabold text-[#ff545a] shrink-0">{{ oItem.quantity }}x</span>
                                        <span class="truncate font-semibold text-foreground">{{ oItem.title }}</span>
                                    </div>
                                    <span class="text-muted-foreground font-medium shrink-0">
                                        {{ formatCurrency(oItem.subtotal || (oItem.client_price * oItem.quantity)) }}
                                    </span>
                                </div>
                            </div>
                            <div v-else-if="booking.catalog_item" class="flex items-center gap-2.5">
                                <img
                                    :src="booking.catalog_item.image_url || 'https://images.unsplash.com/photo-1519741497674-611481863552?auto=format&fit=crop&w=800&q=80'"
                                    class="size-12 rounded-lg object-cover"
                                />
                                <div>
                                    <h4 class="font-bold text-foreground text-xs leading-snug">{{ booking.catalog_item.title }}</h4>
                                    <span class="text-muted-foreground text-[11px]">Qty: {{ booking.item_quantity || 1 }} unit(s)</span>
                                </div>
                            </div>
                            <div v-else class="text-foreground font-medium">
                                {{ booking.services?.join(', ') || 'Custom Event Package' }}
                            </div>
                        </div>

                        <!-- Assigned Provider -->
                        <div class="rounded-xl bg-muted/40 p-3.5 border border-border/40">
                            <span class="text-[10px] font-bold text-muted-foreground uppercase block mb-1">Assigned Service Provider</span>
                            <div v-if="booking.assigned_provider" class="flex items-center gap-2">
                                <BadgeCheck class="size-5 text-emerald-500 shrink-0" />
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
                        <div class="rounded-xl bg-muted/40 p-3.5 border border-border/40 sm:col-span-2 lg:col-span-1 flex items-center justify-between">
                            <div>
                                <span class="text-[10px] font-bold text-muted-foreground uppercase block">Client Order Value</span>
                                <span class="text-base font-extrabold text-foreground">
                                    {{ formatCurrency(booking.total_client_amount || booking.budget) }}
                                </span>
                            </div>
                            <div class="text-right">
                                <span class="text-[10px] font-bold text-muted-foreground uppercase block">Provider Payout</span>
                                <span class="text-base font-extrabold text-emerald-600 dark:text-emerald-400">
                                    {{ formatCurrency(booking.provider_payout_amount) }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Row 3: Admin Actions Bar -->
                    <div class="flex flex-wrap items-center justify-between gap-3 border-t border-border/40 pt-3">
                        <div class="flex items-center gap-2">
                            <span
                                v-if="booking.dispatch_status === 'completed'"
                                class="inline-flex items-center gap-1 rounded-md px-2.5 py-1 text-xs font-bold"
                                :class="booking.payout_status === 'paid' ? 'bg-emerald-500/20 text-emerald-700 dark:text-emerald-300' : 'bg-amber-500/20 text-amber-800 dark:text-amber-300'"
                            >
                                <DollarSign class="size-3.5" />
                                Payout: {{ booking.payout_status === 'paid' ? 'Paid to Provider' : 'Pending Disbursement' }}
                            </span>
                        </div>

                        <div class="flex flex-wrap items-center gap-2">
                            <!-- Button 1: Dispatch to Provider -->
                            <button
                                type="button"
                                @click="openDispatchModal(booking)"
                                class="inline-flex items-center gap-1.5 rounded-xl bg-primary px-3.5 py-2 text-xs font-bold text-primary-foreground shadow-xs hover:bg-primary/90 transition"
                            >
                                <Truck class="size-3.5" />
                                <span>{{ booking.assigned_provider ? 'Change Provider' : 'Dispatch Gig to Provider' }}</span>
                            </button>

                            <!-- Button 2: Progress Status -->
                            <button
                                v-if="booking.dispatch_status === 'assigned'"
                                type="button"
                                @click="updateBookingStatus(booking.id, 'in_transit')"
                                class="inline-flex items-center gap-1.5 rounded-xl border border-border bg-background px-3.5 py-2 text-xs font-semibold text-foreground hover:bg-muted transition"
                            >
                                <span>Mark In Transit</span>
                            </button>

                            <button
                                v-if="booking.dispatch_status === 'in_transit'"
                                type="button"
                                @click="updateBookingStatus(booking.id, 'delivered_and_setup')"
                                class="inline-flex items-center gap-1.5 rounded-xl border border-teal-500/40 bg-teal-500/10 px-3.5 py-2 text-xs font-semibold text-teal-700 dark:text-teal-300 hover:bg-teal-500/20 transition"
                            >
                                <CheckCircle2 class="size-3.5" />
                                <span>Verify Delivered & Set Up</span>
                            </button>

                            <button
                                v-if="booking.dispatch_status === 'delivered_and_setup'"
                                type="button"
                                @click="updateBookingStatus(booking.id, 'completed', 'pending_payout')"
                                class="inline-flex items-center gap-1.5 rounded-xl bg-emerald-600 px-3.5 py-2 text-xs font-bold text-white shadow-xs hover:bg-emerald-700 transition"
                            >
                                <CheckCircle2 class="size-3.5" />
                                <span>Complete Gig</span>
                            </button>

                            <!-- Button 3: Release Payout -->
                            <button
                                v-if="booking.dispatch_status === 'completed' && booking.payout_status !== 'paid'"
                                type="button"
                                @click="updateBookingStatus(booking.id, 'completed', 'paid')"
                                class="inline-flex items-center gap-1.5 rounded-xl bg-emerald-600 px-3.5 py-2 text-xs font-bold text-white shadow-xs hover:bg-emerald-700 transition"
                            >
                                <DollarSign class="size-3.5" />
                                <span>Disburse Payout via M-Pesa</span>
                            </button>
                        </div>
                    </div>
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
                                v-for="vendor in verifiedVendors"
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
</template>
