<script setup lang="ts">
import { computed, ref, onMounted, onUnmounted } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import {
    Menu,
    X,
    ChevronDown,
    Armchair,
    UtensilsCrossed,
    Camera,
    Sparkles,
    Palette,
    Music2,
    Building2,
    Car,
    ArrowRight,
    PlusCircle,
} from '@lucide/vue';
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import { useAppearance } from '@/composables/useAppearance';
import { useLanguage } from '@/composables/useLanguage';
import LanguageSwitcher from '@/components/LanguageSwitcher.vue';

const page = usePage();
const user = page.props.auth?.user;
const { resolvedAppearance, updateAppearance } = useAppearance();
const { t } = useLanguage();

const isScrolled = ref(false);
const isMobileMenuOpen = ref(false);
const isServicesDropdownOpen = ref(false);

// Mega Menu Categories for Services & Rentals
const megaMenuCategories = [
    {
        title: 'Equipment & Rentals',
        titleSw: 'Vifaa & Ukodishaji',
        desc: 'Chairs, tables, tents, marquees, stages, generators',
        descSw: 'Viti, meza, mahema, majukwaa, jenereta',
        icon: Armchair,
        color: 'text-amber-500 bg-amber-500/10 dark:bg-amber-500/20',
        href: '/explore?category=rentals',
    },
    {
        title: 'Food, Catering & Cakes',
        titleSw: 'Upishi, Chakula & Keki',
        desc: 'Wedding cakes, buffet, private chefs, drinks & bar',
        descSw: 'Keki za harusi, buffet, vinywaji na wahudumu',
        icon: UtensilsCrossed,
        color: 'text-rose-500 bg-rose-500/10 dark:bg-rose-500/20',
        href: '/explore?category=catering',
    },
    {
        title: 'Sound, Lighting & Media',
        titleSw: 'Muziki, Taa & Media',
        desc: 'PA systems, DJs, LED walls, photographers & drone',
        descSw: 'Sound system, ma-DJ, taa, picha na video',
        icon: Camera,
        color: 'text-purple-500 bg-purple-500/10 dark:bg-purple-500/20',
        href: '/explore?category=sound-media',
    },
    {
        title: 'Decor & Floral Styling',
        titleSw: 'Mapambo & Maua',
        desc: 'Wedding decor, fresh flowers, red carpets, backdrops',
        descSw: 'Mapambo ya sherehe, maua, zulia na mandhari',
        icon: Palette,
        color: 'text-pink-500 bg-pink-500/10 dark:bg-pink-500/20',
        href: '/explore?category=decor',
    },
    {
        title: 'Venues & Halls',
        titleSw: 'Kumbi & Viwanja',
        desc: 'Gardens, wedding halls, conference centers, villas',
        descSw: 'Bustani, kumbi za harusi, hoteli na mikutano',
        icon: Building2,
        color: 'text-emerald-500 bg-emerald-500/10 dark:bg-emerald-500/20',
        href: '/explore?category=venues',
    },
    {
        title: 'Transport & Bridal Cars',
        titleSw: 'Usafiri & Magari ya Kifahari',
        desc: 'Wedding luxury cars, guest buses, equipment delivery',
        descSw: 'Magari ya harusi, mabasi ya wageni, usafirishaji',
        icon: Car,
        color: 'text-blue-500 bg-blue-500/10 dark:bg-blue-500/20',
        href: '/explore?category=transport',
    },
];

const toggleTheme = () => {
    updateAppearance(resolvedAppearance.value === 'dark' ? 'light' : 'dark');
};

const handleScroll = () => {
    isScrolled.value = window.scrollY > 20;
};

// Close dropdown on outside click
const closeDropdown = (e: MouseEvent) => {
    const target = e.target as HTMLElement;
    if (!target.closest('#services-dropdown-container')) {
        isServicesDropdownOpen.value = false;
    }
};

onMounted(() => {
    window.addEventListener('scroll', handleScroll);
    document.addEventListener('click', closeDropdown);
});

onUnmounted(() => {
    window.removeEventListener('scroll', handleScroll);
    document.removeEventListener('click', closeDropdown);
});
</script>

<template>
    <header
        class="sticky top-0 z-40 border-b backdrop-blur-md transition-all duration-300"
        :class="[
            isScrolled
                ? 'border-zinc-200/80 bg-white/90 shadow-sm dark:border-zinc-800/80 dark:bg-zinc-950/90'
                : 'border-zinc-200/40 bg-white/75 dark:border-zinc-850/50 dark:bg-zinc-950/75',
        ]"
    >
        <div
            class="mx-auto flex max-w-7xl items-center justify-between px-6 py-3.5 sm:px-8"
        >
            <!-- Brand Monogram & Name -->
            <a href="#" class="group flex shrink-0 items-center gap-3">
                <AppLogoIcon
                    class="size-9.5 drop-shadow-md transition group-hover:scale-105"
                />
                <div class="flex flex-col">
                    <span
                        class="font-sans text-xl font-black tracking-tight text-zinc-900 transition dark:text-white"
                    >
                        HEY <span class="text-[#ff545a]">Events</span>
                    </span>
                </div>
            </a>

            <!-- Desktop Navigation Links with Mega Dropdown -->
            <nav class="hidden items-center space-x-6 lg:space-x-7 md:flex">
                <!-- 0. Home Link -->
                <Link
                    href="/"
                    class="text-sm font-semibold text-zinc-700 transition hover:text-[#ff545a] dark:text-zinc-300 dark:hover:text-white"
                >
                    {{ t('nav.home') }}
                </Link>

                <!-- 1. Services & Rentals with Modern Mega Dropdown -->
                <div id="services-dropdown-container" class="relative">
                    <button
                        type="button"
                        @click="isServicesDropdownOpen = !isServicesDropdownOpen"
                        class="group inline-flex items-center gap-1.5 text-sm font-semibold transition hover:text-[#ff545a]"
                        :class="[
                            isServicesDropdownOpen
                                ? 'text-[#ff545a]'
                                : 'text-zinc-700 dark:text-zinc-300 dark:hover:text-white',
                        ]"
                    >
                        <span>{{ t('nav.servicesAndRentals') }}</span>
                        <ChevronDown
                            class="size-3.5 transition-transform duration-200"
                            :class="{ 'rotate-180 text-[#ff545a]': isServicesDropdownOpen }"
                        />
                    </button>

                    <!-- Mega Dropdown Panel -->
                    <transition
                        enter-active-class="transition duration-200 ease-out"
                        enter-from-class="opacity-0 translate-y-2"
                        enter-to-class="opacity-100 translate-y-0"
                        leave-active-class="transition duration-150 ease-in"
                        leave-from-class="opacity-100 translate-y-0"
                        leave-to-class="opacity-0 translate-y-2"
                    >
                        <div
                            v-if="isServicesDropdownOpen"
                            class="absolute left-1/2 -translate-x-1/3 top-full mt-3 w-[660px] rounded-3xl border border-zinc-200/90 bg-white/95 p-6 shadow-2xl backdrop-blur-2xl dark:border-zinc-800 dark:bg-zinc-900/95"
                        >
                            <!-- Header Inside Dropdown -->
                            <div class="flex items-center justify-between border-b border-zinc-100 pb-3 dark:border-zinc-800">
                                <div>
                                    <span class="text-xs font-bold uppercase tracking-wider text-zinc-400 dark:text-zinc-500">
                                        {{ t('nav.servicesAndRentals') }}
                                    </span>
                                    <p class="text-xs font-medium text-zinc-600 dark:text-zinc-400">
                                        {{ t('hero.title') }}
                                    </p>
                                </div>
                                <span class="rounded-full bg-red-500/10 px-2.5 py-0.5 text-[10px] font-bold text-[#ff545a]">
                                    All-in-One
                                </span>
                            </div>

                            <!-- 6 Main Category Grid -->
                            <div class="mt-4 grid grid-cols-2 gap-3">
                                <a
                                    v-for="(cat, idx) in megaMenuCategories"
                                    :key="idx"
                                    :href="cat.href"
                                    @click="isServicesDropdownOpen = false"
                                    class="group flex items-start gap-3 rounded-2xl p-2.5 transition hover:bg-zinc-50 dark:hover:bg-zinc-800/60"
                                >
                                    <div
                                        class="flex size-10 shrink-0 items-center justify-center rounded-xl transition group-hover:scale-105"
                                        :class="cat.color"
                                    >
                                        <component :is="cat.icon" class="size-5" />
                                    </div>
                                    <div class="min-w-0">
                                        <h4 class="text-xs font-bold text-zinc-900 transition group-hover:text-[#ff545a] dark:text-white">
                                            {{ cat.title }}
                                        </h4>
                                        <p class="mt-0.5 line-clamp-1 text-[11px] text-zinc-500 dark:text-zinc-400">
                                            {{ cat.desc }}
                                        </p>
                                    </div>
                                </a>
                            </div>

                            <!-- Bottom Directory Link -->
                            <div class="mt-4 flex items-center justify-between rounded-2xl bg-zinc-50 p-3 dark:bg-zinc-800/50">
                                <span class="text-xs font-medium text-zinc-600 dark:text-zinc-300">
                                    Looking for chairs, sound, cakes, or full planning?
                                </span>
                                <Link
                                    href="/explore"
                                    @click="isServicesDropdownOpen = false"
                                    class="inline-flex items-center gap-1.5 text-xs font-bold text-[#ff545a] hover:underline"
                                >
                                    <span>{{ t('nav.viewAllCategories') }}</span>
                                    <ArrowRight class="size-3.5" />
                                </Link>
                            </div>
                        </div>
                    </transition>
                </div>

                <!-- 2. Venues Quick Link (Dedicated Route) -->
                <Link
                    href="/explore?category=venues"
                    class="text-sm font-medium text-zinc-600 transition hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white"
                >
                    {{ t('nav.venues') }}
                </Link>

                <!-- 3. How It Works (Same Page Section) -->
                <a
                    href="/#how-it-works"
                    class="text-sm font-medium text-zinc-600 transition hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white"
                >
                    {{ t('nav.howItWorks') }}
                </a>

                <!-- 4. About Us (Same Page Section) -->
                <a
                    href="/#about"
                    class="text-sm font-medium text-zinc-600 transition hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white"
                >
                    {{ t('nav.aboutUs') }}
                </a>

                <!-- 5. Contact (Same Page Section) -->
                <a
                    href="/#contact"
                    class="text-sm font-medium text-zinc-600 transition hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white"
                >
                    {{ t('nav.contact') }}
                </a>
            </nav>

            <!-- Right Actions (Language, Theme Switcher + Auth) -->
            <div class="hidden shrink-0 items-center space-x-3.5 md:flex">
                <LanguageSwitcher compact />

                <!-- Theme Toggle Button -->
                <button
                    @click="toggleTheme"
                    class="inline-flex h-9 shrink-0 items-center gap-2 rounded-xl border border-zinc-200/90 bg-white/80 px-2.5 text-xs font-semibold text-zinc-800 shadow-xs backdrop-blur-md transition hover:bg-zinc-100 dark:border-zinc-700/90 dark:bg-zinc-800/80 dark:text-zinc-100 dark:hover:bg-zinc-700"
                    type="button"
                    :title="t('nav.toggleTheme')"
                    :aria-label="t('nav.toggleTheme')"
                >
                    <svg
                        v-if="resolvedAppearance === 'dark'"
                        class="size-4 shrink-0 text-indigo-400"
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    >
                        <path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z" />
                    </svg>
                    <svg
                        v-else
                        class="size-4 shrink-0 text-amber-500"
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    >
                        <circle cx="12" cy="12" r="4" />
                        <path
                            d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M6.34 17.66l-1.41 1.41M19.07 4.93l-1.41 1.41"
                        />
                    </svg>
                </button>

                <template v-if="user">
                    <Link
                        href="/dashboard"
                        class="inline-flex items-center rounded-xl bg-zinc-900 px-4 py-2 text-xs font-semibold text-white shadow-sm transition hover:bg-zinc-800 dark:bg-white dark:text-zinc-900 dark:hover:bg-zinc-100"
                    >
                        {{ t('nav.dashboard') }}
                    </Link>
                </template>
                <template v-else>
                    <Link
                        href="/login"
                        class="px-2 text-sm font-medium text-zinc-600 transition hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white"
                    >
                        {{ t('nav.signIn') }}
                    </Link>
                    <Link
                        href="/register"
                        class="inline-flex items-center rounded-xl bg-[#ff545a] px-4 py-2 text-xs font-semibold text-white shadow-sm transition hover:bg-[#e0454b]"
                    >
                        {{ t('nav.getStarted') }}
                    </Link>
                </template>
            </div>

            <!-- Mobile Actions (Theme Button + Hamburger) -->
            <div class="flex shrink-0 items-center gap-2 md:hidden">
                <button
                    @click="toggleTheme"
                    class="flex size-9 shrink-0 items-center justify-center rounded-xl border border-zinc-200 bg-white/80 text-zinc-800 backdrop-blur-md transition dark:border-zinc-700 dark:bg-zinc-800/80 dark:text-zinc-100"
                    type="button"
                    :aria-label="t('nav.toggleTheme')"
                >
                    <svg
                        v-if="resolvedAppearance === 'dark'"
                        class="size-4 shrink-0 text-indigo-400"
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    >
                        <path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z" />
                    </svg>
                    <svg
                        v-else
                        class="size-4 shrink-0 text-amber-500"
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    >
                        <circle cx="12" cy="12" r="4" />
                        <path
                            d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M6.34 17.66l-1.41 1.41M19.07 4.93l-1.41 1.41"
                        />
                    </svg>
                </button>

                <button
                    @click="isMobileMenuOpen = !isMobileMenuOpen"
                    class="rounded-xl p-2 text-zinc-700 hover:bg-zinc-100 dark:text-zinc-300 dark:hover:bg-zinc-800"
                    type="button"
                    :aria-label="t('nav.toggleMenu')"
                >
                    <X v-if="isMobileMenuOpen" class="h-6 w-6" />
                    <Menu v-else class="h-6 w-6" />
                </button>
            </div>
        </div>

        <!-- Mobile Drawer -->
        <div
            v-if="isMobileMenuOpen"
            class="border-b border-zinc-200 bg-white px-6 pt-2 pb-6 shadow-xl md:hidden dark:border-zinc-800 dark:bg-zinc-950"
        >
            <div class="flex flex-col space-y-3">
                <LanguageSwitcher />

                <Link
                    href="/"
                    @click="isMobileMenuOpen = false"
                    class="rounded-lg px-3 py-2 text-sm font-semibold text-zinc-800 hover:bg-zinc-50 dark:text-zinc-200 dark:hover:bg-zinc-900"
                >
                    {{ t('nav.home') }}
                </Link>

                <Link
                    href="/explore"
                    @click="isMobileMenuOpen = false"
                    class="rounded-lg px-3 py-2 text-sm font-semibold text-zinc-800 hover:bg-zinc-50 dark:text-zinc-200 dark:hover:bg-zinc-900"
                >
                    {{ t('nav.servicesAndRentals') }}
                </Link>

                <Link
                    href="/explore?category=venues"
                    @click="isMobileMenuOpen = false"
                    class="rounded-lg px-3 py-2 text-sm font-semibold text-zinc-800 hover:bg-zinc-50 dark:text-zinc-200 dark:hover:bg-zinc-900"
                >
                    {{ t('nav.venues') }}
                </Link>

                <a
                    href="/#how-it-works"
                    @click="isMobileMenuOpen = false"
                    class="rounded-lg px-3 py-2 text-sm font-semibold text-zinc-800 hover:bg-zinc-50 dark:text-zinc-200 dark:hover:bg-zinc-900"
                >
                    {{ t('nav.howItWorks') }}
                </a>

                <a
                    href="/#about"
                    @click="isMobileMenuOpen = false"
                    class="rounded-lg px-3 py-2 text-sm font-semibold text-zinc-800 hover:bg-zinc-50 dark:text-zinc-200 dark:hover:bg-zinc-900"
                >
                    {{ t('nav.aboutUs') }}
                </a>

                <a
                    href="/#contact"
                    @click="isMobileMenuOpen = false"
                    class="rounded-lg px-3 py-2 text-sm font-semibold text-zinc-800 hover:bg-zinc-50 dark:text-zinc-200 dark:hover:bg-zinc-900"
                >
                    {{ t('nav.contact') }}
                </a>

                <div class="border-t border-zinc-100 pt-3 dark:border-zinc-800">
                    <Link
                        v-if="!user"
                        href="/login"
                        class="block w-full rounded-lg border border-zinc-200 py-2.5 text-center text-xs font-semibold text-zinc-800 dark:border-zinc-800 dark:text-zinc-200"
                    >
                        {{ t('nav.signIn') }}
                    </Link>
                    <Link
                        v-if="!user"
                        href="/register"
                        class="mt-2 block w-full rounded-lg bg-[#ff545a] py-2.5 text-center text-xs font-semibold text-white"
                    >
                        {{ t('nav.getStarted') }}
                    </Link>
                    <Link
                        v-else
                        href="/dashboard"
                        class="block w-full rounded-lg bg-zinc-900 py-2.5 text-center text-xs font-semibold text-white dark:bg-white dark:text-zinc-900"
                    >
                        {{ t('nav.dashboard') }}
                    </Link>
                </div>
            </div>
        </div>
    </header>
</template>
