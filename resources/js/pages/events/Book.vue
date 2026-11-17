<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import { toast } from 'vue-sonner';
import { dashboard } from '@/routes';
import InputError from '@/components/InputError.vue';
import { useLanguage } from '@/composables/useLanguage';
import {
    CalendarPlus,
    Cake,
    Music,
    Mic,
    Camera,
    Car,
    Palette,
    Mail,
    Check,
    CheckCircle2,
    Calendar,
    MapPin,
    ArrowLeft,
    Send,
    HelpCircle,
    X,
    ShoppingBag,
    Plus,
    Minus,
    Trash2,
} from '@lucide/vue';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Dashboard',
                href: dashboard(),
            },
            {
                title: 'Book Event',
                href: '/events/book',
            },
        ],
    },
});

const { t } = useLanguage();

interface ServiceOption {
    id: string;
    name: string;
    category: string;
    description: string;
    icon: any;
}

const serviceOptions: ServiceOption[] = [
    {
        id: 'cake',
        name: 'Cake & Catering',
        category: 'Bakery & Sweets',
        description: 'Bespoke celebration & wedding cakes, designer tiered cakes, and dessert station styling.',
        icon: Cake,
    },
    {
        id: 'music_dj',
        name: 'Music, Sound & DJ',
        category: 'Audio & Entertainment',
        description: 'Concert-grade sound systems, live DJ sets, professional mixing, and acoustic management.',
        icon: Music,
    },
    {
        id: 'mc',
        name: 'MC (Master of Ceremonies)',
        category: 'Hosting & Protocol',
        description: 'Professional event hosts, bilingual MCs, timeline direction, and audience engagement.',
        icon: Mic,
    },
    {
        id: 'photography',
        name: 'Photography & Cinema',
        category: 'Media & Coverage',
        description: 'High-definition photo sessions, cinematic 4K video, drone aerial captures, and edited albums.',
        icon: Camera,
    },
    {
        id: 'transportation',
        name: 'VIP Transportation',
        category: 'Travel & Logistics',
        description: 'Bridal convoy luxury vehicles, VIP guest transfers, executive shuttles, and chauffeur coordination.',
        icon: Car,
    },
    {
        id: 'decoration',
        name: 'Decoration & Styling',
        category: 'Ambiance & Styling',
        description: 'Bespoke venue transformation, floral centerpieces, backdrop styling, and entrance decor.',
        icon: Palette,
    },
    {
        id: 'card',
        name: 'Invitations & Protocol',
        category: 'Invitations & Print',
        description: 'Custom print invitations, digital RSVP cards, escort cards, ceremony booklets, and signage.',
        icon: Mail,
    },
];

const eventTypes = [
    'Wedding Celebration',
    'Corporate Gala & Banquet',
    'Birthday & Anniversary Party',
    'Music Concert & Festival',
    'Business Summit & Conference',
    'Private Dinner & Reception',
    'Community & Cultural Festival',
    'Product Launch & Exhibition',
    'Other Special Event',
];

const props = defineProps<{
    preselectedItem?: any;
    preselectedItems?: any[];
}>();

interface PackageItemState {
    id: number;
    title: string;
    category: string;
    client_price: number;
    pricing_unit: string;
    quantity: number;
    image_url?: string;
    location?: string;
}

const packageItems = ref<PackageItemState[]>([]);

const form = useForm({
    name: '',
    client_phone: '',
    event_type: 'Wedding Celebration',
    catalog_item_id: props.preselectedItem?.id ?? null,
    item_quantity: 1,
    order_items: [] as any[],
    services: [] as string[],
    budget: '' as string | number,
    event_date: '',
    location: '',
    venue_name: '',
    notes: '',
});

function syncPackageToForm() {
    if (packageItems.value.length === 0) {
        form.order_items = [];
        return;
    }

    const total = packageItems.value.reduce(
        (sum, item) => sum + item.client_price * item.quantity,
        0,
    );
    form.budget = total;
    form.order_items = packageItems.value.map((item) => ({
        id: item.id,
        quantity: item.quantity,
    }));

    if (packageItems.value.length === 1) {
        form.catalog_item_id = packageItems.value[0].id;
        form.item_quantity = packageItems.value[0].quantity;
        if (!form.location) {
            form.location = packageItems.value[0].location || '';
        }
    } else {
        form.catalog_item_id = null;
    }

    const titles = packageItems.value.map((i) => i.title);
    for (const title of titles) {
        if (!form.services.includes(title)) {
            form.services.push(title);
        }
    }
}

onMounted(() => {
    let basketStored: any[] = [];
    try {
        const raw = sessionStorage.getItem('heyevent_basket');
        if (raw) basketStored = JSON.parse(raw);
    } catch (e) {}

    const sourceItems =
        props.preselectedItems && props.preselectedItems.length > 0
            ? props.preselectedItems
            : props.preselectedItem
              ? [props.preselectedItem]
              : [];

    if (sourceItems.length > 0) {
        packageItems.value = sourceItems.map((item: any) => {
            const foundInBasket = basketStored.find((b: any) => b.id === item.id);
            return {
                id: item.id,
                title: item.title,
                category: item.category,
                client_price: Number(item.client_price || 0),
                pricing_unit: item.pricing_unit || 'unit',
                quantity: foundInBasket?.quantity || 1,
                image_url: item.image_url || item.image,
                location: item.location,
            };
        });
        syncPackageToForm();
    }
});

function updateItemQty(itemId: number, delta: number) {
    const item = packageItems.value.find((i) => i.id === itemId);
    if (!item) return;
    item.quantity = Math.max(1, item.quantity + delta);
    syncPackageToForm();
}

function removeItemFromPackage(itemId: number) {
    packageItems.value = packageItems.value.filter((i) => i.id !== itemId);
    syncPackageToForm();
}

const toggleService = (serviceId: string) => {
    if (form.services.includes(serviceId)) {
        form.services = form.services.filter((id) => id !== serviceId);
    } else {
        form.services.push(serviceId);
    }
};

const showSuccessModal = ref(false);

const submit = () => {
    syncPackageToForm();
    form.post('/events/book', {
        preserveScroll: true,
        onSuccess: () => {
            sessionStorage.removeItem('heyevent_basket');
            showSuccessModal.value = true;
            toast.success(t('bookingForm.successTitle'));
        },
    });
};
</script>

<template>
    <Head :title="t('bookingForm.title') + ' — HEY Events'" />

    <div class="w-full space-y-6">
        <!-- Header Banner -->
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between border-b border-border/60 pb-6">
            <div>
                <div class="flex items-center gap-2">
                    <div class="rounded-lg bg-primary/10 p-2 text-primary">
                        <CalendarPlus class="size-5" />
                    </div>
                    <h1 class="text-2xl font-bold tracking-tight text-foreground">
                        {{ t('bookingForm.title') }}
                    </h1>
                </div>
                <p class="mt-1 text-xs sm:text-sm text-muted-foreground">
                    {{ t('bookingForm.subtitle') }}
                </p>
            </div>

            <Link
                href="/dashboard"
                class="inline-flex items-center gap-1.5 self-start rounded-xl border border-border bg-background px-3.5 py-2 text-xs font-semibold text-foreground transition hover:bg-muted"
            >
                <ArrowLeft class="size-3.5" />
                <span>{{ t('bookingForm.backToDashboard') }}</span>
            </Link>
        </div>

        <!-- Preselected Marketplace Items Package Review Card -->
        <div
            v-if="packageItems.length > 0"
            class="rounded-3xl border border-red-500/30 bg-red-500/5 p-5 shadow-xs space-y-4"
        >
            <div class="flex items-center justify-between border-b border-red-500/20 pb-3">
                <div class="flex items-center gap-2">
                    <div class="rounded-xl bg-[#ff545a] p-1.5 text-white">
                        <ShoppingBag class="size-4" />
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-foreground">
                            {{ t('bookingForm.packageSummaryTitle') }}
                        </h3>
                        <p class="text-[11px] text-muted-foreground">
                            {{ t('bookingForm.guaranteedFulfillment') }}
                        </p>
                    </div>
                </div>

                <div class="text-right">
                    <span class="text-[10px] font-bold uppercase text-muted-foreground block">
                        {{ t('bookingForm.totalEstimate') }}
                    </span>
                    <span class="text-base font-black text-[#ff545a]">
                        TZS {{ form.budget ? Number(form.budget).toLocaleString() : '0' }}
                    </span>
                </div>
            </div>

            <!-- Itemized List -->
            <div class="grid gap-3 sm:grid-cols-2">
                <div
                    v-for="pItem in packageItems"
                    :key="pItem.id"
                    class="flex items-center justify-between rounded-2xl border border-border/70 bg-card p-3 shadow-2xs"
                >
                    <div class="flex items-center gap-3 min-w-0">
                        <img
                            :src="pItem.image_url || '/images/card-resort.jpg'"
                            :alt="pItem.title"
                            class="size-14 rounded-xl object-cover shrink-0 border border-border/50"
                        />
                        <div class="min-w-0">
                            <span class="text-[9px] font-bold text-[#ff545a] uppercase tracking-wider block">
                                {{ pItem.category }}
                            </span>
                            <h4 class="text-xs font-bold text-foreground truncate">
                                {{ pItem.title }}
                            </h4>
                            <span class="text-[11px] text-muted-foreground font-semibold">
                                TZS {{ pItem.client_price.toLocaleString() }} / {{ pItem.pricing_unit }}
                            </span>
                        </div>
                    </div>

                    <div class="flex items-center gap-2 shrink-0">
                        <div class="flex items-center rounded-xl border border-border bg-background p-0.5">
                            <button
                                type="button"
                                @click="updateItemQty(pItem.id, -1)"
                                class="p-1 text-muted-foreground hover:text-foreground"
                            >
                                <Minus class="size-3" />
                            </button>
                            <span class="px-2 text-xs font-bold text-foreground">{{ pItem.quantity }}</span>
                            <button
                                type="button"
                                @click="updateItemQty(pItem.id, 1)"
                                class="p-1 text-muted-foreground hover:text-foreground"
                            >
                                <Plus class="size-3" />
                            </button>
                        </div>
                        <button
                            type="button"
                            @click="removeItemFromPackage(pItem.id)"
                            class="p-1.5 text-muted-foreground hover:text-red-500 rounded-lg transition"
                            title="Remove"
                        >
                            <Trash2 class="size-3.5" />
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Booking Form -->
        <form @submit.prevent="submit" class="space-y-6">
            <!-- 1. Event Information -->
            <div class="rounded-3xl border border-border/70 bg-card p-6 shadow-xs space-y-5">
                <div>
                    <h2 class="text-base font-semibold text-foreground">
                        {{ t('bookingForm.eventOverview') }}
                    </h2>
                    <p class="text-xs text-muted-foreground mt-0.5">
                        {{ t('bookingForm.eventOverviewDesc') }}
                    </p>
                </div>

                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-12">
                    <!-- Event Name -->
                    <div class="space-y-1.5 sm:col-span-2 lg:col-span-4">
                        <label for="event-name" class="text-xs font-semibold uppercase tracking-wider text-foreground">
                            {{ t('bookingForm.eventName') }} <span class="text-red-500">*</span>
                        </label>
                        <input
                            id="event-name"
                            v-model="form.name"
                            type="text"
                            required
                            :placeholder="t('bookingForm.eventNamePlaceholder')"
                            class="w-full rounded-xl border border-border bg-background px-3.5 py-2.5 text-sm text-foreground placeholder:text-muted-foreground/60 focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary"
                            :class="{ 'border-red-500': form.errors.name }"
                        />
                        <InputError :message="form.errors.name" />
                    </div>

                    <!-- Client Phone / WhatsApp -->
                    <div class="space-y-1.5 sm:col-span-1 lg:col-span-4">
                        <label for="client-phone" class="text-xs font-semibold uppercase tracking-wider text-foreground">
                            {{ t('bookingForm.clientPhone') }} <span class="text-red-500">*</span>
                        </label>
                        <input
                            id="client-phone"
                            v-model="form.client_phone"
                            type="tel"
                            required
                            placeholder="e.g. 0712 345 678"
                            class="w-full rounded-xl border border-border bg-background px-3.5 py-2.5 text-sm text-foreground placeholder:text-muted-foreground/60 focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary"
                            :class="{ 'border-red-500': form.errors.client_phone }"
                        />
                        <InputError :message="form.errors.client_phone" />
                    </div>

                    <!-- Kind of Event -->
                    <div class="space-y-1.5 sm:col-span-1 lg:col-span-4">
                        <label for="event-type" class="text-xs font-semibold uppercase tracking-wider text-foreground">
                            {{ t('bookingForm.eventType') }} <span class="text-red-500">*</span>
                        </label>
                        <select
                            id="event-type"
                            v-model="form.event_type"
                            required
                            class="w-full rounded-xl border border-border bg-background px-3.5 py-2.5 text-sm text-foreground focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary"
                            :class="{ 'border-red-500': form.errors.event_type }"
                        >
                            <option value="" disabled>{{ t('bookingForm.selectEventType') }}</option>
                            <option v-for="type in eventTypes" :key="type" :value="type">
                                {{ type }}
                            </option>
                        </select>
                        <InputError :message="form.errors.event_type" />
                    </div>

                    <!-- Tentative Date -->
                    <div class="space-y-1.5 sm:col-span-1 lg:col-span-4">
                        <label for="event-date" class="flex items-center gap-1.5 text-xs font-semibold uppercase tracking-wider text-foreground">
                            <Calendar class="size-3.5 text-muted-foreground" />
                            <span>{{ t('bookingForm.eventDate') }}</span>
                        </label>
                        <input
                            id="event-date"
                            v-model="form.event_date"
                            type="date"
                            class="w-full rounded-xl border border-border bg-background px-3.5 py-2 text-sm text-foreground focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary"
                        />
                    </div>

                    <!-- City / Region -->
                    <div class="space-y-1.5 sm:col-span-1 lg:col-span-4">
                        <label for="event-location" class="flex items-center gap-1.5 text-xs font-semibold uppercase tracking-wider text-foreground">
                            <MapPin class="size-3.5 text-muted-foreground" />
                            <span>{{ t('bookingForm.eventCity') }}</span>
                        </label>
                        <input
                            id="event-location"
                            v-model="form.location"
                            type="text"
                            placeholder="e.g. Dar es Salaam, Arusha, Mwanza"
                            class="w-full rounded-xl border border-border bg-background px-3.5 py-2 text-sm text-foreground placeholder:text-muted-foreground/60 focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary"
                        />
                    </div>

                    <!-- Venue / Hall / Beach Name -->
                    <div class="space-y-1.5 sm:col-span-2 lg:col-span-4">
                        <label for="event-venue" class="flex items-center gap-1.5 text-xs font-semibold uppercase tracking-wider text-foreground">
                            <MapPin class="size-3.5 text-muted-foreground" />
                            <span>{{ t('bookingForm.venueName') }}</span>
                        </label>
                        <input
                            id="event-venue"
                            v-model="form.venue_name"
                            type="text"
                            :placeholder="t('bookingForm.venuePlaceholder')"
                            class="w-full rounded-xl border border-border bg-background px-3.5 py-2 text-sm text-foreground placeholder:text-muted-foreground/60 focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary"
                        />
                    </div>
                </div>
            </div>

            <!-- 2. Services Needed -->
            <div class="rounded-3xl border border-border/70 bg-card p-6 shadow-xs space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-1 border-b border-border/50 pb-4">
                    <div>
                        <h2 class="text-base font-semibold text-foreground">
                            {{ t('bookingForm.servicesSection') }}
                        </h2>
                        <p class="text-xs text-muted-foreground mt-0.5">
                            {{ t('bookingForm.servicesSectionDesc') }}
                        </p>
                    </div>

                    <span class="self-start rounded-full bg-primary/10 px-3 py-1 text-xs font-bold text-primary">
                        {{ form.services.length }} Selected
                    </span>
                </div>

                <InputError :message="form.errors.services" />

                <!-- Services Grid -->
                <div class="grid gap-3.5 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                    <div
                        v-for="service in serviceOptions"
                        :key="service.id"
                        @click="toggleService(service.id)"
                        class="cursor-pointer rounded-2xl border p-4 transition-all relative select-none flex flex-col justify-between"
                        :class="
                            form.services.includes(service.id)
                                ? 'border-primary bg-primary/5 ring-1 ring-primary shadow-xs'
                                : 'border-border/70 bg-background hover:border-border hover:bg-muted/30'
                        "
                    >
                        <div>
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="rounded-xl p-2.5 shrink-0 transition-colors"
                                        :class="
                                            form.services.includes(service.id)
                                                ? 'bg-primary text-primary-foreground'
                                                : 'bg-muted text-foreground'
                                        "
                                    >
                                        <component :is="service.icon" class="size-4" />
                                    </div>
                                    <div class="space-y-0.5 min-w-0">
                                        <h3 class="text-sm font-semibold text-foreground leading-tight truncate">
                                            {{ service.name }}
                                        </h3>
                                        <span class="inline-block rounded bg-muted/60 px-1.5 py-0.5 text-[10px] font-medium text-muted-foreground">
                                            {{ service.category }}
                                        </span>
                                    </div>
                                </div>

                                <div
                                    class="flex size-5 shrink-0 items-center justify-center rounded-md border transition-colors mt-0.5"
                                    :class="
                                        form.services.includes(service.id)
                                            ? 'bg-primary border-primary text-primary-foreground'
                                            : 'border-border bg-background'
                                    "
                                >
                                    <Check v-if="form.services.includes(service.id)" class="size-3 stroke-[3]" />
                                </div>
                            </div>

                            <p class="mt-2.5 text-xs text-muted-foreground line-clamp-2">
                                {{ service.description }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 3 & 4. Budget & Notes -->
            <div class="grid gap-6 lg:grid-cols-2">
                <!-- 3. Target Budget -->
                <div class="rounded-3xl border border-border/70 bg-card p-6 shadow-xs space-y-4 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between">
                            <h2 class="text-base font-semibold text-foreground">
                                {{ t('bookingForm.budget') }}
                            </h2>
                            <span class="rounded bg-muted px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wider text-muted-foreground">
                                TZS
                            </span>
                        </div>
                        <p class="text-xs text-muted-foreground mt-0.5">
                            Calculated from selected package items or customize your proposed budget.
                        </p>
                    </div>

                    <div class="space-y-2">
                        <div class="relative">
                            <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-xs font-bold text-muted-foreground">
                                TZS
                            </span>
                            <input
                                id="event-budget"
                                v-model="form.budget"
                                type="number"
                                min="0"
                                step="10000"
                                placeholder="e.g. 2,500,000"
                                class="w-full rounded-xl border border-border bg-background py-2.5 pl-14 pr-4 text-sm font-semibold text-foreground placeholder:font-normal placeholder:text-muted-foreground/60 focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary"
                                :class="{ 'border-red-500': form.errors.budget }"
                            />
                        </div>
                        <InputError :message="form.errors.budget" />
                        <p class="text-[11px] text-muted-foreground flex items-center gap-1">
                            <HelpCircle class="size-3 text-muted-foreground shrink-0" />
                            <span>HEY Events holds payments securely until delivery is inspected on-site.</span>
                        </p>
                    </div>
                </div>

                <!-- 4. Notes & Delivery Instructions -->
                <div class="rounded-3xl border border-border/70 bg-card p-6 shadow-xs space-y-4 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between">
                            <h2 class="text-base font-semibold text-foreground">
                                {{ t('bookingForm.notesSection') }}
                            </h2>
                            <span class="rounded bg-muted px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wider text-muted-foreground">
                                Optional
                            </span>
                        </div>
                        <p class="text-xs text-muted-foreground mt-0.5">
                            {{ t('bookingForm.notesSectionDesc') }}
                        </p>
                    </div>

                    <div class="space-y-1">
                        <textarea
                            v-model="form.notes"
                            rows="3"
                            :placeholder="t('bookingForm.notesPlaceholder')"
                            class="w-full rounded-xl border border-border bg-background px-3.5 py-2.5 text-sm text-foreground placeholder:text-muted-foreground/60 focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary"
                        ></textarea>
                    </div>
                </div>
            </div>

            <!-- Submission Action Bar -->
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4 rounded-3xl border border-border/70 bg-card p-5 shadow-xs">
                <div class="text-xs text-muted-foreground">
                    <span v-if="form.services.length === 0 && packageItems.length === 0" class="text-amber-600 dark:text-amber-400 font-medium">
                        Please select at least 1 service or gear item above to proceed.
                    </span>
                    <span v-else class="text-foreground">
                        Ready to reserve: <strong>{{ packageItems.length > 0 ? packageItems.length + ' gear items' : form.services.length + ' services' }}</strong> for <strong>{{ form.name || 'your event' }}</strong>.
                    </span>
                </div>

                <div class="flex items-center gap-3 w-full sm:w-auto">
                    <Link
                        href="/explore"
                        class="w-full sm:w-auto text-center rounded-xl border border-border bg-background px-4 py-2.5 text-xs font-semibold text-foreground hover:bg-muted transition-colors"
                    >
                        Back to Explore
                    </Link>

                    <button
                        type="submit"
                        :disabled="form.processing || (form.services.length === 0 && packageItems.length === 0)"
                        class="w-full sm:w-auto inline-flex items-center justify-center gap-2 rounded-xl bg-primary px-6 py-2.5 text-xs font-extrabold text-primary-foreground shadow-md transition-all hover:bg-primary/90 disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        <Send class="size-3.5" v-if="!form.processing" />
                        <span>{{ form.processing ? t('bookingForm.submitting') : t('bookingForm.submitButton') }}</span>
                    </button>
                </div>
            </div>
        </form>

        <!-- Success Pop Notification Modal -->
        <Teleport to="body">
            <div
                v-if="showSuccessModal"
                class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4 animate-in fade-in"
            >
                <div class="w-full max-w-md rounded-3xl border border-border bg-card p-6 shadow-2xl space-y-5 animate-in zoom-in-95">
                    <div class="flex items-start justify-between">
                        <div class="flex items-center gap-3">
                            <div class="rounded-2xl bg-emerald-500/15 p-3 text-emerald-600 dark:text-emerald-400">
                                <CheckCircle2 class="size-6 stroke-[2.5]" />
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-foreground">
                                    {{ t('bookingForm.successTitle') }}
                                </h3>
                                <p class="text-xs text-muted-foreground">Confirmation Received</p>
                            </div>
                        </div>

                        <button
                            type="button"
                            @click="showSuccessModal = false"
                            class="rounded-lg p-1 text-muted-foreground hover:bg-muted transition-colors"
                        >
                            <X class="size-4" />
                        </button>
                    </div>

                    <div class="rounded-2xl border border-emerald-500/30 bg-emerald-500/10 p-4 text-emerald-900 dark:text-emerald-200 space-y-1.5">
                        <p class="text-xs text-foreground/90 leading-relaxed">
                            {{ t('bookingForm.successMessage') }}
                        </p>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-2.5 pt-2">
                        <Link
                            href="/dashboard"
                            class="flex-1 inline-flex items-center justify-center rounded-xl bg-primary px-4 py-2.5 text-xs font-bold text-primary-foreground shadow hover:bg-primary/90 transition-all"
                        >
                            Go to Dashboard
                        </Link>
                        <Link
                            href="/events/my-bookings"
                            class="flex-1 inline-flex items-center justify-center rounded-xl border border-border bg-background px-4 py-2.5 text-xs font-bold text-foreground hover:bg-muted transition-colors"
                        >
                            {{ t('bookingForm.viewMyBookings') }}
                        </Link>
                    </div>
                </div>
            </div>
        </Teleport>
    </div>
</template>
