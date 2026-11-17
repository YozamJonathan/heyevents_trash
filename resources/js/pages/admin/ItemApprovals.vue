<script setup lang="ts">
import { ref } from 'vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import {
    PackageCheck,
    CheckCircle2,
    XCircle,
    Clock,
    AlertCircle,
    Eye,
    Sliders,
    Search,
    MapPin,
    DollarSign,
    Tag,
    X,
} from '@lucide/vue';
import { toast } from 'vue-sonner';

const props = defineProps<{
    pendingItems: any[];
    approvedItems: any[];
    rejectedItems: any[];
}>();

const activeTab = ref<'pending' | 'approved' | 'rejected'>('pending');
const selectedItem = ref<any>(null);
const isRejectModalOpen = ref(false);

const approveForm = useForm({
    client_price: 0 as number,
    is_featured: false,
});

const rejectForm = useForm({
    admin_notes: '',
});

const handleApprove = (item: any) => {
    approveForm.client_price = Number(item.client_price || item.provider_payout * 1.2);
    approveForm.is_featured = !!item.is_featured;

    approveForm.post(`/admin/items/${item.id}/approve`, {
        preserveScroll: true,
        onSuccess: () => {
            toast.success(`"${item.title}" approved and published to Explore!`);
        },
    });
};

const openRejectModal = (item: any) => {
    selectedItem.value = item;
    rejectForm.admin_notes = '';
    isRejectModalOpen.value = true;
};

const handleReject = () => {
    if (!selectedItem.value) return;
    rejectForm.post(`/admin/items/${selectedItem.value.id}/reject`, {
        preserveScroll: true,
        onSuccess: () => {
            isRejectModalOpen.value = false;
            selectedItem.value = null;
            toast.error('Item rejected and feedback sent to provider.');
        },
    });
};

const formatPrice = (price: any) => {
    return 'TZS ' + Number(price || 0).toLocaleString();
};

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard', href: '/dashboard' },
            { title: 'Equipment & Service Upload Approvals', href: '/admin/item-approvals' },
        ],
    },
});
</script>

<template>
    <Head title="Equipment & Service Approvals — HEY Events" />

    <div class="space-y-6 p-4 md:p-6">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border-b border-border/60 pb-5">
                <div>
                    <div class="flex items-center gap-2">
                        <div class="rounded-lg bg-primary/10 p-2 text-primary">
                            <PackageCheck class="size-5" />
                        </div>
                        <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-foreground">
                            Equipment & Services Moderation Desk
                        </h1>
                    </div>
                    <p class="text-xs sm:text-sm text-muted-foreground mt-1">
                        Review tools, rental equipment, and packages posted by verified providers before they become visible to clients on Explore.
                    </p>
                </div>

                <!-- Tabs -->
                <div class="flex items-center gap-1.5 rounded-xl border border-border bg-card p-1 text-xs">
                    <button
                        type="button"
                        @click="activeTab = 'pending'"
                        class="rounded-lg px-3 py-1.5 font-bold transition flex items-center gap-1.5"
                        :class="activeTab === 'pending' ? 'bg-primary text-primary-foreground shadow-xs' : 'text-muted-foreground hover:text-foreground'"
                    >
                        <Clock class="size-3.5" />
                        <span>Pending ({{ props.pendingItems.length }})</span>
                    </button>
                    <button
                        type="button"
                        @click="activeTab = 'approved'"
                        class="rounded-lg px-3 py-1.5 font-bold transition flex items-center gap-1.5"
                        :class="activeTab === 'approved' ? 'bg-emerald-600 text-white shadow-xs' : 'text-muted-foreground hover:text-foreground'"
                    >
                        <CheckCircle2 class="size-3.5" />
                        <span>Live on Explore ({{ props.approvedItems.length }})</span>
                    </button>
                    <button
                        type="button"
                        @click="activeTab = 'rejected'"
                        class="rounded-lg px-3 py-1.5 font-bold transition flex items-center gap-1.5"
                        :class="activeTab === 'rejected' ? 'bg-red-600 text-white shadow-xs' : 'text-muted-foreground hover:text-foreground'"
                    >
                        <XCircle class="size-3.5" />
                        <span>Rejected ({{ props.rejectedItems.length }})</span>
                    </button>
                </div>
            </div>

            <!-- TAB 1: PENDING REVIEW -->
            <div v-if="activeTab === 'pending'" class="space-y-4">
                <div
                    v-if="props.pendingItems.length === 0"
                    class="rounded-2xl border border-border/70 bg-card p-12 text-center shadow-xs"
                >
                    <div class="rounded-full bg-emerald-500/10 p-3 text-emerald-600 dark:text-emerald-400 w-fit mx-auto mb-2.5">
                        <CheckCircle2 class="size-6" />
                    </div>
                    <h3 class="text-sm font-bold text-foreground">All Uploads Reviewed!</h3>
                    <p class="text-xs text-muted-foreground mt-0.5">
                        No pending provider tools or equipment waiting for admin verification.
                    </p>
                </div>

                <div v-else class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                    <div
                        v-for="item in props.pendingItems"
                        :key="item.id"
                        class="rounded-2xl border border-border/70 bg-card p-5 shadow-xs flex flex-col justify-between space-y-4"
                    >
                        <div>
                            <div class="relative overflow-hidden rounded-xl border border-border/60">
                                <img
                                    :src="item.image_url || 'https://images.unsplash.com/photo-1519741497674-611481863552?auto=format&fit=crop&w=800&q=80'"
                                    class="h-44 w-full object-cover"
                                />
                                <span class="absolute top-2.5 right-2.5 rounded-full bg-amber-500/90 text-white font-bold text-[10px] px-2.5 py-0.5 uppercase tracking-wider backdrop-blur-xs">
                                    Awaiting Review
                                </span>
                            </div>

                            <div class="mt-3.5 space-y-1">
                                <span class="text-[10px] font-bold text-primary uppercase tracking-wider block">
                                    {{ item.category }}
                                </span>
                                <h3 class="text-sm font-bold text-foreground leading-snug">
                                    {{ item.title }}
                                </h3>
                                <p class="text-xs text-muted-foreground line-clamp-2">
                                    {{ item.description || 'No description provided.' }}
                                </p>
                            </div>

                            <!-- Provider & Price Breakdown -->
                            <div class="mt-4 rounded-xl bg-muted/40 p-3 text-xs space-y-1.5 border border-border/40">
                                <div class="flex items-center justify-between">
                                    <span class="text-muted-foreground">Provider:</span>
                                    <strong class="text-foreground">{{ item.provider?.brand_name || 'Independent Provider' }}</strong>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-muted-foreground">Provider Rate (Payout):</span>
                                    <strong class="text-emerald-600 dark:text-emerald-400">{{ formatPrice(item.provider_payout) }} / {{ item.pricing_unit }}</strong>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-muted-foreground">Client Market Price:</span>
                                    <strong class="text-foreground">{{ formatPrice(item.client_price) }}</strong>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-muted-foreground">Stock Available:</span>
                                    <span class="font-semibold">{{ item.stock_quantity }} unit(s)</span>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex items-center gap-2 pt-2 border-t border-border/40">
                            <button
                                type="button"
                                @click="openRejectModal(item)"
                                class="flex-1 rounded-xl border border-red-500/40 text-red-600 dark:text-red-400 py-2 text-xs font-bold hover:bg-red-500/10 transition"
                            >
                                Reject / Edit
                            </button>
                            <button
                                type="button"
                                @click="handleApprove(item)"
                                class="flex-1 rounded-xl bg-emerald-600 text-white py-2 text-xs font-bold hover:bg-emerald-700 shadow-xs transition"
                            >
                                Approve & Publish
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB 2: APPROVED & LIVE IN EXPLORE -->
            <div v-else-if="activeTab === 'approved'" class="space-y-4">
                <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                    <div
                        v-for="item in props.approvedItems"
                        :key="item.id"
                        class="rounded-2xl border border-border/70 bg-card p-5 shadow-xs flex flex-col justify-between space-y-4"
                    >
                        <div>
                            <div class="relative overflow-hidden rounded-xl border border-border/60">
                                <img
                                    :src="item.image_url || 'https://images.unsplash.com/photo-1519741497674-611481863552?auto=format&fit=crop&w=800&q=80'"
                                    class="h-40 w-full object-cover"
                                />
                                <span class="absolute top-2.5 right-2.5 rounded-full bg-emerald-600 text-white font-bold text-[10px] px-2.5 py-0.5 uppercase tracking-wider shadow-xs">
                                    Live on Explore
                                </span>
                            </div>

                            <div class="mt-3.5 space-y-1">
                                <span class="text-[10px] font-bold text-primary uppercase tracking-wider block">
                                    {{ item.category }}
                                </span>
                                <h3 class="text-sm font-bold text-foreground">
                                    {{ item.title }}
                                </h3>
                                <div class="flex items-center gap-1.5 text-xs text-muted-foreground">
                                    <MapPin class="size-3 text-primary" />
                                    <span>{{ item.location }}</span>
                                    <span>•</span>
                                    <span>By {{ item.provider?.brand_name || 'Verified' }}</span>
                                </div>
                            </div>

                            <div class="mt-3 rounded-lg bg-muted/40 p-2.5 text-xs flex items-center justify-between">
                                <div>
                                    <span class="text-[10px] uppercase text-muted-foreground block font-bold">Client Price</span>
                                    <span class="text-sm font-extrabold text-[#ff545a]">{{ formatPrice(item.client_price) }}</span>
                                </div>
                                <div class="text-right">
                                    <span class="text-[10px] uppercase text-muted-foreground block font-bold">Provider Payout</span>
                                    <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400">{{ formatPrice(item.provider_payout) }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-between text-[11px] text-muted-foreground border-t border-border/40 pt-2">
                            <span>Stock: {{ item.stock_quantity }} units</span>
                            <span class="text-emerald-600 dark:text-emerald-400 font-semibold flex items-center gap-1">
                                <CheckCircle2 class="size-3" /> Verified & Active
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB 3: REJECTED ITEMS -->
            <div v-else-if="activeTab === 'rejected'" class="space-y-4">
                <div
                    v-if="props.rejectedItems.length === 0"
                    class="rounded-2xl border border-border/70 bg-card p-12 text-center shadow-xs"
                >
                    <h3 class="text-sm font-bold text-foreground">No Rejected Items</h3>
                    <p class="text-xs text-muted-foreground mt-0.5">There are no rejected items currently.</p>
                </div>

                <div v-else class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                    <div
                        v-for="item in props.rejectedItems"
                        :key="item.id"
                        class="rounded-2xl border border-red-500/30 bg-card p-5 shadow-xs space-y-3"
                    >
                        <h3 class="text-sm font-bold text-foreground">{{ item.title }}</h3>
                        <div class="rounded-lg bg-red-500/10 p-3 text-xs text-red-700 dark:text-red-400 space-y-1">
                            <span class="font-bold block">Rejection Feedback to Provider:</span>
                            <p>{{ item.admin_notes || 'Item did not meet marketplace quality standards.' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- REJECT MODAL -->
        <div
            v-if="isRejectModalOpen"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4"
        >
            <div class="w-full max-w-md rounded-2xl border border-border bg-card p-6 shadow-2xl space-y-4 animate-in zoom-in-95">
                <div class="flex items-center justify-between border-b border-border/50 pb-3">
                    <h3 class="text-sm font-bold text-foreground">Reject Equipment / Request Edit</h3>
                    <button type="button" @click="isRejectModalOpen = false" class="text-muted-foreground hover:text-foreground">
                        <X class="size-4" />
                    </button>
                </div>

                <form @submit.prevent="handleReject" class="space-y-4">
                    <div class="space-y-1.5">
                        <label class="text-xs font-semibold text-foreground uppercase tracking-wider">
                            Reason / Action Required for Provider
                        </label>
                        <textarea
                            v-model="rejectForm.admin_notes"
                            required
                            rows="4"
                            placeholder="e.g. Please provide a clear photo of the tent and specify whether generator cables are included..."
                            class="w-full rounded-xl border border-border bg-background p-3 text-xs text-foreground focus:border-primary focus:outline-none"
                        ></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-2">
                        <button
                            type="button"
                            @click="isRejectModalOpen = false"
                            class="rounded-xl border border-border px-4 py-2 text-xs font-semibold text-foreground hover:bg-muted"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            :disabled="rejectForm.processing"
                            class="rounded-xl bg-red-600 px-5 py-2 text-xs font-bold text-white shadow hover:bg-red-700 disabled:opacity-50"
                        >
                            {{ rejectForm.processing ? 'Rejecting...' : 'Confirm Rejection' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
</template>
