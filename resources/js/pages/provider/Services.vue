<script setup lang="ts">
import { ref } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import {
    Briefcase,
    Plus,
    Clock,
    CheckCircle2,
    XCircle,
    AlertCircle,
    Trash2,
    MapPin,
    Tag,
    X,
    UploadCloud,
} from '@lucide/vue';
import { toast } from 'vue-sonner';

const props = defineProps<{
    items: any[];
    isApproved: boolean;
}>();

const isCreateModalOpen = ref(false);

const form = useForm({
    title: '',
    category: 'rentals',
    description: '',
    provider_payout: '' as string | number,
    pricing_unit: 'per day',
    stock_quantity: 1,
    location: 'Dar es Salaam',
    image_url: '',
});

const submit = () => {
    form.post('/provider/services', {
        preserveScroll: true,
        onSuccess: () => {
            isCreateModalOpen.value = false;
            form.reset();
            toast.success('Equipment uploaded! Awaiting HEY Events Admin review.');
        },
    });
};

const deleteItem = (item: any) => {
    if (confirm(`Are you sure you want to remove "${item.title}"?`)) {
        form.delete(`/provider/services/${item.id}`, {
            preserveScroll: true,
            onSuccess: () => {
                toast.success('Item removed successfully.');
            },
        });
    }
};

const formatPrice = (val: any) => {
    return 'TZS ' + Number(val || 0).toLocaleString();
};

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard', href: '/dashboard' },
            { title: 'My Equipment & Tools', href: '/provider/services' },
        ],
    },
});
</script>

<template>
    <Head title="My Equipment & Tools — HEY Events" />

    <div class="space-y-6 p-4 md:p-6">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border-b border-border/60 pb-5">
                <div>
                    <div class="flex items-center gap-2">
                        <div class="rounded-lg bg-primary/10 p-2 text-primary">
                            <Briefcase class="size-5" />
                        </div>
                        <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-foreground">
                            My Equipment, Tools & Packages
                        </h1>
                    </div>
                    <p class="text-xs sm:text-sm text-muted-foreground mt-1">
                        Post your rental gear, sound systems, tents, or service packages. All postings are reviewed by HEY Events Admin before going live.
                    </p>
                </div>

                <button
                    type="button"
                    @click="isCreateModalOpen = true"
                    :disabled="!isApproved"
                    class="inline-flex items-center gap-1.5 self-start rounded-xl bg-primary px-4 py-2 text-xs font-bold text-primary-foreground shadow-sm hover:bg-primary/90 transition disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    <Plus class="size-4" />
                    <span>Post New Equipment / Service</span>
                </button>
            </div>

            <!-- KYC Warning if not approved -->
            <div
                v-if="!isApproved"
                class="rounded-2xl border border-amber-500/30 bg-amber-500/10 p-4 text-xs text-amber-800 dark:text-amber-300 flex items-center gap-3"
            >
                <AlertCircle class="size-5 shrink-0 text-amber-600 dark:text-amber-400" />
                <div>
                    <strong class="font-bold block">Account KYC In Review</strong>
                    <span>Your vendor registration is currently under admin review. Once verified, you will be able to post tools and receive client orders.</span>
                </div>
            </div>

            <!-- Empty State -->
            <div
                v-if="items.length === 0"
                class="rounded-2xl border border-border/70 bg-card p-12 text-center shadow-xs"
            >
                <div class="rounded-full bg-muted p-4 text-muted-foreground w-fit mx-auto mb-3">
                    <Briefcase class="size-8" />
                </div>
                <h3 class="text-base font-bold text-foreground">No Equipment or Services Posted Yet</h3>
                <p class="text-xs text-muted-foreground mt-1 max-w-sm mx-auto">
                    Start by posting the chairs, tents, audio equipment, lighting rigs, or catering services you own.
                </p>
                <button
                    type="button"
                    @click="isCreateModalOpen = true"
                    :disabled="!isApproved"
                    class="mt-4 inline-flex items-center gap-1.5 rounded-xl bg-primary px-4 py-2 text-xs font-bold text-primary-foreground shadow-sm hover:bg-primary/90 transition disabled:opacity-50"
                >
                    <Plus class="size-4" />
                    <span>Post Your First Tool / Equipment</span>
                </button>
            </div>

            <!-- Populated Grid -->
            <div v-else class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                <div
                    v-for="item in items"
                    :key="item.id"
                    class="rounded-2xl border border-border/70 bg-card p-5 shadow-xs flex flex-col justify-between space-y-4 transition hover:border-border/90"
                >
                    <div>
                        <div class="relative overflow-hidden rounded-xl border border-border/60">
                            <img
                                :src="item.image_url || 'https://images.unsplash.com/photo-1519741497674-611481863552?auto=format&fit=crop&w=800&q=80'"
                                class="h-44 w-full object-cover"
                            />
                            <!-- Status Badge -->
                            <span
                                class="absolute top-2.5 right-2.5 rounded-full font-bold text-[10px] px-2.5 py-0.5 uppercase tracking-wider shadow-xs"
                                :class="{
                                    'bg-amber-500 text-white': item.status === 'pending',
                                    'bg-emerald-600 text-white': item.status === 'approved',
                                    'bg-red-600 text-white': item.status === 'rejected',
                                }"
                            >
                                {{
                                    item.status === 'pending'
                                        ? 'Under Admin Review'
                                        : item.status === 'approved'
                                        ? 'Verified & Live'
                                        : 'Needs Edit'
                                }}
                            </span>
                        </div>

                        <div class="mt-3.5 space-y-1">
                            <span class="text-[10px] font-bold text-primary uppercase tracking-wider block">
                                {{ item.category }}
                            </span>
                            <h3 class="text-sm font-bold text-foreground">
                                {{ item.title }}
                            </h3>
                            <p class="text-xs text-muted-foreground line-clamp-2">
                                {{ item.description || 'No description provided.' }}
                            </p>
                        </div>

                        <!-- Feedback if rejected -->
                        <div
                            v-if="item.status === 'rejected' && item.admin_notes"
                            class="mt-3 rounded-lg bg-red-500/10 p-2.5 text-xs text-red-700 dark:text-red-400 space-y-0.5"
                        >
                            <span class="font-bold block">Admin Feedback:</span>
                            <p>{{ item.admin_notes }}</p>
                        </div>

                        <!-- Rate Details -->
                        <div class="mt-4 rounded-xl bg-muted/40 p-3 text-xs space-y-1 border border-border/40">
                            <div class="flex items-center justify-between">
                                <span class="text-muted-foreground">Your Payout Rate:</span>
                                <strong class="text-emerald-600 dark:text-emerald-400 text-sm">
                                    {{ formatPrice(item.provider_payout) }} / {{ item.pricing_unit }}
                                </strong>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-muted-foreground">Stock Available:</span>
                                <span class="font-semibold text-foreground">{{ item.stock_quantity }} unit(s)</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-muted-foreground">Location:</span>
                                <span class="font-semibold text-foreground">{{ item.location }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Footer Actions -->
                    <div class="flex items-center justify-between border-t border-border/40 pt-2 text-xs">
                        <span class="text-muted-foreground text-[11px]">
                            Added: {{ new Date(item.created_at).toLocaleDateString() }}
                        </span>
                        <button
                            type="button"
                            @click="deleteItem(item)"
                            class="text-red-600 dark:text-red-400 p-1 rounded-lg hover:bg-red-500/10 transition"
                            title="Remove tool"
                        >
                            <Trash2 class="size-4" />
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- POST ITEM MODAL -->
        <div
            v-if="isCreateModalOpen"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4 overflow-y-auto"
        >
            <div class="w-full max-w-lg rounded-2xl border border-border bg-card p-6 shadow-2xl space-y-4 my-8 animate-in zoom-in-95">
                <div class="flex items-center justify-between border-b border-border/50 pb-3">
                    <div class="flex items-center gap-2">
                        <div class="rounded-lg bg-primary/10 p-2 text-primary">
                            <Plus class="size-4" />
                        </div>
                        <h3 class="text-sm font-bold text-foreground">Post Tool, Equipment or Package</h3>
                    </div>
                    <button type="button" @click="isCreateModalOpen = false" class="text-muted-foreground hover:text-foreground">
                        <X class="size-4" />
                    </button>
                </div>

                <form @submit.prevent="submit" class="space-y-4">
                    <!-- Title -->
                    <div class="space-y-1">
                        <label class="text-xs font-semibold text-foreground uppercase tracking-wider">
                            Title / Tool Name <span class="text-red-500">*</span>
                        </label>
                        <input
                            v-model="form.title"
                            type="text"
                            required
                            placeholder="e.g. Hexagonal Canopy Tent (100 - 250 Guests)"
                            class="w-full rounded-xl border border-border bg-background px-3 py-2 text-xs text-foreground focus:border-primary focus:outline-none"
                        />
                    </div>

                    <!-- Category & Location -->
                    <div class="grid grid-cols-2 gap-3">
                        <div class="space-y-1">
                            <label class="text-xs font-semibold text-foreground uppercase tracking-wider">
                                Category <span class="text-red-500">*</span>
                            </label>
                            <select
                                v-model="form.category"
                                required
                                class="w-full rounded-xl border border-border bg-background px-3 py-2 text-xs text-foreground focus:border-primary focus:outline-none"
                            >
                                <option value="rentals">Equipment & Rentals (Chairs, Tents, Tables)</option>
                                <option value="sound">Sound, Audio & DJ</option>
                                <option value="venues">Venues & Halls</option>
                                <option value="catering">Catering & Food</option>
                                <option value="decor">Decoration & Floral</option>
                                <option value="photography">Photography & Video</option>
                                <option value="protocol">Transport & Protocol</option>
                            </select>
                        </div>

                        <div class="space-y-1">
                            <label class="text-xs font-semibold text-foreground uppercase tracking-wider">
                                Location / Region <span class="text-red-500">*</span>
                            </label>
                            <input
                                v-model="form.location"
                                type="text"
                                required
                                placeholder="e.g. Dar es Salaam, Arusha"
                                class="w-full rounded-xl border border-border bg-background px-3 py-2 text-xs text-foreground focus:border-primary focus:outline-none"
                            />
                        </div>
                    </div>

                    <!-- Rate & Unit & Stock -->
                    <div class="grid grid-cols-3 gap-3">
                        <div class="space-y-1">
                            <label class="text-xs font-semibold text-foreground uppercase tracking-wider">
                                Your Rate (TZS) <span class="text-red-500">*</span>
                            </label>
                            <input
                                v-model.number="form.provider_payout"
                                type="number"
                                min="500"
                                required
                                placeholder="e.g. 350000"
                                class="w-full rounded-xl border border-border bg-background px-3 py-2 text-xs font-bold text-foreground focus:border-primary focus:outline-none"
                            />
                        </div>

                        <div class="space-y-1">
                            <label class="text-xs font-semibold text-foreground uppercase tracking-wider">
                                Pricing Unit <span class="text-red-500">*</span>
                            </label>
                            <select
                                v-model="form.pricing_unit"
                                required
                                class="w-full rounded-xl border border-border bg-background px-3 py-2 text-xs text-foreground focus:border-primary focus:outline-none"
                            >
                                <option value="per day">per day</option>
                                <option value="per event">per event</option>
                                <option value="per piece">per piece / chair</option>
                                <option value="per hour">per hour</option>
                                <option value="per plate">per plate</option>
                            </select>
                        </div>

                        <div class="space-y-1">
                            <label class="text-xs font-semibold text-foreground uppercase tracking-wider">
                                Stock Qty <span class="text-red-500">*</span>
                            </label>
                            <input
                                v-model.number="form.stock_quantity"
                                type="number"
                                min="1"
                                required
                                class="w-full rounded-xl border border-border bg-background px-3 py-2 text-xs font-bold text-foreground focus:border-primary focus:outline-none"
                            />
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="space-y-1">
                        <label class="text-xs font-semibold text-foreground uppercase tracking-wider">
                            Description & Specifications
                        </label>
                        <textarea
                            v-model="form.description"
                            rows="3"
                            placeholder="Provide specifications, dimensions, power requirements, or what is included..."
                            class="w-full rounded-xl border border-border bg-background p-3 text-xs text-foreground focus:border-primary focus:outline-none"
                        ></textarea>
                    </div>

                    <!-- Photo URL -->
                    <div class="space-y-1">
                        <label class="text-xs font-semibold text-foreground uppercase tracking-wider">
                            Photo Link (Optional)
                        </label>
                        <input
                            v-model="form.image_url"
                            type="url"
                            placeholder="https://... (leave empty for automatic placeholder)"
                            class="w-full rounded-xl border border-border bg-background px-3 py-2 text-xs text-foreground focus:border-primary focus:outline-none"
                        />
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-2">
                        <button
                            type="button"
                            @click="isCreateModalOpen = false"
                            class="rounded-xl border border-border px-4 py-2 text-xs font-semibold text-foreground hover:bg-muted"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="rounded-xl bg-primary px-5 py-2 text-xs font-bold text-primary-foreground shadow hover:bg-primary/90 disabled:opacity-50"
                        >
                            {{ form.processing ? 'Uploading...' : 'Submit for Admin Verification' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
</template>
