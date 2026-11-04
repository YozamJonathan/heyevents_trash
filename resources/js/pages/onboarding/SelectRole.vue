<script setup lang="ts">
import { ref } from 'vue';
import { Head, router, Link } from '@inertiajs/vue3';
import { Sun, Moon, LogOut } from '@lucide/vue';
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import LanguageSwitcher from '@/components/LanguageSwitcher.vue';
import { useLanguage } from '@/composables/useLanguage';
import { useAppearance } from '@/composables/useAppearance';
import { logout } from '@/routes';
import type { ExistingProviderProfile } from '@/types/onboarding';
import RoleSelectionCards from '@/components/onboarding/RoleSelectionCards.vue';
import ProviderRegistrationSlider from '@/components/onboarding/ProviderRegistrationSlider.vue';

const props = defineProps<{
    existingProfile?: ExistingProviderProfile | null;
}>();

const { t } = useLanguage();
const { resolvedAppearance, updateAppearance } = useAppearance();

const toggleTheme = () => {
    updateAppearance(resolvedAppearance.value === 'dark' ? 'light' : 'dark');
};

const currentView = ref<'select' | 'provider'>(
    props.existingProfile?.status === 'rejected' ? 'provider' : 'select',
);
const clientSubmitting = ref(false);

const selectClient = () => {
    clientSubmitting.value = true;
    router.post(
        '/onboarding/client',
        {},
        {
            onFinish: () => {
                clientSubmitting.value = false;
            },
        },
    );
};
</script>

<template>
    <Head :title="t('onboarding.title')" />

    <div class="relative flex min-h-screen flex-col font-sans text-zinc-900 antialiased selection:bg-[#ff545a] selection:text-white dark:text-zinc-100">
        <!-- Persistent City Background -->
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
            <div class="absolute inset-0 bg-slate-100/75 backdrop-blur-[2px] dark:bg-zinc-950/80"></div>
        </div>

        <!-- Navigation Header -->
        <header class="sticky top-0 z-30 w-full border-b border-zinc-200/50 bg-white/70 backdrop-blur-md dark:border-zinc-800/50 dark:bg-zinc-950/70">
            <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-3 sm:px-8">
                <div class="flex items-center gap-3">
                    <AppLogoIcon class="size-9 drop-shadow-md" />
                    <div class="flex flex-col text-left">
                        <div class="flex items-center gap-1.5 font-sans text-base font-extrabold tracking-tight text-zinc-900 dark:text-white">
                            <span>HEY</span>
                            <span class="text-[#ff545a]">EVENTS</span>
                        </div>
                        <span class="text-[9px] font-semibold tracking-wider text-zinc-500 uppercase dark:text-zinc-400">
                            Onboarding
                        </span>
                    </div>
                </div>

                <div class="flex items-center gap-2 sm:gap-3">
                    <LanguageSwitcher compact />

                    <button
                        type="button"
                        @click="toggleTheme"
                        :title="t('nav.themeToggle')"
                        class="inline-flex size-9 items-center justify-center rounded-lg border border-zinc-200/80 bg-white/80 text-zinc-700 shadow-2xs backdrop-blur-xs transition hover:bg-zinc-100 dark:border-zinc-700/80 dark:bg-zinc-900/80 dark:text-zinc-300 dark:hover:bg-zinc-800"
                    >
                        <Sun v-if="resolvedAppearance === 'dark'" class="size-4 text-amber-400" />
                        <Moon v-else class="size-4 text-zinc-600" />
                    </button>

                    <Link
                        :href="logout()"
                        method="post"
                        as="button"
                        class="inline-flex size-9 items-center justify-center rounded-lg border border-zinc-200/80 bg-white/80 text-zinc-600 transition hover:bg-red-50 hover:text-red-600 dark:border-zinc-750 dark:bg-zinc-900/80 dark:text-zinc-400 dark:hover:bg-red-950/40 dark:hover:text-red-400"
                        :title="t('app.logout')"
                    >
                        <LogOut class="size-4" />
                    </Link>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="relative z-10 flex flex-1 items-center justify-center px-4 py-8 sm:px-6 sm:py-12">
            <!-- View 1: Role Selection Cards -->
            <RoleSelectionCards
                v-if="currentView === 'select'"
                :client-submitting="clientSubmitting"
                @select-client="selectClient"
                @select-provider="currentView = 'provider'"
            />

            <!-- View 2: 5-Step Provider Registration Slider -->
            <ProviderRegistrationSlider
                v-else
                :existing-profile="existingProfile"
                @back-to-roles="currentView = 'select'"
            />
        </main>
    </div>
</template>
