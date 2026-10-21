<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { ArrowLeft, Sun, Moon, ShieldCheck, Lock } from '@lucide/vue';
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import LanguageSwitcher from '@/components/LanguageSwitcher.vue';
import { useLanguage, type TranslationKey } from '@/composables/useLanguage';
import { useAppearance } from '@/composables/useAppearance';
import { home } from '@/routes';

const props = defineProps<{
    title?: TranslationKey;
    description?: TranslationKey;
}>();

const { t } = useLanguage();
const { resolvedAppearance, updateAppearance } = useAppearance();

const toggleTheme = () => {
    updateAppearance(resolvedAppearance.value === 'dark' ? 'light' : 'dark');
};
</script>

<template>
    <div
        class="relative flex min-h-screen flex-col font-['Poppins',sans-serif] text-zinc-900 antialiased selection:bg-[#ff545a] selection:text-white dark:text-zinc-100"
    >
        <!-- Persistent Background Layer (City Day for Light Mode, City Night for Dark Mode) -->
        <div class="pointer-events-none fixed inset-0 z-0 overflow-hidden">
            <img
                src="/images/hero-city-day.jpg"
                :alt="t('page.cityAlt')"
                class="h-full w-full object-cover object-center dark:hidden"
            />
            <img
                src="/images/hero-city-night.jpg"
                :alt="t('page.cityAlt')"
                class="hidden h-full w-full object-cover object-center dark:block"
            />
            <!-- Atmospheric Contrast Layer: balanced for high readability and glassmorphic depth -->
            <div
                class="absolute inset-0 bg-slate-100/75 backdrop-blur-[2px] dark:bg-zinc-950/80"
            ></div>
        </div>

        <!-- Top Navigation Header (Synced with Landing Page Header) -->
        <header
            class="sticky top-0 z-30 w-full border-b border-zinc-200/50 bg-white/70 backdrop-blur-md dark:border-zinc-800/50 dark:bg-zinc-950/70"
        >
            <div
                class="mx-auto flex max-w-7xl items-center justify-between px-4 py-3 sm:px-8"
            >
                <!-- Brand Monogram & Name -->
                <Link
                    :href="home()"
                    class="group flex shrink-0 items-center gap-3 transition-transform hover:scale-[1.02]"
                >
                    <AppLogoIcon
                        class="size-9 drop-shadow-md transition group-hover:scale-105"
                    />
                    <div class="flex flex-col text-left">
                        <div
                            class="flex items-center gap-1.5 font-['Poppins',sans-serif] text-base font-extrabold tracking-tight text-zinc-900 dark:text-white"
                        >
                            <span>HEY</span>
                            <span class="text-[#ff545a]">EVENTS</span>
                        </div>
                        <span
                            class="text-[9px] font-semibold tracking-wider text-zinc-500 uppercase dark:text-zinc-400"
                        >
                            Platform
                        </span>
                    </div>
                </Link>

                <!-- Actions: Back to Home + Language Switcher + Dark/Light Theme Switcher -->
                <div class="flex items-center gap-2 sm:gap-3">
                    <Link
                        :href="home()"
                        class="hidden items-center gap-1.5 rounded-lg border border-zinc-200/80 bg-white/70 px-3 py-1.5 text-xs font-semibold text-zinc-700 shadow-2xs backdrop-blur-xs transition hover:bg-white hover:text-zinc-900 sm:inline-flex dark:border-zinc-800 dark:bg-zinc-900/70 dark:text-zinc-300 dark:hover:bg-zinc-800 dark:hover:text-white"
                    >
                        <ArrowLeft class="size-3.5 text-[#ff545a]" />
                        <span>{{ t('nav.backToHome') }}</span>
                    </Link>

                    <!-- Language Switcher (EN / SW) -->
                    <LanguageSwitcher compact />

                    <!-- Theme Toggle -->
                    <button
                        type="button"
                        @click="toggleTheme"
                        :title="t('nav.themeToggle')"
                        class="inline-flex size-9 items-center justify-center rounded-lg border border-zinc-200/80 bg-white/80 text-zinc-700 shadow-2xs backdrop-blur-xs transition hover:bg-zinc-100 hover:text-zinc-900 dark:border-zinc-700/80 dark:bg-zinc-900/80 dark:text-zinc-300 dark:hover:bg-zinc-800 dark:hover:text-white"
                    >
                        <Sun
                            v-if="resolvedAppearance === 'dark'"
                            class="size-4 text-amber-400"
                        />
                        <Moon v-else class="size-4 text-zinc-600" />
                    </button>
                </div>
            </div>
        </header>

        <!-- Main Card Section -->
        <main
            class="relative z-10 flex flex-1 items-center justify-center px-4 py-8 sm:px-6 sm:py-12"
        >
            <div class="w-full max-w-[450px]">
                <!-- Ambient Subtle Glow Behind Card -->
                <div class="relative">
                    <div
                        class="absolute -inset-1.5 -z-10 rounded-3xl bg-gradient-to-r from-amber-500/20 via-[#ff545a]/25 to-rose-500/20 blur-xl opacity-75"
                    ></div>

                    <!-- Glassmorphic Card Container -->
                    <div
                        class="relative rounded-2xl border border-white/80 bg-white/90 p-6 sm:p-8 backdrop-blur-xl shadow-2xl shadow-zinc-900/10 dark:border-zinc-800/90 dark:bg-zinc-900/90 dark:shadow-black/50"
                    >
                        <!-- Card Header -->
                        <div class="mb-6 flex flex-col items-center text-center">
                            <div
                                class="mb-3 inline-flex size-12 items-center justify-center rounded-2xl border border-[#ff545a]/25 bg-gradient-to-tr from-[#ff545a]/15 to-amber-500/10 shadow-xs"
                            >
                                <ShieldCheck class="size-6 text-[#ff545a]" />
                            </div>

                            <h1
                                class="font-['Poppins',sans-serif] text-xl sm:text-2xl font-bold tracking-tight text-zinc-900 dark:text-white"
                            >
                                {{ props.title ? t(props.title) : '' }}
                            </h1>
                            <p
                                class="mt-1 text-xs sm:text-sm text-zinc-500 dark:text-zinc-400"
                            >
                                {{
                                    props.description ? t(props.description) : ''
                                }}
                            </p>
                        </div>

                        <!-- Form / Page Slot -->
                        <slot />
                    </div>
                </div>

                <!-- Trust and Security Badges -->
                <div
                    class="mt-6 flex flex-col items-center gap-1.5 text-center text-xs text-zinc-500 dark:text-zinc-400"
                >
                    <div
                        class="inline-flex items-center gap-1.5 font-medium text-zinc-600 dark:text-zinc-400"
                    >
                        <Lock class="size-3 text-emerald-500" />
                        <span>{{ t('nav.secureAuth') }}</span>
                    </div>
                    <p class="text-[11px] text-zinc-400 dark:text-zinc-500">
                        © 2026 HEY Events Platform. All rights reserved.
                    </p>
                </div>
            </div>
        </main>
    </div>
</template>
