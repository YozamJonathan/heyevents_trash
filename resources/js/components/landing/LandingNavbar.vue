<script setup lang="ts">
import { computed, ref, onMounted, onUnmounted } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import { Menu, X } from '@lucide/vue';
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

const navLinks = computed(() => [
    { name: t('nav.standards'), href: '#standards' },
    { name: t('nav.howItWorks'), href: '#how-it-works' },
    { name: t('nav.exploreEvents'), href: '#explore' },
    { name: t('nav.reviews'), href: '#reviews' },
    { name: t('nav.contact'), href: '#contact' },
]);

const toggleTheme = () => {
    updateAppearance(resolvedAppearance.value === 'dark' ? 'light' : 'dark');
};

const handleScroll = () => {
    isScrolled.value = window.scrollY > 20;
};

onMounted(() => {
    window.addEventListener('scroll', handleScroll);
});

onUnmounted(() => {
    window.removeEventListener('scroll', handleScroll);
});
</script>

<template>
    <header
        class="sticky top-0 z-40 border-b backdrop-blur-md transition-all duration-300"
        :class="[
            isScrolled
                ? 'border-zinc-200/80 bg-white/85 shadow-sm dark:border-zinc-800/80 dark:bg-zinc-950/85'
                : 'dark:border-zinc-850/50 border-zinc-200/40 bg-white/70 dark:bg-zinc-950/70',
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
                        class="font-['Poppins',sans-serif] text-xl font-bold tracking-tight text-zinc-900 transition dark:text-white"
                    >
                        HEY <span class="text-[#ff545a]">Events</span>
                    </span>
                </div>
            </a>

            <!-- Desktop Navigation Links -->
            <nav class="hidden items-center space-x-8 md:flex">
                <a
                    v-for="link in navLinks"
                    :key="link.name"
                    :href="link.href"
                    class="text-sm font-medium text-zinc-600 transition hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white"
                >
                    {{ link.name }}
                </a>
            </nav>

            <!-- Right Actions (Language, Theme Switcher + Auth CTA) -->
            <div class="hidden shrink-0 items-center space-x-3.5 md:flex">
                <LanguageSwitcher compact />

                <!-- Theme Toggle Button -->
                <button
                    @click="toggleTheme"
                    class="inline-flex h-9 shrink-0 items-center gap-2 rounded-xl border border-zinc-200/90 bg-white/80 px-3 text-xs font-semibold text-zinc-800 shadow-xs backdrop-blur-md transition hover:bg-zinc-100 dark:border-zinc-700/90 dark:bg-zinc-800/80 dark:text-zinc-100 dark:hover:bg-zinc-700"
                    type="button"
                    :title="t('nav.toggleTheme')"
                    :aria-label="t('nav.toggleTheme')"
                >
                    <!-- Moon for Dark Mode, Sun for Light Mode -->
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
                    <span
                        class="text-xs font-semibold capitalize select-none"
                        >{{
                            resolvedAppearance === 'dark'
                                ? t('nav.dark')
                                : t('nav.light')
                        }}</span
                    >
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
                        class="inline-flex items-center rounded-xl bg-[#ff545a] px-4.5 py-2 text-xs font-semibold text-white shadow-sm transition hover:bg-[#e0454b]"
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

                <a
                    v-for="link in navLinks"
                    :key="link.name"
                    :href="link.href"
                    @click="isMobileMenuOpen = false"
                    class="rounded-lg px-3 py-2 text-sm font-medium text-zinc-800 hover:bg-zinc-50 dark:text-zinc-200 dark:hover:bg-zinc-900"
                >
                    {{ link.name }}
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
