<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { dashboard } from '@/routes';
import {
    Calendar,
    CalendarCheck,
    CalendarPlus,
    Clock,
    MapPin,
    ArrowLeft,
    CheckCircle2,
    DollarSign,
    Sparkles,
    Cake,
    Music,
    Mic,
    Camera,
    Car,
    Palette,
    Mail,
    FileText,
} from '@lucide/vue';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Dashboard',
                href: dashboard(),
            },
            {
                title: 'My Booked Events',
                href: '/events/my-bookings',
            },
        ],
    },
});

interface BookedEvent {
    id: number;
    name: string;
    event_type: string;
    services: string[];
    budget?: number | string | null;
    event_date?: string | null;
    location?: string | null;
    notes?: string | null;
    status: string;
    created_at: string;
}

const props = withDefaults(
    defineProps<{
        bookings?: BookedEvent[];
    }>(),
    {
        bookings: () => [],
    }
);

const serviceMeta: Record<string, { label: string; icon: any }> = {
    cake: { label: 'Cake', icon: Cake },
    music_dj: { label: 'Music & DJ', icon: Music },
    mc: { label: 'MC', icon: Mic },
    photography: { label: 'Photography', icon: Camera },
    transportation: { label: 'Transportation', icon: Car },
    decoration: { label: 'Decoration', icon: Palette },
    card: { label: 'Invitation Card', icon: Mail },
};

const formatCurrency = (amount: number | string | null | undefined) => {
    if (!amount) return 'Not specified (Flexible)';
    const num = Number(amount);
    return isNaN(num) ? String(amount) : `TZS ${num.toLocaleString()}`;
};

const formatDate = (dateStr?: string | null) => {
    if (!dateStr) return 'Date to be confirmed';
    try {
        const d = new Date(dateStr);
        return d.toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
        });
    } catch {
        return dateStr;
    }
};
</script>

<template>
    <Head title="My Booked Events" />

    <div class="max-w-6xl mx-auto space-y-8 p-4 md:p-6">
        <!-- Header -->
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between border-b border-border/60 pb-6">
            <div>
                <div class="flex items-center gap-2">
                    <div class="rounded-lg bg-primary/10 p-2 text-primary">
                        <CalendarCheck class="size-5" />
                    </div>
                    <h1 class="text-2xl font-bold tracking-tight text-foreground">
                        My Booked Events
                    </h1>
                </div>
                <p class="mt-1 text-xs sm:text-sm text-muted-foreground">
                    Review your scheduled celebrations, service packages, and status updates.
                </p>
            </div>

            <div class="flex items-center gap-2.5">
                <Link
                    href="/dashboard"
                    class="inline-flex items-center gap-1.5 rounded-lg border border-border bg-background px-3 py-1.5 text-xs font-medium text-foreground transition hover:bg-muted"
                >
                    <ArrowLeft class="size-3.5" /> Dashboard
                </Link>
                <Link
                    href="/events/book"
                    class="inline-flex items-center gap-1.5 rounded-lg bg-primary px-3.5 py-1.5 text-xs font-semibold text-primary-foreground shadow-sm hover:bg-primary/90 transition-all"
                >
                    <CalendarPlus class="size-3.5" /> Book Another Event
                </Link>
            </div>
        </div>

        <!-- Summary Bar -->
        <div class="grid gap-4 sm:grid-cols-3">
            <div class="rounded-xl border border-border/70 bg-card p-4 shadow-sm">
                <span class="text-xs font-medium uppercase tracking-wider text-muted-foreground">Total Bookings</span>
                <div class="mt-2 text-2xl font-bold text-foreground">{{ props.bookings.length }}</div>
                <span class="text-xs text-muted-foreground">All event requests</span>
            </div>

            <div class="rounded-xl border border-border/70 bg-card p-4 shadow-sm">
                <span class="text-xs font-medium uppercase tracking-wider text-muted-foreground">Status In Review</span>
                <div class="mt-2 text-2xl font-bold text-amber-600 dark:text-amber-400">
                    {{ props.bookings.filter(b => (b.status || '').toLowerCase().includes('pending')).length }}
                </div>
                <span class="text-xs text-muted-foreground">Our team is reviewing</span>
            </div>

            <div class="rounded-xl border border-border/70 bg-card p-4 shadow-sm">
                <span class="text-xs font-medium uppercase tracking-wider text-muted-foreground">Target Budget</span>
                <div class="mt-2 text-xl font-bold text-foreground">
                    TZS {{
                        props.bookings.reduce((sum, b) => sum + (Number(b.budget) || 0), 0).toLocaleString()
                    }}
                </div>
                <span class="text-xs text-muted-foreground">Total planned value</span>
            </div>
        </div>

        <!-- Bookings List -->
        <div class="space-y-4">
            <!-- Empty State -->
            <div
                v-if="props.bookings.length === 0"
                class="rounded-xl border border-border/70 bg-card p-12 text-center shadow-sm"
            >
                <div class="mx-auto rounded-full bg-amber-500/10 p-4 text-amber-600 dark:text-amber-400 w-fit mb-3">
                    <CalendarCheck class="size-8" />
                </div>
                <h3 class="text-base font-semibold text-foreground">No Booked Events Yet</h3>
                <p class="mt-1 text-xs text-muted-foreground max-w-md mx-auto">
                    You have not submitted any event bookings yet. Once you submit a booking request, your event schedule, selected services, and quotes will appear here.
                </p>
                <Link
                    href="/events/book"
                    class="mt-4 inline-flex items-center gap-1.5 rounded-lg bg-primary px-4 py-2 text-xs font-semibold text-primary-foreground shadow-sm hover:bg-primary/90 transition-all"
                >
                    <CalendarPlus class="size-3.5" /> Book Your First Event
                </Link>
            </div>

            <!-- Populated Events Grid -->
            <div v-else class="grid gap-4 md:grid-cols-2">
                <div
                    v-for="booking in props.bookings"
                    :key="booking.id"
                    class="rounded-xl border border-border/70 bg-card p-5 shadow-sm space-y-4 transition-all hover:border-primary/50"
                >
                    <!-- Top header: Name & Status -->
                    <div class="flex items-start justify-between gap-3 border-b border-border/50 pb-3">
                        <div>
                            <span class="inline-block rounded bg-primary/10 px-2 py-0.5 text-[10px] font-semibold text-primary mb-1">
                                {{ booking.event_type }}
                            </span>
                            <h2 class="text-base font-bold text-foreground leading-tight">
                                {{ booking.name }}
                            </h2>
                        </div>
                        <span class="shrink-0 rounded-full bg-amber-500/10 px-2.5 py-1 text-xs font-semibold text-amber-600 dark:text-amber-400 flex items-center gap-1">
                            <Clock class="size-3" />
                            {{ booking.status || 'Pending Review' }}
                        </span>
                    </div>

                    <!-- Meta: Date, Location, Budget -->
                    <div class="grid grid-cols-2 gap-3 text-xs">
                        <div class="flex items-center gap-2 text-muted-foreground">
                            <Calendar class="size-3.5 text-primary shrink-0" />
                            <span>{{ formatDate(booking.event_date) }}</span>
                        </div>
                        <div class="flex items-center gap-2 text-muted-foreground">
                            <MapPin class="size-3.5 text-primary shrink-0" />
                            <span class="truncate">{{ booking.location || 'Location TBA' }}</span>
                        </div>
                        <div class="col-span-2 flex items-center gap-2 text-foreground font-medium">
                            <span class="text-muted-foreground">Proposed Budget:</span>
                            <span class="font-bold text-emerald-600 dark:text-emerald-400">
                                {{ formatCurrency(booking.budget) }}
                            </span>
                        </div>
                    </div>

                    <!-- Services Included -->
                    <div class="space-y-1.5 border-t border-border/40 pt-3">
                        <span class="text-[11px] font-semibold uppercase tracking-wider text-muted-foreground">
                            Services Requested ({{ booking.services?.length || 0 }}):
                        </span>
                        <div class="flex flex-wrap gap-1.5 pt-1">
                            <span
                                v-for="serviceId in booking.services"
                                :key="serviceId"
                                class="inline-flex items-center gap-1 rounded-md border border-border bg-muted/40 px-2 py-1 text-[11px] font-medium text-foreground"
                            >
                                <component
                                    :is="serviceMeta[serviceId]?.icon || Sparkles"
                                    class="size-3 text-primary"
                                />
                                <span>{{ serviceMeta[serviceId]?.label || serviceId }}</span>
                            </span>
                        </div>
                    </div>

                    <!-- Notes if any -->
                    <div v-if="booking.notes" class="rounded-lg bg-muted/30 p-2.5 text-xs text-muted-foreground">
                        <span class="font-semibold text-foreground">Client Note: </span>
                        <span>{{ booking.notes }}</span>
                    </div>

                    <!-- Footer timeline message -->
                    <div class="flex items-center justify-between text-[11px] text-muted-foreground pt-1 border-t border-border/40">
                        <span>Submitted: {{ formatDate(booking.created_at) }}</span>
                        <span class="text-emerald-600 dark:text-emerald-400 font-medium flex items-center gap-1">
                            <CheckCircle2 class="size-3" />
                            Request Received
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
