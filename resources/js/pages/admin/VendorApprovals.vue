<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { dashboard } from '@/routes';
import {
    CheckCircle2,
    Clock,
    AlertCircle,
    Building2,
    User as UserIcon,
    Phone,
    PhoneCall,
    FileText,
    ExternalLink,
    Search,
    Check,
    X,
    RotateCcw,
    ShieldCheck,
    MessageCircle,
    Camera,
    Video,
    Inbox,
    Filter,
} from '@lucide/vue';
import { useLanguage } from '@/composables/useLanguage';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Dashboard',
                href: dashboard(),
            },
            {
                title: 'Vendor Approvals (KYC)',
                href: '/admin/vendor-approvals',
            },
        ],
    },
});

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
    };
}

const props = defineProps<{
    vendors: VendorProfile[];
    currentStatus: string;
    counts: {
        all: number;
        pending: number;
        approved: number;
        rejected: number;
    };
}>();

const { t } = useLanguage();

const searchQuery = ref('');
const activeTab = ref<string>(props.currentStatus || 'all');
const processingId = ref<number | null>(null);

function selectTab(status: string) {
    activeTab.value = status;
    router.get(
        '/admin/vendor-approvals',
        status === 'all' ? {} : { status },
        { preserveState: true, replace: true },
    );
}

const filteredVendors = computed(() => {
    let list = props.vendors;

    if (activeTab.value !== 'all') {
        list = list.filter((v) => v.status === activeTab.value);
    }

    if (!searchQuery.value.trim()) return list;

    const query = searchQuery.value.toLowerCase().trim();
    return list.filter((v) => {
        return (
            v.brand_name.toLowerCase().includes(query) ||
            v.contact_name.toLowerCase().includes(query) ||
            v.nida_number.toLowerCase().includes(query) ||
            (v.primary_phone && v.primary_phone.toLowerCase().includes(query)) ||
            (v.user?.email && v.user.email.toLowerCase().includes(query)) ||
            (v.categories && v.categories.some((c) => c.toLowerCase().includes(query)))
        );
    });
});

function approveVendor(vendor: VendorProfile) {
    if (!confirm(`Are you sure you want to approve "${vendor.brand_name}" as an accredited service provider?`)) {
        return;
    }

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
    const reason = window.prompt(
        `Please specify the reason for rejecting "${vendor.brand_name}" (this will be shown to the provider):`,
        vendor.rejection_reason || 'Business licence or NIDA details could not be verified.',
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

function formatDate(dateStr?: string | null) {
    if (!dateStr) return '—';
    try {
        return new Date(dateStr).toLocaleDateString(undefined, {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
        });
    } catch {
        return dateStr;
    }
}

function cleanPhoneUrl(phone?: string) {
    if (!phone) return '#';
    return `tel:${phone.replace(/[^+\d]/g, '')}`;
}

function cleanWhatsAppUrl(wa?: string) {
    if (!wa) return '#';
    if (wa.startsWith('http')) return wa;
    const digits = wa.replace(/[^\d]/g, '');
    return `https://wa.me/${digits}`;
}

function cleanInstagramUrl(handle?: string) {
    if (!handle) return '#';
    if (handle.startsWith('http')) return handle;
    const username = handle.replace(/^@/, '');
    return `https://instagram.com/${username}`;
}

function cleanTikTokUrl(handle?: string) {
    if (!handle) return '#';
    if (handle.startsWith('http')) return handle;
    const username = handle.startsWith('@') ? handle : `@${handle}`;
    return `https://tiktok.com/${username}`;
}
</script>

<template>
    <Head title="Vendor Approvals (KYC)" />

    <div class="space-y-6">
        <!-- Page Header -->
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-foreground font-['Poppins',sans-serif]">
                    Vendor Approvals (KYC)
                </h1>
                <p class="text-xs sm:text-sm text-muted-foreground mt-1">
                    Review and verify service provider credentials, NIDA identity, and official business licences.
                </p>
            </div>
            <Link
                href="/dashboard"
                class="inline-flex self-start items-center gap-1.5 rounded-xl border border-border/60 bg-background px-3.5 py-2 text-xs font-semibold text-foreground transition hover:bg-muted"
            >
                ← Back to Dashboard
            </Link>
        </div>

        <!-- Filter Tabs / Count Cards -->
        <div class="grid gap-3 grid-cols-2 sm:grid-cols-4">
            <button
                type="button"
                @click="selectTab('all')"
                class="flex flex-col gap-1 rounded-xl border p-4 text-left transition-all"
                :class="
                    activeTab === 'all'
                        ? 'border-[#ff545a] bg-[#ff545a]/5 shadow-xs'
                        : 'border-border/70 bg-card hover:border-border'
                "
            >
                <span class="text-xs font-medium text-muted-foreground">All Submissions</span>
                <div class="flex items-baseline justify-between mt-1">
                    <span class="text-2xl font-bold text-foreground">{{ counts.all }}</span>
                    <Filter class="size-4 text-muted-foreground/60" />
                </div>
            </button>

            <button
                type="button"
                @click="selectTab('pending')"
                class="flex flex-col gap-1 rounded-xl border p-4 text-left transition-all"
                :class="
                    activeTab === 'pending'
                        ? 'border-amber-500 bg-amber-500/10 shadow-xs'
                        : 'border-border/70 bg-card hover:border-amber-500/40'
                "
            >
                <div class="flex items-center gap-1.5">
                    <span class="size-2 rounded-full bg-amber-500 animate-pulse"></span>
                    <span class="text-xs font-medium text-amber-700 dark:text-amber-400">Awaiting Review</span>
                </div>
                <div class="flex items-baseline justify-between mt-1">
                    <span class="text-2xl font-bold text-foreground">{{ counts.pending }}</span>
                    <Clock class="size-4 text-amber-600 dark:text-amber-400" />
                </div>
            </button>

            <button
                type="button"
                @click="selectTab('approved')"
                class="flex flex-col gap-1 rounded-xl border p-4 text-left transition-all"
                :class="
                    activeTab === 'approved'
                        ? 'border-emerald-500 bg-emerald-500/10 shadow-xs'
                        : 'border-border/70 bg-card hover:border-emerald-500/40'
                "
            >
                <span class="text-xs font-medium text-emerald-700 dark:text-emerald-400">Verified Vendors</span>
                <div class="flex items-baseline justify-between mt-1">
                    <span class="text-2xl font-bold text-foreground">{{ counts.approved }}</span>
                    <CheckCircle2 class="size-4 text-emerald-600 dark:text-emerald-400" />
                </div>
            </button>

            <button
                type="button"
                @click="selectTab('rejected')"
                class="flex flex-col gap-1 rounded-xl border p-4 text-left transition-all"
                :class="
                    activeTab === 'rejected'
                        ? 'border-rose-500 bg-rose-500/10 shadow-xs'
                        : 'border-border/70 bg-card hover:border-rose-500/40'
                "
            >
                <span class="text-xs font-medium text-rose-700 dark:text-rose-400">Rejected / Revisions</span>
                <div class="flex items-baseline justify-between mt-1">
                    <span class="text-2xl font-bold text-foreground">{{ counts.rejected }}</span>
                    <AlertCircle class="size-4 text-rose-600 dark:text-rose-400" />
                </div>
            </button>
        </div>

        <!-- Search Bar -->
        <div class="relative">
            <Search class="absolute left-3.5 top-1/2 -translate-y-1/2 size-4 text-muted-foreground" />
            <input
                v-model="searchQuery"
                type="text"
                placeholder="Search vendors by brand name, representative, NIDA, phone, service, or email..."
                class="w-full h-11 pl-10 pr-4 rounded-xl border border-border/80 bg-card text-xs text-foreground placeholder:text-muted-foreground focus:border-[#ff545a] focus:ring-1 focus:ring-[#ff545a] outline-none transition"
            />
        </div>

        <!-- Empty State -->
        <div
            v-if="filteredVendors.length === 0"
            class="flex flex-col items-center justify-center rounded-2xl border border-dashed border-border/80 bg-card/60 p-12 text-center"
        >
            <div class="rounded-full bg-muted p-4 text-muted-foreground mb-3">
                <Inbox class="size-8" />
            </div>
            <h3 class="text-base font-semibold text-foreground">No vendor applications found</h3>
            <p class="text-xs text-muted-foreground mt-1 max-w-sm">
                {{ searchQuery ? 'Try adjusting your search criteria or filter tabs.' : 'There are no vendor profiles matching this status at the moment.' }}
            </p>
        </div>

        <!-- Vendor Cards Grid -->
        <div v-else class="space-y-4">
            <div
                v-for="vendor in filteredVendors"
                :key="vendor.id"
                class="group rounded-2xl border border-border/80 bg-card p-5 sm:p-6 shadow-sm transition hover:border-[#ff545a]/40 dark:bg-card"
            >
                <!-- Card Header -->
                <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between pb-4 border-b border-border/50">
                    <div class="space-y-1">
                        <div class="flex flex-wrap items-center gap-2">
                            <h2 class="text-lg font-bold text-foreground font-['Poppins',sans-serif]">
                                {{ vendor.brand_name }}
                            </h2>
                            <!-- Status Pill -->
                            <span
                                v-if="vendor.status === 'pending'"
                                class="inline-flex items-center gap-1.5 rounded-full bg-amber-500/15 px-2.5 py-0.5 text-[11px] font-bold text-amber-700 dark:text-amber-300 uppercase"
                            >
                                <span class="size-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                Pending Verification
                            </span>
                            <span
                                v-else-if="vendor.status === 'approved'"
                                class="inline-flex items-center gap-1.5 rounded-full bg-emerald-500/15 px-2.5 py-0.5 text-[11px] font-bold text-emerald-700 dark:text-emerald-300 uppercase"
                            >
                                <ShieldCheck class="size-3 text-emerald-600 dark:text-emerald-400" />
                                Verified Vendor
                            </span>
                            <span
                                v-else
                                class="inline-flex items-center gap-1.5 rounded-full bg-rose-500/15 px-2.5 py-0.5 text-[11px] font-bold text-rose-700 dark:text-rose-300 uppercase"
                            >
                                <X class="size-3 text-rose-600 dark:text-rose-400" />
                                Application Rejected
                            </span>
                        </div>

                        <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-muted-foreground">
                            <span class="flex items-center gap-1">
                                <UserIcon class="size-3.5 text-[#ff545a]" />
                                <span class="font-medium text-foreground">{{ vendor.contact_name }}</span>
                            </span>
                            <span v-if="vendor.user?.email" class="text-muted-foreground">
                                {{ vendor.user.email }}
                            </span>
                            <span>Submitted: {{ formatDate(vendor.created_at) }}</span>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex flex-wrap items-center gap-2">
                        <!-- Pending Actions -->
                        <template v-if="vendor.status === 'pending'">
                            <button
                                type="button"
                                @click="rejectVendor(vendor)"
                                :disabled="processingId === vendor.id"
                                class="inline-flex items-center gap-1.5 rounded-xl border border-rose-500/40 bg-rose-500/10 px-3.5 py-2 text-xs font-semibold text-rose-700 hover:bg-rose-500/20 dark:text-rose-300 transition disabled:opacity-50"
                            >
                                <X class="size-3.5" />
                                <span>Reject</span>
                            </button>
                            <button
                                type="button"
                                @click="approveVendor(vendor)"
                                :disabled="processingId === vendor.id"
                                class="inline-flex items-center gap-1.5 rounded-xl bg-emerald-600 px-4 py-2 text-xs font-semibold text-white shadow-xs hover:bg-emerald-700 transition disabled:opacity-50"
                            >
                                <Check class="size-3.5" />
                                <span>Approve Vendor</span>
                            </button>
                        </template>

                        <!-- Approved Actions -->
                        <template v-else-if="vendor.status === 'approved'">
                            <div class="flex items-center gap-2">
                                <span class="text-[11px] text-muted-foreground">
                                    Verified on {{ formatDate(vendor.verified_at) }}
                                    <span v-if="vendor.verifier"> by {{ vendor.verifier.name }}</span>
                                </span>
                                <button
                                    type="button"
                                    @click="rejectVendor(vendor)"
                                    :disabled="processingId === vendor.id"
                                    class="inline-flex items-center gap-1 rounded-lg border border-border px-2.5 py-1.5 text-[11px] font-medium text-muted-foreground hover:text-rose-600 hover:border-rose-400 transition"
                                >
                                    <RotateCcw class="size-3" />
                                    <span>Revoke</span>
                                </button>
                            </div>
                        </template>

                        <!-- Rejected Actions -->
                        <template v-else>
                            <button
                                type="button"
                                @click="approveVendor(vendor)"
                                :disabled="processingId === vendor.id"
                                class="inline-flex items-center gap-1.5 rounded-xl bg-emerald-600 px-3.5 py-2 text-xs font-semibold text-white shadow-xs hover:bg-emerald-700 transition disabled:opacity-50"
                            >
                                <Check class="size-3.5" />
                                <span>Re-evaluate & Approve</span>
                            </button>
                        </template>
                    </div>
                </div>

                <!-- Rejection Note if Rejected -->
                <div
                    v-if="vendor.status === 'rejected' && vendor.rejection_reason"
                    class="mt-3 rounded-xl border border-rose-500/30 bg-rose-500/10 p-3 text-xs text-rose-800 dark:text-rose-200"
                >
                    <span class="font-bold">Rejection Reason:</span>
                    <span class="ml-1">{{ vendor.rejection_reason }}</span>
                </div>

                <!-- Details Grid -->
                <div class="mt-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-4 text-xs">
                    <!-- NIDA -->
                    <div class="rounded-xl border border-border/50 bg-background/60 p-3 min-w-0">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-muted-foreground block mb-1">
                            National ID (NIDA)
                        </span>
                        <span class="font-mono text-xs font-semibold text-foreground truncate block">
                            {{ vendor.nida_number }}
                        </span>
                    </div>

                    <!-- Contact Phones -->
                    <div class="rounded-xl border border-border/50 bg-background/60 p-3 min-w-0">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-muted-foreground block mb-1">
                            Phone Numbers
                        </span>
                        <div class="space-y-0.5 truncate">
                            <a
                                v-if="vendor.primary_phone"
                                :href="cleanPhoneUrl(vendor.primary_phone)"
                                class="inline-flex items-center gap-1 font-semibold text-[#ff545a] hover:underline"
                            >
                                <Phone class="size-3" />
                                <span>{{ vendor.primary_phone }} (Primary)</span>
                            </a>
                            <span v-else class="text-muted-foreground">—</span>

                            <div v-if="vendor.secondary_phone" class="text-muted-foreground text-[11px]">
                                <a :href="cleanPhoneUrl(vendor.secondary_phone)" class="hover:underline">
                                    {{ vendor.secondary_phone }} (Secondary)
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Social Media Channels -->
                    <div class="rounded-xl border border-border/50 bg-background/60 p-3 min-w-0">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-muted-foreground block mb-1">
                            Social Channels
                        </span>
                        <div class="flex flex-wrap items-center gap-1.5 text-[11px]">
                            <a
                                v-if="vendor.instagram"
                                :href="cleanInstagramUrl(vendor.instagram)"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="inline-flex items-center gap-1 rounded bg-purple-500/10 px-2 py-0.5 text-purple-700 hover:bg-purple-500/20 dark:text-purple-300"
                            >
                                <Camera class="size-3" />
                                <span class="truncate max-w-[90px]">{{ vendor.instagram }}</span>
                            </a>

                            <a
                                v-if="vendor.whatsapp"
                                :href="cleanWhatsAppUrl(vendor.whatsapp)"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="inline-flex items-center gap-1 rounded bg-emerald-500/10 px-2 py-0.5 text-emerald-700 hover:bg-emerald-500/20 dark:text-emerald-300"
                            >
                                <MessageCircle class="size-3" />
                                <span>WhatsApp</span>
                            </a>

                            <a
                                v-if="vendor.tiktok"
                                :href="cleanTikTokUrl(vendor.tiktok)"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="inline-flex items-center gap-1 rounded bg-cyan-500/10 px-2 py-0.5 text-cyan-700 hover:bg-cyan-500/20 dark:text-cyan-300"
                            >
                                <Video class="size-3" />
                                <span class="truncate max-w-[90px]">{{ vendor.tiktok }}</span>
                            </a>

                            <span
                                v-if="!vendor.instagram && !vendor.whatsapp && !vendor.tiktok && vendor.social_media"
                                class="text-foreground truncate"
                            >
                                {{ vendor.social_media }}
                            </span>
                            <span
                                v-if="!vendor.instagram && !vendor.whatsapp && !vendor.tiktok && !vendor.social_media"
                                class="text-muted-foreground"
                            >
                                —
                            </span>
                        </div>
                    </div>

                    <!-- Business Licence File -->
                    <div class="rounded-xl border border-border/50 bg-background/60 p-3 min-w-0">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-muted-foreground block mb-1">
                            Official Licence
                        </span>
                        <a
                            v-if="vendor.business_licence_url"
                            :href="vendor.business_licence_url"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="inline-flex items-center gap-1.5 font-semibold text-blue-600 hover:text-blue-700 dark:text-blue-400 hover:underline truncate"
                        >
                            <FileText class="size-3.5 shrink-0" />
                            <span class="truncate">View Document</span>
                            <ExternalLink class="size-3 shrink-0" />
                        </a>
                        <span v-else class="text-muted-foreground text-[11px]">
                            Document not accessible
                        </span>
                    </div>
                </div>

                <!-- Services Categories & Description -->
                <div class="mt-3.5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2.5 pt-3 border-t border-border/40 text-xs">
                    <div class="flex flex-wrap items-center gap-1.5">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-muted-foreground mr-1">
                            Services:
                        </span>
                        <template v-if="vendor.categories && vendor.categories.length > 0">
                            <span
                                v-for="cat in vendor.categories"
                                :key="cat"
                                class="inline-flex items-center rounded-md bg-[#ff545a]/10 px-2 py-0.5 text-[11px] font-medium text-[#ff545a]"
                            >
                                {{ cat }}
                            </span>
                        </template>
                        <span
                            v-else-if="vendor.category"
                            class="inline-flex items-center rounded-md bg-[#ff545a]/10 px-2 py-0.5 text-[11px] font-medium text-[#ff545a]"
                        >
                            {{ vendor.category }}
                        </span>
                        <span v-else class="text-muted-foreground">—</span>
                    </div>

                    <div v-if="vendor.description" class="text-muted-foreground text-[11px] max-w-xl truncate">
                        <span class="font-medium text-foreground">Bio:</span> {{ vendor.description }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
