<script setup lang="ts">
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import { useAppearance } from '@/composables/useAppearance';
import { useLanguage } from '@/composables/useLanguage';
import { Phone, Mail, MapPin } from '@lucide/vue';

const currentYear = new Date().getFullYear();
const { resolvedAppearance, updateAppearance } = useAppearance();
const { t } = useLanguage();

const toggleTheme = () => {
    updateAppearance(resolvedAppearance.value === 'dark' ? 'light' : 'dark');
};
</script>

<template>
    <footer
        id="contact"
        class="border-t border-zinc-200/80 bg-white py-14 text-zinc-600 dark:border-zinc-800 dark:bg-zinc-950 dark:text-zinc-400"
    >
        <div class="mx-auto max-w-7xl px-6 sm:px-8">
            <!-- Top Contact & Info Grid -->
            <div class="grid gap-10 pb-12 sm:grid-cols-2 lg:grid-cols-4 border-b border-zinc-100 dark:border-zinc-850">
                <!-- Column 1: Brand & Tagline -->
                <div class="space-y-4">
                    <div class="flex items-center gap-3">
                        <AppLogoIcon class="size-8 drop-shadow-sm" />
                        <span
                            class="font-sans text-xl font-black tracking-tight text-zinc-900 dark:text-white"
                        >
                            HEY <span class="text-[#ff545a]">Events</span>
                        </span>
                    </div>
                    <p class="text-xs leading-relaxed text-zinc-500 dark:text-zinc-400">
                        Tanzania's unified event ecosystem. Rent equipment, hire verified vendors, and book premier venues in one click.
                    </p>
                </div>

                <!-- Column 2: Direct Contacts -->
                <div class="space-y-3">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-zinc-900 dark:text-white">
                        Contact Us
                    </h4>
                    <div class="space-y-2.5 text-xs">
                        <a
                            href="tel:+255763327959"
                            class="flex items-center gap-2.5 text-zinc-700 transition hover:text-[#ff545a] dark:text-zinc-300 dark:hover:text-[#ff545a]"
                        >
                            <Phone class="size-4 text-[#ff545a] shrink-0" />
                            <span class="font-semibold">+255 763 327 959</span>
                        </a>
                        <a
                            href="mailto:heyevents3@gmail.com"
                            class="flex items-center gap-2.5 text-zinc-700 transition hover:text-[#ff545a] dark:text-zinc-300 dark:hover:text-[#ff545a]"
                        >
                            <Mail class="size-4 text-[#ff545a] shrink-0" />
                            <span>heyevents3@gmail.com</span>
                        </a>
                        <div class="flex items-center gap-2.5 text-zinc-500 dark:text-zinc-400">
                            <MapPin class="size-4 text-[#ff545a] shrink-0" />
                            <span>Dar es Salaam, Tanzania</span>
                        </div>
                    </div>
                </div>

                <!-- Column 3: Quick Navigation -->
                <div class="space-y-3">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-zinc-900 dark:text-white">
                        Explore
                    </h4>
                    <ul class="space-y-2 text-xs">
                        <li>
                            <a href="/explore?category=rentals" class="hover:text-[#ff545a] transition">Equipment & Rentals</a>
                        </li>
                        <li>
                            <a href="/explore?category=venues" class="hover:text-[#ff545a] transition">Venues & Halls</a>
                        </li>
                        <li>
                            <a href="/explore?category=catering" class="hover:text-[#ff545a] transition">Catering & Cakes</a>
                        </li>
                        <li>
                            <a href="/#about" class="hover:text-[#ff545a] transition">About HEY Events</a>
                        </li>
                    </ul>
                </div>

                <!-- Column 4: Official Social Accounts -->
                <div class="space-y-3">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-zinc-900 dark:text-white">
                        Social Channels
                    </h4>
                    <div
                        class="inline-flex items-center gap-2 rounded-2xl border border-zinc-200/80 bg-zinc-50/80 p-2 text-xs font-semibold text-zinc-700 shadow-2xs dark:border-zinc-800 dark:bg-zinc-900/80 dark:text-zinc-300"
                    >
                        <a
                            href="https://instagram.com/heyevents.tz"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="rounded-lg p-1.5 text-zinc-600 transition hover:bg-pink-500/10 hover:text-pink-600 dark:text-zinc-400 dark:hover:text-pink-400"
                            title="Instagram @heyevents.tz"
                            aria-label="Instagram"
                        >
                            <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect width="20" height="20" x="2" y="2" rx="5" ry="5" />
                                <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z" />
                                <line x1="17.5" x2="17.51" y1="6.5" y2="6.5" />
                            </svg>
                        </a>
                        <a
                            href="https://facebook.com/heyevents.tz"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="rounded-lg p-1.5 text-zinc-600 transition hover:bg-blue-500/10 hover:text-blue-600 dark:text-zinc-400 dark:hover:text-blue-400"
                            title="Facebook @heyevents.tz"
                            aria-label="Facebook"
                        >
                            <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z" />
                            </svg>
                        </a>
                        <span class="text-zinc-300 dark:text-zinc-700">|</span>
                        <span class="text-xs font-bold text-zinc-800 dark:text-zinc-200 pr-1">heyevents.tz</span>
                    </div>
                </div>
            </div>

            <!-- Bottom Row: Copyright & Theme Switcher -->
            <div class="mt-8 flex flex-col items-center justify-between gap-4 sm:flex-row text-xs">
                <span class="text-zinc-400">
                    © {{ currentYear }} HEY Events. {{ t('footer.rights') }}
                </span>

                <div class="flex items-center gap-4">
                    <button
                        @click="toggleTheme"
                        class="inline-flex items-center gap-1.5 rounded-lg border border-zinc-200/80 bg-zinc-50 px-2.5 py-1 text-xs text-zinc-600 transition hover:bg-zinc-100 hover:text-zinc-900 dark:border-zinc-800 dark:bg-zinc-900 dark:text-zinc-400 dark:hover:bg-zinc-800 dark:hover:text-white"
                        type="button"
                    >
                        <svg
                            v-if="resolvedAppearance === 'dark'"
                            class="size-3.5 shrink-0 text-indigo-400"
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z" />
                        </svg>
                        <svg
                            v-else
                            class="size-3.5 shrink-0 text-amber-500"
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <circle cx="12" cy="12" r="4" />
                            <path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M6.34 17.66l-1.41 1.41M19.07 4.93l-1.41 1.41" />
                        </svg>
                        <span>{{ resolvedAppearance === 'dark' ? t('footer.darkMode') : t('footer.lightMode') }}</span>
                    </button>
                </div>
            </div>
        </div>
    </footer>
</template>
