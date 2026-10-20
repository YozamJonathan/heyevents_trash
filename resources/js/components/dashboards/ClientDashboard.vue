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
import { useLanguage, type TranslationKey } from '@/composables/useLanguage';

const { t } = useLanguage();

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

// 2. Dynamic Metrics computed from actual client bookings, localized
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
            title: t('clientDashboard.metricEvents'),
            value: totalEvents > 0 ? `${totalEvents} Upcoming` : t('clientDashboard.metricEventsValue'),
            subtitle: totalEvents > 0 ? `${totalEvents} event${totalEvents === 1 ? '' : 's'} registered` : t('clientDashboard.metricEventsNote'),
            icon: Calendar,
        },
        {
            title: t('clientDashboard.metricInquiries'),
            value: pendingEvents > 0 ? `${pendingEvents} Pending` : t('clientDashboard.metricInquiriesValue'),
            subtitle: pendingEvents > 0 ? 'Under review by our team' : t('clientDashboard.metricInquiriesNote'),
            icon: Clock,
        },
        {
            title: t('clientDashboard.metricInvestment'),
            value: totalBudget > 0 ? `TZS ${totalBudget.toLocaleString()}` : t('clientDashboard.metricInvestmentValue'),
            subtitle: totalBudget > 0 ? 'Planned budget across events' : t('clientDashboard.metricInvestmentNote'),
            icon: CreditCard,
        },
        {
            title: t('clientDashboard.metricFavorites'),
            value: t('clientDashboard.metricFavoritesValue'),
            subtitle: t('clientDashboard.metricFavoritesNote'),
            icon: Heart,
        },
    ];
});

// 3. Portfolio Showcase ("Kazi Ambazo Tumezisimamia")
const managedProjects = [
    {
        title: 'Serengeti Corporate Leadership Gala',
        typeKey: 'clientDashboard.project1Type',
        locationKey: 'clientDashboard.project1Location',
        serviceKeys: [
            'clientDashboard.project1Service1',
            'clientDashboard.project1Service2',
            'clientDashboard.project1Service3',
        ],
        image: '/images/hero-city-night.jpg',
        praiseKey: 'clientDashboard.project1Praise',
    },
    {
        title: 'Zanzibar Luxury Destination Wedding',
        typeKey: 'clientDashboard.project2Type',
        locationKey: 'clientDashboard.project2Location',
        serviceKeys: [
            'clientDashboard.project2Service1',
            'clientDashboard.project2Service2',
            'clientDashboard.project2Service3',
        ],
        image: '/images/card-resort.jpg',
        praiseKey: 'clientDashboard.project2Praise',
    },
    {
        title: 'Kilimanjaro Annual Founders Retreat',
        typeKey: 'clientDashboard.project3Type',
        locationKey: 'clientDashboard.project3Location',
        serviceKeys: [
            'clientDashboard.project3Service1',
            'clientDashboard.project3Service2',
            'clientDashboard.project3Service3',
        ],
        image: '/images/card-alpine.jpg',
        praiseKey: 'clientDashboard.project3Praise',
    },
] satisfies {
    title: string;
    typeKey: TranslationKey;
    locationKey: TranslationKey;
    serviceKeys: TranslationKey[];
    image: string;
    praiseKey: TranslationKey;
}[];
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
        <div
            class="border-border/80 relative flex flex-col gap-4 overflow-hidden rounded-xl border bg-gradient-to-r from-amber-500/10 via-orange-500/5 to-transparent p-5 sm:flex-row sm:items-center sm:justify-between"
        >
            <div class="space-y-1">
                <h3 class="text-foreground text-base font-semibold">
                    {{ t('clientDashboard.welcome') }}
                </h3>
                <p class="text-muted-foreground text-xs">
                    {{ t('clientDashboard.intro') }}
                </p>
            </div>
            <div class="flex items-center gap-2">
                <Link
                    href="/events/book"
                    class="bg-primary text-primary-foreground hover:bg-primary/90 inline-flex items-center gap-1.5 rounded-lg px-3.5 py-2 text-xs font-semibold shadow-sm transition-all"
                >
                    <CalendarPlus class="size-3.5" />
                    {{ t('nav.bookEvent') }}
                </Link>
            </div>
        </div>

        <!-- 1. Metrics Grid -->
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div
                v-for="metric in metrics"
                :key="metric.title"
                class="border-border/70 bg-card rounded-xl border p-5 shadow-sm transition-all hover:border-amber-500/40"
            >
                <div class="flex items-center justify-between">
                    <span
                        class="text-muted-foreground text-xs font-medium tracking-wider uppercase"
                    >{{ metric.title }}</span>
                    <div
                        class="rounded-lg bg-amber-500/10 p-2 text-amber-600 dark:text-amber-400"
                    >
                        <component :is="metric.icon" class="size-4" />
                    </div>
                </div>
                <div class="mt-3">
                    <div
                        class="text-foreground text-2xl font-bold tracking-tight"
                    >
                        {{ metric.value }}
                    </div>
                    <span class="text-muted-foreground text-xs">{{
                        metric.subtitle
                    }}</span>
                </div>
            </div>
        </div>

        <!-- 2. Upcoming Bookings Section -->
        <div class="border-border/70 bg-card rounded-xl border p-6 shadow-sm space-y-4">
            <div
                class="border-border/50 flex items-center justify-between border-b pb-4"
            >
                <div>
                    <h2 class="text-foreground text-lg font-semibold">
                        {{ t('clientDashboard.upcoming') }}
                    </h2>
                    <p class="text-muted-foreground text-xs">
                        {{ t('clientDashboard.upcomingDescription') }}
                    </p>
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
            <div
                v-if="props.bookedEvents.length === 0"
                class="flex flex-col items-center justify-center py-12 text-center"
            >
                <div
                    class="mb-3 rounded-full bg-amber-500/10 p-3.5 text-amber-600 dark:text-amber-400"
                >
                    <CalendarCheck class="size-7" />
                </div>
                <h3 class="text-foreground text-sm font-semibold">
                    {{ t('clientDashboard.noEvents') }}
                </h3>
                <p class="text-muted-foreground mt-1 max-w-sm text-xs">
                    {{ t('clientDashboard.noEventsDescription') }}
                </p>
                <Link
                    href="/events/book"
                    class="border-border bg-background text-foreground hover:bg-muted mt-4 inline-flex items-center gap-1.5 rounded-lg border px-3 py-1.5 text-xs font-medium transition-colors"
                >
                    <CalendarPlus class="size-3.5" />
                    {{ t('nav.bookEvent') }}
                </Link>
            </div>

            <!-- Populated list if bookings exist -->
            <div v-else class="grid gap-3.5 sm:grid-cols-2">
                <div
                    v-for="ev in props.bookedEvents"
                    :key="ev.id"
                    class="border-border/60 bg-muted/20 hover:border-primary/40 space-y-3 rounded-xl border p-4 transition-all"
                >
                    <div class="flex items-start justify-between gap-2">
                        <div>
                            <span class="bg-primary/10 text-primary mb-1 inline-block rounded px-2 py-0.5 text-[10px] font-semibold">
                                {{ ev.event_type }}
                            </span>
                            <h4 class="text-foreground text-sm font-bold leading-tight">{{ ev.name }}</h4>
                        </div>
                        <span class="shrink-0 rounded-full bg-amber-500/10 px-2 py-0.5 text-[10px] font-semibold text-amber-600 dark:text-amber-400 flex items-center gap-1">
                            <Clock class="size-2.5" />
                            {{ ev.status || 'Pending Review' }}
                        </span>
                    </div>

                    <div class="text-muted-foreground flex flex-wrap items-center gap-3 text-xs">
                        <span class="flex items-center gap-1">
                            <Calendar class="text-primary size-3" />
                            {{ formatDate(ev.event_date) }}
                        </span>
                        <span class="flex items-center gap-1">
                            <MapPin class="text-primary size-3" />
                            {{ ev.location || 'Location TBA' }}
                        </span>
                        <span class="text-foreground font-semibold">
                            {{ formatCurrency(ev.budget) }}
                        </span>
                    </div>

                    <!-- Services Tags -->
                    <div class="border-border/40 flex flex-wrap gap-1 border-t pt-2">
                        <span
                            v-for="s in ev.services"
                            :key="s"
                            class="bg-background border-border/60 text-foreground inline-flex items-center gap-1 rounded border px-1.5 py-0.5 text-[10px] font-medium"
                        >
                            <component :is="serviceMeta[s]?.icon || Sparkles" class="text-primary size-2.5" />
                            <span>{{ serviceMeta[s]?.label || s }}</span>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. Showcase: Kazi Ambazo Tumezisimamia -->
        <div
            class="border-border/80 bg-card space-y-5 rounded-xl border p-6 shadow-sm"
        >
            <div
                class="border-border/60 flex items-center justify-between border-b pb-4"
            >
                <div>
                    <h2 class="text-foreground text-lg font-bold">
                        {{ t('clientDashboard.portfolio') }}
                    </h2>
                    <p class="text-muted-foreground text-xs">
                        {{ t('clientDashboard.portfolioDescription') }}
                    </p>
                </div>
                <span
                    class="bg-primary/10 text-primary rounded-full px-3 py-1 text-xs font-semibold"
                >
                    {{ t('clientDashboard.managed') }}
                </span>
            </div>

            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                <div
                    v-for="(proj, idx) in managedProjects"
                    :key="idx"
                    class="group border-border/70 bg-card hover:border-primary/50 overflow-hidden rounded-xl border shadow-sm transition-all hover:shadow-md"
                >
                    <div class="bg-muted relative h-44 overflow-hidden">
                        <img
                            :src="proj.image"
                            :alt="proj.title"
                            class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105"
                        />
                        <div
                            class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent"
                        ></div>
                        <span
                            class="absolute bottom-3 left-3 rounded-md bg-black/60 px-2 py-0.5 text-[10px] font-semibold text-white backdrop-blur-sm"
                        >
                            {{ t(proj.typeKey) }}
                        </span>
                    </div>

                    <div class="space-y-3 p-4">
                        <div>
                            <h3
                                class="text-foreground line-clamp-1 text-sm font-bold"
                            >
                                {{ proj.title }}
                            </h3>
                            <p
                                class="text-muted-foreground mt-0.5 flex items-center gap-1 text-xs"
                            >
                                <MapPin class="text-muted-foreground size-3" />
                                {{ t(proj.locationKey) }}
                            </p>
                        </div>

                        <!-- Services Delivered -->
                        <div class="flex flex-wrap gap-1">
                            <span
                                v-for="(serviceKey, sIdx) in proj.serviceKeys"
                                :key="sIdx"
                                class="bg-muted text-foreground rounded px-2 py-0.5 text-[10px] font-medium"
                            >
                                {{ t(serviceKey) }}
                            </span>
                        </div>

                        <p
                            class="border-border/40 text-muted-foreground border-t pt-2.5 text-[11px] italic"
                        >
                            "{{ t(proj.praiseKey) }}"
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
