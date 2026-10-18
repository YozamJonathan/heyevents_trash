<script setup lang="ts">
import { computed } from 'vue';
import { usePage, Link } from '@inertiajs/vue3';
import {
    Calendar,
    CalendarCheck,
    Clock,
    CreditCard,
    Heart,
    MapPin,
    CalendarPlus,
    CheckCircle2,
    Sparkles,
    Cake,
    Music,
    Mic,
    Camera,
    Car,
    Palette,
    Mail,
    ArrowRight,
} from '@lucide/vue';

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
        bookedEvents?: BookedEvent[];
    }>(),
    {
        bookedEvents: () => [],
    }
);

const page = usePage();
const flashSuccess = computed(() => (page.props as any).flash?.success || (page.props as any).flash?.toast?.message);

const serviceMeta: Record<string, { label: string; icon: any }> = {
    cake: { label: 'Cake', icon: Cake },
    music_dj: { label: 'Music & DJ', icon: Music },
    mc: { label: 'MC', icon: Mic },
    photography: { label: 'Photography', icon: Camera },
    transportation: { label: 'Transportation', icon: Car },
    decoration: { label: 'Decoration', icon: Palette },
    card: { label: 'Card', icon: Mail },
};

const formatCurrency = (amount: number | string | null | undefined) => {
    if (!amount) return 'Flexible / TBA';
    const num = Number(amount);
    return isNaN(num) ? String(amount) : `TZS ${num.toLocaleString()}`;
};

const formatDate = (dateStr?: string | null) => {
    if (!dateStr) return 'Date TBA';
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

// 2. Dynamic Metrics computed from actual client bookings
const metrics = computed(() => {
    const totalEvents = props.bookedEvents.length;
    const pendingEvents = props.bookedEvents.filter(
        (e) => (e.status || '').toLowerCase().includes('pending')
    ).length;
    const totalBudget = props.bookedEvents.reduce(
        (sum, e) => sum + (Number(e.budget) || 0),
        0
    );

    return [
        {
            title: 'My Booked Events',
            value: totalEvents > 0 ? `${totalEvents} Upcoming` : '0 Upcoming',
            subtitle: totalEvents > 0 ? `${totalEvents} event${totalEvents === 1 ? '' : 's'} registered` : 'No active bookings',
            icon: Calendar,
        },
        {
            title: 'Active Inquiries',
            value: pendingEvents > 0 ? `${pendingEvents} Pending` : '0 Pending',
            subtitle: pendingEvents > 0 ? 'Under review by our team' : 'All quotes up to date',
            icon: Clock,
        },
        {
            title: 'Total Investment',
            value: totalBudget > 0 ? `TZS ${totalBudget.toLocaleString()}` : 'TZS 0',
            subtitle: totalBudget > 0 ? 'Planned budget across events' : 'Completed payments',
            icon: CreditCard,
        },
        {
            title: 'Saved Favorites',
            value: '0 Saved',
            subtitle: 'Shortlisted vendors',
            icon: Heart,
        },
    ];
});

// 3. Portfolio Showcase ("Kazi Ambazo Tumezisimamia")
const managedProjects = [
    {
        title: 'Serengeti Corporate Leadership Gala',
        type: 'Corporate Summit & Banquet',
        location: 'Dar es Salaam Waterfront',
        services: ['Sound & Stage Rig', 'Banquet Catering', 'Protocol Security'],
        image: '/images/hero-city-night.jpg',
        praise: 'Flawless execution from sound engineering to VIP protocol.',
    },
    {
        title: 'Zanzibar Luxury Destination Wedding',
        type: 'Luxury Wedding Celebration',
        location: 'Sunset Beachfront Resort',
        services: ['Floral & Decor', 'Drone Cinematography', 'Live DJ Band'],
        image: '/images/card-resort.jpg',
        praise: 'The decor transformation and photography exceeded every expectation.',
    },
    {
        title: 'Kilimanjaro Annual Founders Retreat',
        type: 'Exclusive Executive Summit',
        location: 'Alpine Mountain Lodge',
        services: ['Full AV Rig', 'MC Hosting', 'Photography'],
        image: '/images/card-alpine.jpg',
        praise: 'Punctual, organized, and totally stress-free for our executive team.',
    },
];
</script>

<template>
    <div class="space-y-6">
        <!-- Notification Pop Message Banner if newly booked -->
        <div
            v-if="flashSuccess"
            class="flex items-start gap-3 rounded-xl border border-emerald-500/40 bg-emerald-500/10 p-4 text-emerald-800 dark:text-emerald-200 shadow-sm animate-in fade-in slide-in-from-top-2"
        >
            <CheckCircle2 class="size-5 shrink-0 text-emerald-600 dark:text-emerald-400 mt-0.5" />
            <div class="space-y-1">
                <h4 class="text-sm font-bold">Booking Request Received</h4>
                <p class="text-xs leading-relaxed opacity-90">{{ flashSuccess }}</p>
            </div>
        </div>

        <!-- Discovery & Planning Banner -->
        <div class="relative flex flex-col gap-4 overflow-hidden rounded-xl border border-border/80 bg-gradient-to-r from-amber-500/10 via-orange-500/5 to-transparent p-5 sm:flex-row sm:items-center sm:justify-between">
            <div class="space-y-1">
                <h3 class="text-base font-semibold text-foreground">Welcome to HEY Events</h3>
                <p class="text-xs text-muted-foreground">Manage your event bookings, coordinate with verified service providers, and explore our portfolio.</p>
            </div>
            <div class="flex items-center gap-2">
                <Link
                    href="/events/book"
                    class="inline-flex items-center gap-1.5 rounded-lg bg-primary px-3.5 py-2 text-xs font-semibold text-primary-foreground shadow-sm hover:bg-primary/90 transition-all"
                >
                    <CalendarPlus class="size-3.5" /> Book an Event
                </Link>
            </div>
        </div>

        <!-- 1. Metrics Grid -->
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div
                v-for="metric in metrics"
                :key="metric.title"
                class="rounded-xl border border-border/70 bg-card p-5 shadow-sm transition-all hover:border-amber-500/40"
            >
                <div class="flex items-center justify-between">
                    <span class="text-xs font-medium uppercase tracking-wider text-muted-foreground">{{ metric.title }}</span>
                    <div class="rounded-lg bg-amber-500/10 p-2 text-amber-600 dark:text-amber-400">
                        <component :is="metric.icon" class="size-4" />
                    </div>
                </div>
                <div class="mt-3">
                    <div class="text-2xl font-bold tracking-tight text-foreground">{{ metric.value }}</div>
                    <span class="text-xs text-muted-foreground">{{ metric.subtitle }}</span>
                </div>
            </div>
        </div>

        <!-- 2. Upcoming Bookings Section -->
        <div class="rounded-xl border border-border/70 bg-card p-6 shadow-sm space-y-4">
            <div class="flex items-center justify-between border-b border-border/50 pb-4">
                <div>
                    <h2 class="text-lg font-semibold text-foreground">My Upcoming Events & Bookings</h2>
                    <p class="text-xs text-muted-foreground">Your confirmed event contracts and operational schedule.</p>
                </div>

                <Link
                    v-if="props.bookedEvents.length > 0"
                    href="/events/my-bookings"
                    class="inline-flex items-center gap-1 text-xs font-semibold text-primary hover:underline"
                >
                    <span>View All ({{ props.bookedEvents.length }})</span>
                    <ArrowRight class="size-3" />
                </Link>
            </div>

            <!-- Clean Empty State when no events are booked -->
            <div v-if="props.bookedEvents.length === 0" class="flex flex-col items-center justify-center py-12 text-center">
                <div class="rounded-full bg-amber-500/10 p-3.5 text-amber-600 dark:text-amber-400 mb-3">
                    <CalendarCheck class="size-7" />
                </div>
                <h3 class="text-sm font-semibold text-foreground">No Booked Events Yet</h3>
                <p class="mt-1 text-xs text-muted-foreground max-w-sm">
                    You have no scheduled bookings currently. Once you confirm an event or reserve a service provider, your timeline and details will appear here.
                </p>
                <Link
                    href="/events/book"
                    class="mt-4 inline-flex items-center gap-1.5 rounded-lg border border-border bg-background px-3 py-1.5 text-xs font-medium text-foreground hover:bg-muted transition-colors"
                >
                    <CalendarPlus class="size-3.5" /> Book Your First Event
                </Link>
            </div>

            <!-- Populated list if bookings exist -->
            <div v-else class="grid gap-3.5 sm:grid-cols-2">
                <div
                    v-for="ev in props.bookedEvents"
                    :key="ev.id"
                    class="rounded-xl border border-border/60 bg-muted/20 p-4 space-y-3 transition-all hover:border-primary/40"
                >
                    <div class="flex items-start justify-between gap-2">
                        <div>
                            <span class="inline-block rounded bg-primary/10 px-2 py-0.5 text-[10px] font-semibold text-primary mb-1">
                                {{ ev.event_type }}
                            </span>
                            <h4 class="text-sm font-bold text-foreground leading-tight">{{ ev.name }}</h4>
                        </div>
                        <span class="shrink-0 rounded-full bg-amber-500/10 px-2 py-0.5 text-[10px] font-semibold text-amber-600 dark:text-amber-400 flex items-center gap-1">
                            <Clock class="size-2.5" />
                            {{ ev.status || 'Pending Review' }}
                        </span>
                    </div>

                    <div class="flex flex-wrap items-center gap-3 text-xs text-muted-foreground">
                        <span class="flex items-center gap-1">
                            <Calendar class="size-3 text-primary" />
                            {{ formatDate(ev.event_date) }}
                        </span>
                        <span class="flex items-center gap-1">
                            <MapPin class="size-3 text-primary" />
                            {{ ev.location || 'Location TBA' }}
                        </span>
                        <span class="font-semibold text-foreground">
                            {{ formatCurrency(ev.budget) }}
                        </span>
                    </div>

                    <!-- Services Tags -->
                    <div class="flex flex-wrap gap-1 border-t border-border/40 pt-2">
                        <span
                            v-for="s in ev.services"
                            :key="s"
                            class="inline-flex items-center gap-1 rounded bg-background border border-border/60 px-1.5 py-0.5 text-[10px] font-medium text-foreground"
                        >
                            <component :is="serviceMeta[s]?.icon || Sparkles" class="size-2.5 text-primary" />
                            <span>{{ serviceMeta[s]?.label || s }}</span>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. Showcase: Kazi Ambazo Tumezisimamia -->
        <div class="rounded-xl border border-border/80 bg-card p-6 shadow-sm space-y-5">
            <div class="flex items-center justify-between border-b border-border/60 pb-4">
                <div>
                    <h2 class="text-lg font-bold text-foreground">Kazi Ambazo Tumezisimamia</h2>
                    <p class="text-xs text-muted-foreground">Portfolio of events directly managed by HEY Events.</p>
                </div>
                <span class="rounded-full bg-primary/10 px-3 py-1 text-xs font-semibold text-primary">
                    100% In-House Managed
                </span>
            </div>

            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                <div
                    v-for="(proj, idx) in managedProjects"
                    :key="idx"
                    class="group overflow-hidden rounded-xl border border-border/70 bg-card shadow-sm transition-all hover:border-primary/50 hover:shadow-md"
                >
                    <div class="relative h-44 overflow-hidden bg-muted">
                        <img
                            :src="proj.image"
                            :alt="proj.title"
                            class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105"
                        />
                        <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent"></div>
                        <span class="absolute bottom-3 left-3 rounded-md bg-black/60 backdrop-blur-sm px-2 py-0.5 text-[10px] font-semibold text-white">
                            {{ proj.type }}
                        </span>
                    </div>

                    <div class="p-4 space-y-3">
                        <div>
                            <h3 class="text-sm font-bold text-foreground line-clamp-1">{{ proj.title }}</h3>
                            <p class="text-xs text-muted-foreground flex items-center gap-1 mt-0.5">
                                <MapPin class="size-3 text-muted-foreground" />
                                {{ proj.location }}
                            </p>
                        </div>

                        <!-- Services Delivered -->
                        <div class="flex flex-wrap gap-1">
                            <span
                                v-for="(svc, sIdx) in proj.services"
                                :key="sIdx"
                                class="rounded bg-muted px-2 py-0.5 text-[10px] font-medium text-foreground"
                            >
                                {{ svc }}
                            </span>
                        </div>

                        <p class="border-t border-border/40 pt-2.5 text-[11px] italic text-muted-foreground">
                            "{{ proj.praise }}"
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
