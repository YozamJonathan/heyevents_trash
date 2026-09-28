<script setup lang="ts">
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import { useAppearance } from '@/composables/useAppearance';

const currentYear = new Date().getFullYear();
const { resolvedAppearance, updateAppearance } = useAppearance();

const toggleTheme = () => {
    updateAppearance(resolvedAppearance.value === 'dark' ? 'light' : 'dark');
};
</script>

<template>
    <footer id="contact" class="border-t border-zinc-200/80 bg-white py-12 text-zinc-600 dark:border-zinc-800 dark:bg-zinc-950 dark:text-zinc-400">
        <div class="mx-auto max-w-7xl px-6 sm:px-8">
            <div class="flex flex-col md:flex-row items-center justify-between gap-6">
                <!-- Brand -->
                <div class="flex items-center gap-3">
                    <AppLogoIcon class="size-8 drop-shadow-sm" />
                    <span class="text-lg font-bold tracking-tight text-zinc-900 dark:text-white font-['Poppins',sans-serif]">
                        HEY <span class="text-[#ff545a]">Events</span>
                    </span>
                    <span class="text-xs text-zinc-400 ml-2">
                        © {{ currentYear }} All rights reserved.
                    </span>
                </div>

                <!-- Links & Theme Toggle -->
                <div class="flex flex-wrap items-center gap-6 text-xs font-medium text-zinc-500 dark:text-zinc-400">
                    <a href="#standards" class="hover:text-zinc-900 dark:hover:text-white transition">Standards</a>
                    <a href="#how-it-works" class="hover:text-zinc-900 dark:hover:text-white transition">How It Works</a>
                    <a href="#explore" class="hover:text-zinc-900 dark:hover:text-white transition">Explore</a>
                    <a href="#reviews" class="hover:text-zinc-900 dark:hover:text-white transition">Reviews</a>
                    <a href="mailto:support@heyevents.com" class="hover:text-zinc-900 dark:hover:text-white transition">Support</a>

                    <button
                        @click="toggleTheme"
                        class="inline-flex items-center gap-1.5 rounded-lg border border-zinc-200/80 bg-zinc-50 px-2.5 py-1 text-xs text-zinc-600 transition hover:bg-zinc-100 hover:text-zinc-900 dark:border-zinc-800 dark:bg-zinc-900 dark:text-zinc-400 dark:hover:bg-zinc-800 dark:hover:text-white"
                        type="button"
                        aria-label="Toggle theme"
                    >
                        <svg v-if="resolvedAppearance === 'dark'" class="size-3.5 shrink-0 text-indigo-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z" />
                        </svg>
                        <svg v-else class="size-3.5 shrink-0 text-amber-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="4" />
                            <path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M6.34 17.66l-1.41 1.41M19.07 4.93l-1.41 1.41" />
                        </svg>
                        <span>{{ resolvedAppearance === 'dark' ? 'Dark Mode' : 'Light Mode' }}</span>
                    </button>
                </div>
            </div>
        </div>
    </footer>
</template>
