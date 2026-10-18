<script setup lang="ts">
import { ref } from 'vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import { toast } from 'vue-sonner';
import { dashboard } from '@/routes';
import InputError from '@/components/InputError.vue';
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
        name: 'Cake',
        category: 'Bakery & Sweets',
        description: 'Bespoke celebration & wedding cakes, designer tiered cakes, and dessert station styling.',
        icon: Cake,
    },
    {
        id: 'music_dj',
        name: 'Music and DJ',
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
        name: 'Photography',
        category: 'Media & Coverage',
        description: 'High-definition photo sessions, cinematic 4K video, drone aerial captures, and edited albums.',
        icon: Camera,
    },
    {
        id: 'transportation',
        name: 'Transportation',
        category: 'Travel & Logistics',
        description: 'Bridal convoy luxury vehicles, VIP guest transfers, executive shuttles, and chauffeur coordination.',
        icon: Car,
    },
    {
        id: 'decoration',
        name: 'Decoration',
        category: 'Ambiance & Styling',
        description: 'Bespoke venue transformation, floral centerpieces, backdrop styling, and entrance decor.',
        icon: Palette,
    },
    {
        id: 'card',
        name: 'Card (Invitations & Stationery)',
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

const form = useForm({
    name: '',
    event_type: '',
    services: [] as string[],
    budget: '' as string | number,
    event_date: '',
    location: '',
    notes: '',
});

const toggleService = (serviceId: string) => {
    if (form.services.includes(serviceId)) {
        form.services = form.services.filter((id) => id !== serviceId);
    } else {
        form.services.push(serviceId);
    }
};

const showSuccessModal = ref(false);

const submit = () => {
    form.post('/events/book', {
        preserveScroll: true,
        onSuccess: () => {
            showSuccessModal.value = true;
            toast.success('Thanks! The team will reach you soon.');
        },
    });
};
</script>

<template>
    <Head title="Book an Event" />

    <div class="max-w-4xl mx-auto space-y-8 p-4 md:p-6">
        <!-- Header Banner -->
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between border-b border-border/60 pb-6">
            <div>
                <div class="flex items-center gap-2">
                    <div class="rounded-lg bg-primary/10 p-2 text-primary">
                        <CalendarPlus class="size-5" />
                    </div>
                    <h1 class="text-2xl font-bold tracking-tight text-foreground">
                        Book an Event
                    </h1>
                </div>
                <p class="mt-1 text-xs sm:text-sm text-muted-foreground">
                    Tell us about your event and select the services you need. Our team will review your details and coordinate the execution.
                </p>
            </div>

            <Link
                href="/dashboard"
                class="inline-flex items-center gap-1.5 self-start rounded-lg border border-border bg-background px-3 py-1.5 text-xs font-medium text-foreground transition hover:bg-muted"
            >
                <ArrowLeft class="size-3.5" /> Back to Dashboard
            </Link>
        </div>

        <!-- Booking Form -->
        <form @submit.prevent="submit" class="space-y-8">
            <!-- 1. Event Information -->
            <div class="rounded-xl border border-border/70 bg-card p-6 shadow-sm space-y-6">
                <div>
                    <h2 class="text-base font-semibold text-foreground">1. Event Overview</h2>
                    <p class="text-xs text-muted-foreground mt-0.5">Provide basic information about your upcoming occasion.</p>
                </div>

                <div class="grid gap-6 sm:grid-cols-2">
                    <!-- Event Name -->
                    <div class="space-y-2 sm:col-span-2">
                        <label for="event-name" class="text-xs font-semibold uppercase tracking-wider text-foreground">
                            Event Name <span class="text-red-500">*</span>
                        </label>
                        <input
                            id="event-name"
                            v-model="form.name"
                            type="text"
                            required
                            placeholder="e.g. John & Sarah's Wedding Reception, Tech Innovators Summit 2026"
                            class="w-full rounded-lg border border-border bg-background px-3.5 py-2.5 text-sm text-foreground placeholder:text-muted-foreground/60 focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary"
                            :class="{ 'border-red-500': form.errors.name }"
                        />
                        <InputError :message="form.errors.name" />
                    </div>

                    <!-- Kind of Event -->
                    <div class="space-y-2 sm:col-span-2">
                        <label for="event-type" class="text-xs font-semibold uppercase tracking-wider text-foreground">
                            Kind of Event <span class="text-red-500">*</span>
                        </label>
                        <select
                            id="event-type"
                            v-model="form.event_type"
                            required
                            class="w-full rounded-lg border border-border bg-background px-3.5 py-2.5 text-sm text-foreground focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary"
                            :class="{ 'border-red-500': form.errors.event_type }"
                        >
                            <option value="" disabled selected>Select the kind of event...</option>
                            <option v-for="type in eventTypes" :key="type" :value="type">
                                {{ type }}
                            </option>
                        </select>
                        <InputError :message="form.errors.event_type" />
                    </div>

                    <!-- Tentative Date (Optional) -->
                    <div class="space-y-2">
                        <label for="event-date" class="flex items-center gap-1.5 text-xs font-semibold uppercase tracking-wider text-foreground">
                            <Calendar class="size-3.5 text-muted-foreground" />
                            <span>Tentative Date</span>
                            <span class="text-[10px] font-normal lowercase text-muted-foreground">(optional)</span>
                        </label>
                        <input
                            id="event-date"
                            v-model="form.event_date"
                            type="date"
                            class="w-full rounded-lg border border-border bg-background px-3.5 py-2 text-sm text-foreground focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary"
                        />
                    </div>

                    <!-- Tentative Venue / Location (Optional) -->
                    <div class="space-y-2">
                        <label for="event-location" class="flex items-center gap-1.5 text-xs font-semibold uppercase tracking-wider text-foreground">
                            <MapPin class="size-3.5 text-muted-foreground" />
                            <span>Location / City</span>
                            <span class="text-[10px] font-normal lowercase text-muted-foreground">(optional)</span>
                        </label>
                        <input
                            id="event-location"
                            v-model="form.location"
                            type="text"
                            placeholder="e.g. Dar es Salaam, Arusha, Zanzibar"
                            class="w-full rounded-lg border border-border bg-background px-3.5 py-2 text-sm text-foreground placeholder:text-muted-foreground/60 focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary"
                        />
                    </div>
                </div>
            </div>

            <!-- 2. Services Needed -->
            <div class="rounded-xl border border-border/70 bg-card p-6 shadow-sm space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-1 border-b border-border/50 pb-4">
                    <div>
                        <h2 class="text-base font-semibold text-foreground">
                            2. Select Services Needed <span class="text-red-500">*</span>
                        </h2>
                        <p class="text-xs text-muted-foreground mt-0.5">
                            Pick all the services you want HEY Events to provide for your event.
                        </p>
                    </div>

                    <span class="self-start rounded-full bg-primary/10 px-2.5 py-1 text-xs font-semibold text-primary">
                        {{ form.services.length }} Selected
                    </span>
                </div>

                <InputError :message="form.errors.services" />

                <!-- Services Grid -->
                <div class="grid gap-3 sm:grid-cols-2">
                    <div
                        v-for="service in serviceOptions"
                        :key="service.id"
                        @click="toggleService(service.id)"
                        class="cursor-pointer rounded-xl border p-4 transition-all relative select-none"
                        :class="
                            form.services.includes(service.id)
                                ? 'border-primary bg-primary/5 ring-1 ring-primary shadow-sm'
                                : 'border-border/70 bg-card hover:border-border hover:bg-muted/30'
                        "
                    >
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex items-start gap-3">
                                <div
                                    class="rounded-lg p-2.5 shrink-0 transition-colors"
                                    :class="
                                        form.services.includes(service.id)
                                            ? 'bg-primary text-primary-foreground'
                                            : 'bg-muted text-foreground'
                                    "
                                >
                                    <component :is="service.icon" class="size-4" />
                                </div>
                                <div class="space-y-1">
                                    <h3 class="text-sm font-semibold text-foreground leading-tight">{{ service.name }}</h3>
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

            <!-- 3. Target Budget (Optional) -->
            <div class="rounded-xl border border-border/70 bg-card p-6 shadow-sm space-y-4">
                <div class="flex items-start justify-between">
                    <div>
                        <div class="flex items-center gap-2">
                            <h2 class="text-base font-semibold text-foreground">3. Estimated Budget</h2>
                            <span class="rounded bg-muted px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wider text-muted-foreground">
                                Optional
                            </span>
                        </div>
                        <p class="text-xs text-muted-foreground mt-0.5">
                            If you have an allocated budget, specify it in <strong>TZS (Tanzanian Shillings)</strong>. If left blank, our team will calculate and propose standard packages.
                        </p>
                    </div>
                </div>

                <div class="max-w-md space-y-2 pt-1">
                    <label for="event-budget" class="text-xs font-semibold uppercase tracking-wider text-foreground">
                        Your Proposed Budget (TZS)
                    </label>
                    <div class="relative">
                        <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-xs font-bold text-muted-foreground">
                            TZS
                        </span>
                        <input
                            id="event-budget"
                            v-model="form.budget"
                            type="number"
                            min="0"
                            step="50000"
                            placeholder="e.g. 2,500,000"
                            class="w-full rounded-lg border border-border bg-background py-2.5 pl-14 pr-4 text-sm font-semibold text-foreground placeholder:font-normal placeholder:text-muted-foreground/60 focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary"
                            :class="{ 'border-red-500': form.errors.budget }"
                        />
                    </div>
                    <InputError :message="form.errors.budget" />
                    <p class="text-[11px] text-muted-foreground flex items-center gap-1">
                        <HelpCircle class="size-3 text-muted-foreground" />
                        <span>Optional — You can discuss or adjust your budget during consultation.</span>
                    </p>
                </div>
            </div>

            <!-- 4. Additional Notes / Special Requests (Optional) -->
            <div class="rounded-xl border border-border/70 bg-card p-6 shadow-sm space-y-3">
                <div class="flex items-center justify-between">
                    <h2 class="text-base font-semibold text-foreground">4. Additional Notes or Requests</h2>
                    <span class="rounded bg-muted px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wider text-muted-foreground">
                        Optional
                    </span>
                </div>
                <textarea
                    v-model="form.notes"
                    rows="3"
                    placeholder="Provide any specific preferences, estimated guest count, theme, or questions..."
                    class="w-full rounded-lg border border-border bg-background px-3.5 py-2.5 text-sm text-foreground placeholder:text-muted-foreground/60 focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary"
                ></textarea>
            </div>

            <!-- Submission Action -->
            <div class="flex flex-col sm:flex-row items-center justify-end gap-3 pt-2 pb-6">
                <Link
                    href="/dashboard"
                    class="w-full sm:w-auto text-center rounded-lg border border-border bg-background px-4 py-2.5 text-xs font-semibold text-foreground hover:bg-muted transition-colors"
                >
                    Cancel
                </Link>

                <button
                    type="submit"
                    :disabled="form.processing || form.services.length === 0"
                    class="w-full sm:w-auto inline-flex items-center justify-center gap-2 rounded-lg bg-primary px-6 py-2.5 text-xs font-semibold text-primary-foreground shadow-md transition-all hover:bg-primary/90 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    <Send class="size-3.5" v-if="!form.processing" />
                    <span>{{ form.processing ? 'Submitting Request...' : 'Submit Event Booking Request' }}</span>
                </button>
            </div>
        </form>

        <!-- Success Pop Notification Modal -->
        <Teleport to="body">
            <div
                v-if="showSuccessModal"
                class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4 animate-in fade-in"
            >
                <div class="w-full max-w-md rounded-2xl border border-border bg-card p-6 shadow-2xl space-y-5 animate-in zoom-in-95">
                    <div class="flex items-start justify-between">
                        <div class="flex items-center gap-3">
                            <div class="rounded-full bg-emerald-500/15 p-3 text-emerald-600 dark:text-emerald-400">
                                <CheckCircle2 class="size-6 stroke-[2.5]" />
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-foreground">Booking Request Submitted</h3>
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

                    <!-- Prominent Pop Message as requested -->
                    <div class="rounded-xl border border-emerald-500/30 bg-emerald-500/10 p-4 text-emerald-900 dark:text-emerald-200 space-y-1.5">
                        <p class="text-sm font-bold flex items-center gap-1.5">
                            <CheckCircle2 class="size-4 shrink-0 text-emerald-600 dark:text-emerald-400" />
                            <span>Thanks! The team will reach you soon.</span>
                        </p>
                        <p class="text-xs text-muted-foreground leading-relaxed">
                            Our team has received your booking details for <strong class="text-foreground font-semibold">"{{ form.name }}"</strong> and will coordinate with you shortly.
                        </p>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-2.5 pt-2">
                        <Link
                            href="/dashboard"
                            class="flex-1 inline-flex items-center justify-center rounded-lg bg-primary px-4 py-2.5 text-xs font-semibold text-primary-foreground shadow hover:bg-primary/90 transition-all"
                        >
                            Go to Dashboard
                        </Link>
                        <Link
                            href="/events/my-bookings"
                            class="flex-1 inline-flex items-center justify-center rounded-lg border border-border bg-background px-4 py-2.5 text-xs font-semibold text-foreground hover:bg-muted transition-colors"
                        >
                            View My Booked Events
                        </Link>
                    </div>
                </div>
            </div>
        </Teleport>
    </div>
</template>
