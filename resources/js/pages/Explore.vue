<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

defineOptions({
    layout: null,
});
import {
    Search,
    MapPin,
    Star,
    Armchair,
    UtensilsCrossed,
    Camera,
    Palette,
    Building2,
    Car,
    Sparkles,
    CheckCircle2,
    Lock,
    X,
    ArrowRight,
    UserCheck,
} from '@lucide/vue';
import LandingNavbar from '@/components/landing/LandingNavbar.vue';
import LandingFooter from '@/components/landing/LandingFooter.vue';
import WhatsAppCornerButton from '@/components/WhatsAppCornerButton.vue';
import { useLanguage } from '@/composables/useLanguage';

const { t } = useLanguage();
const page = usePage();
const user = computed(() => page.props.auth?.user);

// Query states
const searchQuery = ref('');
const selectedCategory = ref('all');

// Auth Gate Modal state
const isAuthModalOpen = ref(false);
const pendingBookItem = ref<any>(null);

const categories = [
    { id: 'all', name: 'All Categories', nameSw: 'Makundi Yote', icon: Sparkles },
    { id: 'rentals', name: 'Equipment & Rentals', nameSw: 'Vifaa & Ukodishaji', icon: Armchair },
    { id: 'venues', name: 'Venues & Halls', nameSw: 'Kumbi & Viwanja', icon: Building2 },
    { id: 'catering', name: 'Catering & Cakes', nameSw: 'Upishi & Keki', icon: UtensilsCrossed },
    { id: 'sound-media', name: 'Sound & Media', nameSw: 'Muziki & Picha', icon: Camera },
    { id: 'decor', name: 'Decor & Styling', nameSw: 'Mapambo & Maua', icon: Palette },
    { id: 'transport', name: 'Transport & Cars', nameSw: 'Usafiri & Magari', icon: Car },
];

const mockItems = ref([
    {
        id: 1,
        title: 'Chiavari & Banquet Chairs (White/Gold)',
        category: 'rentals',
        categoryLabel: 'Equipment & Rentals',
        location: 'Kinondoni, Dar es Salaam',
        price: 'TZS 2,500 / chair',
        rating: 4.9,
        reviewsCount: 38,
        image: '/images/card-resort.jpg',
        badge: 'Top Rental',
        providerName: 'Classic Event Rentals TZ',
        verified: true,
        details: 'High-strength resin Chiavari chairs with soft leather cushions, ideal for weddings and formal banquets.',
    },
    {
        id: 2,
        title: 'Serena Grand Ballroom & Gardens',
        category: 'venues',
        categoryLabel: 'Venues & Halls',
        location: 'City Centre, Dar es Salaam',
        price: 'TZS 3,500,000 / day',
        rating: 5.0,
        reviewsCount: 64,
        image: '/images/card-resort.jpg',
        badge: 'Premier Venue',
        providerName: 'Dar Serena Hospitality',
        verified: true,
        details: 'Luxury air-conditioned ballroom with attached tropical garden lawn for up to 600 seated banquet guests.',
    },
    {
        id: 3,
        title: 'Luxury 4-Tier Custom Wedding Cake',
        category: 'catering',
        categoryLabel: 'Catering & Cakes',
        location: 'Mikocheni, Dar es Salaam',
        price: 'TZS 450,000 - 800,000',
        rating: 4.8,
        reviewsCount: 29,
        image: '/images/card-alpine.jpg',
        badge: 'Artisan Bakery',
        providerName: 'Sweet Moments Cakes',
        verified: true,
        details: 'Custom edible sugar florals, multiple gourmet flavours, delivered and staged directly at your reception.',
    },
    {
        id: 4,
        title: 'Concert Grade PA System & DJ Booth',
        category: 'sound-media',
        categoryLabel: 'Sound & Media',
        location: 'Masaki, Dar es Salaam',
        price: 'TZS 800,000 / event',
        rating: 4.9,
        reviewsCount: 51,
        image: '/images/hero-dar-night.jpg',
        badge: 'Pro Audio',
        providerName: 'BeatWave Sound & Lights',
        verified: true,
        details: 'JBL Line Array system, wireless microphones, automated moving head lights, and experienced audio engineer.',
    },
    {
        id: 5,
        title: 'High-Peak Marquee Tent (Capacity 300)',
        category: 'rentals',
        categoryLabel: 'Equipment & Rentals',
        location: 'Sinza, Dar es Salaam',
        price: 'TZS 650,000 / day',
        rating: 4.7,
        reviewsCount: 22,
        image: '/images/card-resort.jpg',
        badge: 'Weather Proof',
        providerName: 'Kilimanjaro Tents & Logistics',
        verified: true,
        details: 'Waterproof heavy-duty PVC canvas marquee including professional setup team, draping, and perimeter poles.',
    },
    {
        id: 6,
        title: 'Mercedes Benz S-Class Bridal Chauffeur',
        category: 'transport',
        categoryLabel: 'Transport & Cars',
        location: 'Oysterbay, Dar es Salaam',
        price: 'TZS 350,000 / 6 hrs',
        rating: 5.0,
        reviewsCount: 19,
        image: '/images/card-alpine.jpg',
        badge: 'VIP Ride',
        providerName: 'Executive Bridal Fleet',
        verified: true,
        details: 'Chauffeured late-model black Mercedes S-Class decorated with silk ribbons, red carpet service included.',
    },
]);

const filteredItems = computed(() => {
    return mockItems.value.filter((item) => {
        const matchesCategory =
            selectedCategory.value === 'all' || item.category === selectedCategory.value;
        const matchesSearch =
            searchQuery.value === '' ||
            item.title.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
            item.location.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
            item.providerName.toLowerCase().includes(searchQuery.value.toLowerCase());
        return matchesCategory && matchesSearch;
    });
});

// Check query param if passed (e.g. /explore?category=venues)
if (typeof window !== 'undefined') {
    const urlParams = new URLSearchParams(window.location.search);
    const cat = urlParams.get('category');
    if (cat && categories.some((c) => c.id === cat)) {
        selectedCategory.value = cat;
    }
}

// Action triggered on clicking "Book Now" / "Rent Now"
const handleBookClick = (item: any) => {
    if (!user.value) {
        // Guest user: open clean register/login modal gate
        pendingBookItem.value = item;
        isAuthModalOpen.value = true;
    } else {
        // Logged-in user: proceed to booking flow
        window.location.href = `/events/book?item=${item.id}`;
    }
};
</script>

<template>
    <Head title="Explore Event Equipment, Services & Venues — HEY Events" />

    <div class="min-h-screen bg-slate-50 font-sans text-zinc-900 antialiased dark:bg-zinc-950 dark:text-zinc-100">
        <!-- Clean Public Website Header (No Dashboard Sidebar) -->
        <LandingNavbar />

        <!-- Header / Banner Section -->
        <section class="border-b border-zinc-200/80 bg-white py-12 dark:border-zinc-800 dark:bg-zinc-900/60">
            <div class="mx-auto max-w-7xl px-6 sm:px-8">
                <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
                    <div>
                        <div class="inline-flex items-center gap-1.5 rounded-full bg-red-500/10 px-3 py-1 text-xs font-bold text-[#ff545a]">
                            <Sparkles class="size-3.5" />
                            <span>Public Catalog • Browse Freely</span>
                        </div>
                        <h1
                            class="mt-3 font-display text-3xl font-extrabold tracking-tight text-zinc-950 sm:text-4xl dark:text-white"
                        >
                            Find Equipment, Services & Venues
                        </h1>
                        <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-400">
                            Rent chairs, tents, sound, decor, or book verified caterers and event halls across Tanzania.
                        </p>
                    </div>

                    <!-- Search Input Bar -->
                    <div class="w-full max-w-md">
                        <div class="relative">
                            <Search class="absolute left-3.5 top-1/2 size-4 -translate-y-1/2 text-zinc-400" />
                            <input
                                v-model="searchQuery"
                                type="text"
                                placeholder="Search chairs, tents, Serena, cakes, sound..."
                                class="w-full rounded-2xl border border-zinc-200 bg-white py-2.5 pl-10 pr-4 text-xs font-medium shadow-xs focus:border-[#ff545a] focus:ring-1 focus:ring-[#ff545a] dark:border-zinc-700 dark:bg-zinc-800 dark:text-white"
                            />
                        </div>
                    </div>
                </div>

                <!-- Category Chips Horizontal Slider -->
                <div class="mt-8 flex items-center gap-2 overflow-x-auto pb-2 scrollbar-none">
                    <button
                        v-for="cat in categories"
                        :key="cat.id"
                        @click="selectedCategory = cat.id"
                        type="button"
                        :class="[
                            'inline-flex shrink-0 items-center gap-2 rounded-xl px-4 py-2 text-xs font-bold transition-all shadow-2xs',
                            selectedCategory === cat.id
                                ? 'bg-[#ff545a] text-white shadow-sm'
                                : 'border border-zinc-200/90 bg-white text-zinc-700 hover:border-[#ff545a] dark:border-zinc-800 dark:bg-zinc-900 dark:text-zinc-300 dark:hover:border-red-500',
                        ]"
                    >
                        <component :is="cat.icon" class="size-3.5" />
                        <span>{{ cat.name }}</span>
                    </button>
                </div>
            </div>
        </section>

        <!-- Directory Grid Content -->
        <main class="mx-auto max-w-7xl px-6 py-12 sm:px-8">
            <div class="flex items-center justify-between pb-6">
                <span class="text-xs font-semibold text-zinc-500">
                    Showing <strong class="text-zinc-900 dark:text-white">{{ filteredItems.length }}</strong> verified items & services
                </span>
                <span class="text-xs text-zinc-400">
                    Tanzania • Real-time Availability
                </span>
            </div>

            <!-- Items Cards Grid -->
            <div v-if="filteredItems.length > 0" class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                <div
                    v-for="item in filteredItems"
                    :key="item.id"
                    class="group flex flex-col overflow-hidden rounded-3xl border border-zinc-200/80 bg-white shadow-sm transition-all hover:-translate-y-1 hover:border-[#ff545a]/60 hover:shadow-xl dark:border-zinc-800/80 dark:bg-zinc-900"
                >
                    <!-- Image with Badge -->
                    <div class="relative h-48 w-full overflow-hidden bg-zinc-100 dark:bg-zinc-800">
                        <img
                            :src="item.image"
                            :alt="item.title"
                            class="h-full w-full object-cover transition duration-300 group-hover:scale-105"
                        />
                        <div class="absolute left-3 top-3 inline-flex items-center gap-1 rounded-full bg-black/70 px-2.5 py-1 text-[10px] font-bold text-white backdrop-blur-md">
                            <span>{{ item.badge }}</span>
                        </div>
                        <div class="absolute right-3 top-3 inline-flex items-center gap-1 rounded-full bg-white/90 px-2 py-0.5 text-xs font-bold text-zinc-900 shadow-sm backdrop-blur-md dark:bg-zinc-900/90 dark:text-white">
                            <Star class="size-3 text-amber-500 fill-amber-500" />
                            <span>{{ item.rating }}</span>
                            <span class="text-[10px] text-zinc-400">({{ item.reviewsCount }})</span>
                        </div>
                    </div>

                    <!-- Content -->
                    <div class="flex flex-1 flex-col p-5">
                        <div class="flex items-center gap-1.5 text-xs text-zinc-500 dark:text-zinc-400">
                            <MapPin class="size-3 text-[#ff545a]" />
                            <span class="truncate">{{ item.location }}</span>
                        </div>

                        <h3 class="mt-2 text-sm font-bold leading-snug text-zinc-950 transition group-hover:text-[#ff545a] dark:text-white">
                            {{ item.title }}
                        </h3>

                        <p class="mt-1 line-clamp-2 text-xs text-zinc-600 dark:text-zinc-400">
                            {{ item.details }}
                        </p>

                        <div class="mt-3 flex items-center gap-1 text-[11px] text-zinc-600 dark:text-zinc-400">
                            <span>By {{ item.providerName }}</span>
                            <CheckCircle2 v-if="item.verified" class="size-3 text-emerald-500" />
                        </div>

                        <!-- Price & Action -->
                        <div class="mt-auto flex items-center justify-between border-t border-zinc-100 pt-4 dark:border-zinc-800">
                            <div>
                                <span class="block text-[10px] uppercase font-bold text-zinc-400">Rate / Price</span>
                                <span class="text-xs font-extrabold text-[#ff545a]">{{ item.price }}</span>
                            </div>

                            <button
                                type="button"
                                @click="handleBookClick(item)"
                                class="inline-flex items-center gap-1.5 rounded-xl bg-zinc-900 px-3.5 py-1.5 text-xs font-bold text-white shadow-xs transition hover:bg-[#ff545a] dark:bg-white dark:text-zinc-900 dark:hover:bg-[#ff545a] dark:hover:text-white"
                            >
                                <span>Book Now</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Empty Search State -->
            <div v-else class="flex flex-col items-center justify-center rounded-3xl border border-dashed border-zinc-300 py-16 text-center dark:border-zinc-800">
                <Search class="size-8 text-zinc-400" />
                <h4 class="mt-3 text-sm font-bold text-zinc-900 dark:text-white">No items found matching your filter</h4>
                <p class="mt-1 text-xs text-zinc-500">Try searching with a different term or select "All Categories".</p>
                <button
                    @click="searchQuery = ''; selectedCategory = 'all'"
                    class="mt-4 rounded-xl bg-[#ff545a] px-4 py-2 text-xs font-bold text-white"
                >
                    Reset Filters
                </button>
            </div>
        </main>

        <!-- Clean Public Footer -->
        <LandingFooter />

        <!-- Floating Corner WhatsApp Support -->
        <WhatsAppCornerButton />

        <!-- Guest Registration Gate Modal -->
        <div
            v-if="isAuthModalOpen"
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
        >
            <div
                class="relative w-full max-w-md overflow-hidden rounded-3xl border border-zinc-200 bg-white p-6 shadow-2xl dark:border-zinc-800 dark:bg-zinc-900"
            >
                <button
                    @click="isAuthModalOpen = false"
                    class="absolute right-4 top-4 rounded-full p-1.5 text-zinc-400 hover:bg-zinc-100 hover:text-zinc-700 dark:hover:bg-zinc-800 dark:hover:text-white"
                >
                    <X class="size-4.5" />
                </button>

                <div class="flex flex-col items-center text-center">
                    <div class="flex size-14 items-center justify-center rounded-2xl bg-red-500/10 text-[#ff545a]">
                        <Lock class="size-7" />
                    </div>

                    <h3 class="mt-4 font-display text-xl font-extrabold text-zinc-950 dark:text-white">
                        Create an Account to Book
                    </h3>
                    <p class="mt-2 text-xs text-zinc-600 dark:text-zinc-300">
                        To request <strong class="text-zinc-900 dark:text-white">"{{ pendingBookItem?.title }}"</strong>, compare rental quotes, or contact verified providers, please register or sign in to your HEY Events account.
                    </p>

                    <div class="mt-6 flex w-full flex-col gap-2.5">
                        <Link
                            href="/register"
                            class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-[#ff545a] py-3 text-xs font-bold text-white shadow-md transition hover:bg-[#e0454b]"
                        >
                            <span>Create Free Account (Sign Up)</span>
                            <ArrowRight class="size-3.5" />
                        </Link>
                        <Link
                            href="/login"
                            class="inline-flex w-full items-center justify-center gap-2 rounded-xl border border-zinc-200 bg-white py-2.5 text-xs font-bold text-zinc-800 transition hover:bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white dark:hover:bg-zinc-700"
                        >
                            <span>Already have an account? Sign In</span>
                        </Link>
                    </div>

                    <p class="mt-4 text-[11px] text-zinc-400">
                        Free for event clients • No hidden fees • Verified vendors
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>
