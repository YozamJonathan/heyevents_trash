<script setup lang="ts">
import Breadcrumbs from '@/components/Breadcrumbs.vue';
import { SidebarTrigger } from '@/components/ui/sidebar';
import { useWorkspaceMode } from '@/composables/useWorkspaceMode';
import { useLanguage } from '@/composables/useLanguage';
import { useAppearance } from '@/composables/useAppearance';
import LanguageSwitcher from '@/components/LanguageSwitcher.vue';
import NotificationBell from '@/components/NotificationBell.vue';
import { Briefcase, PartyPopper, Moon, Sun } from '@lucide/vue';
import type { BreadcrumbItem } from '@/types';

withDefaults(
    defineProps<{
        breadcrumbs?: BreadcrumbItem[];
    }>(),
    {
        breadcrumbs: () => [],
    },
);

const { isServiceProvider, isClientMode, setMode } = useWorkspaceMode();
const { t } = useLanguage();
const { updateAppearance, resolvedAppearance } = useAppearance();

const toggleTheme = () => {
    updateAppearance(resolvedAppearance.value === 'dark' ? 'light' : 'dark');
};
</script>

<template>
    <header
        class="border-sidebar-border/70 flex h-16 shrink-0 items-center justify-between gap-2 border-b px-4 transition-[width,height] ease-linear group-has-data-[collapsible=icon]/sidebar-wrapper:h-12 md:px-6"
    >
        <div class="flex items-center gap-2 min-w-0">
            <SidebarTrigger class="-ml-1 shrink-0" />
            <template v-if="breadcrumbs && breadcrumbs.length > 0">
                <Breadcrumbs :breadcrumbs="breadcrumbs" class="truncate" />
            </template>
        </div>

        <!-- Header Actions: Mode Pill (for Providers) + Language Switcher + Theme Toggle -->
        <div class="flex items-center gap-2 sm:gap-2.5 shrink-0">
            <!-- Header Mode Pill (Visible on all pages when logged in as a Service Provider) -->
            <div
                v-if="isServiceProvider"
                class="flex items-center rounded-xl border border-sidebar-border bg-sidebar-accent/50 p-1 text-xs shadow-2xs"
            >
                <button
                    type="button"
                    @click="setMode('provider')"
                    :class="[
                        'inline-flex items-center gap-1.5 rounded-lg px-2.5 py-1 font-bold text-xs transition-all',
                        !isClientMode
                            ? 'bg-background text-foreground shadow-xs'
                            : 'text-muted-foreground hover:text-foreground',
                    ]"
                >
                    <Briefcase class="size-3.5" />
                    <span class="hidden sm:inline">{{ t('app.vendorWorkspace') }}</span>
                    <span class="sm:hidden">Vendor</span>
                </button>
                <button
                    type="button"
                    @click="setMode('client')"
                    :class="[
                        'inline-flex items-center gap-1.5 rounded-lg px-2.5 py-1 font-bold text-xs transition-all',
                        isClientMode
                            ? 'bg-[#ff545a] text-white shadow-xs'
                            : 'text-muted-foreground hover:text-foreground',
                    ]"
                >
                    <PartyPopper class="size-3.5" />
                    <span class="hidden sm:inline">{{ t('app.browseAsClient') }}</span>
                    <span class="sm:hidden">Client</span>
                </button>
            </div>

            <!-- Language Switcher (Compact EN / SW) -->
            <LanguageSwitcher compact />

            <!-- Theme Toggle Button (Light / Dark) -->
            <button
                type="button"
                @click="toggleTheme"
                class="inline-flex size-8 shrink-0 items-center justify-center rounded-lg border border-sidebar-border bg-sidebar-accent/40 text-foreground shadow-2xs transition-colors hover:border-[#ff545a]/50 hover:bg-sidebar-accent hover:text-[#ff545a] focus:outline-hidden"
                :title="t('nav.toggleTheme')"
                :aria-label="t('nav.toggleTheme')"
            >
                <Moon
                    v-if="resolvedAppearance === 'dark'"
                    class="size-4 text-indigo-400"
                />
                <Sun
                    v-else
                    class="size-4 text-amber-500"
                />
            </button>

            <!-- Notification Bell with Unread Badge (Client & Vendor) -->
            <NotificationBell />
        </div>
    </header>
</template>
