<script setup lang="ts">
import { usePwa } from '@/composables/usePwa';
import { Download, PlusSquare, RefreshCw, Share, WifiOff, X } from '@lucide/vue';

const {
    isInstallable,
    isOffline,
    needRefresh,
    showIosPrompt,
    installApp,
    dismissInstall,
    dismissIosPrompt,
    updateApp,
} = usePwa();
</script>

<template>
    <div class="pointer-events-none fixed inset-x-0 bottom-4 z-50 flex flex-col items-center gap-2 px-4 sm:bottom-6">
        <!-- 1. Offline Notification Badge -->
        <Transition
            enter-active-class="transition duration-300 ease-out"
            enter-from-class="transform translate-y-4 opacity-0"
            enter-to-class="transform translate-y-0 opacity-100"
            leave-active-class="transition duration-200 ease-in"
            leave-from-class="transform translate-y-0 opacity-100"
            leave-to-class="transform translate-y-4 opacity-0"
        >
            <div
                v-if="isOffline"
                class="pointer-events-auto flex items-center gap-2.5 rounded-full border border-amber-500/30 bg-amber-50/90 px-4 py-2 text-xs font-medium text-amber-900 shadow-lg backdrop-blur-md dark:border-amber-500/20 dark:bg-amber-950/80 dark:text-amber-200"
            >
                <span class="relative flex h-2 w-2">
                    <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-amber-400 opacity-75"></span>
                    <span class="relative inline-flex h-2 w-2 rounded-full bg-amber-500"></span>
                </span>
                <WifiOff class="h-3.5 w-3.5" />
                <span>You're currently offline. Using cached data.</span>
            </div>
        </Transition>

        <!-- 2. Update Available Banner -->
        <Transition
            enter-active-class="transition duration-300 ease-out"
            enter-from-class="transform translate-y-4 opacity-0 scale-95"
            enter-to-class="transform translate-y-0 opacity-100 scale-100"
            leave-active-class="transition duration-200 ease-in"
            leave-from-class="transform translate-y-0 opacity-100 scale-100"
            leave-to-class="transform translate-y-4 opacity-0 scale-95"
        >
            <div
                v-if="needRefresh"
                class="pointer-events-auto flex max-w-md items-center justify-between gap-3 rounded-2xl border border-blue-500/30 bg-white/95 p-3.5 shadow-xl backdrop-blur-md dark:border-blue-500/20 dark:bg-zinc-900/95 dark:text-zinc-100"
            >
                <div class="flex items-center gap-3">
                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-blue-500/10 text-blue-600 dark:bg-blue-400/10 dark:text-blue-400">
                        <RefreshCw class="h-4 w-4 animate-spin" />
                    </div>
                    <div>
                        <p class="text-xs font-semibold">New update available</p>
                        <p class="text-[11px] text-zinc-500 dark:text-zinc-400">Reload to update HEY Events to the latest version.</p>
                    </div>
                </div>
                <button
                    type="button"
                    @click="updateApp"
                    class="shrink-0 rounded-lg bg-blue-600 px-3 py-1.5 text-xs font-medium text-white shadow transition hover:bg-blue-700 active:scale-95"
                >
                    Update
                </button>
            </div>
        </Transition>

        <!-- 3. Standard Install App Prompt (Chrome, Edge, Android) -->
        <Transition
            enter-active-class="transition duration-300 ease-out"
            enter-from-class="transform translate-y-6 opacity-0 scale-95"
            enter-to-class="transform translate-y-0 opacity-100 scale-100"
            leave-active-class="transition duration-200 ease-in"
            leave-from-class="transform translate-y-0 opacity-100 scale-100"
            leave-to-class="transform translate-y-6 opacity-0 scale-95"
        >
            <div
                v-if="isInstallable"
                class="pointer-events-auto flex w-full max-w-sm items-center justify-between gap-3 rounded-2xl border border-zinc-200/80 bg-white/95 p-3.5 shadow-2xl backdrop-blur-md dark:border-zinc-800 dark:bg-zinc-900/95 dark:text-zinc-100"
            >
                <div class="flex items-center gap-3">
                    <img
                        src="/icons/icon-192.png"
                        alt="HEY Events App Icon"
                        class="h-10 w-10 shrink-0 rounded-xl object-cover shadow-sm"
                    />
                    <div>
                        <p class="text-xs font-semibold">Install HEY Events</p>
                        <p class="text-[11px] text-zinc-500 dark:text-zinc-400">Install for faster access & offline viewing.</p>
                    </div>
                </div>

                <div class="flex items-center gap-1.5 shrink-0">
                    <button
                        type="button"
                        @click="installApp"
                        class="flex items-center gap-1.5 rounded-lg bg-zinc-900 px-3 py-1.5 text-xs font-medium text-white shadow transition hover:bg-zinc-800 active:scale-95 dark:bg-zinc-100 dark:text-zinc-900 dark:hover:bg-white"
                    >
                        <Download class="h-3.5 w-3.5" />
                        <span>Install</span>
                    </button>
                    <button
                        type="button"
                        @click="dismissInstall"
                        class="rounded-lg p-1.5 text-zinc-400 transition hover:bg-zinc-100 hover:text-zinc-600 dark:hover:bg-zinc-800 dark:hover:text-zinc-200"
                        title="Dismiss"
                    >
                        <X class="h-4 w-4" />
                    </button>
                </div>
            </div>
        </Transition>

        <!-- 4. iOS Safari Install Guide -->
        <Transition
            enter-active-class="transition duration-300 ease-out"
            enter-from-class="transform translate-y-6 opacity-0 scale-95"
            enter-to-class="transform translate-y-0 opacity-100 scale-100"
            leave-active-class="transition duration-200 ease-in"
            leave-from-class="transform translate-y-0 opacity-100 scale-100"
            leave-to-class="transform translate-y-6 opacity-0 scale-95"
        >
            <div
                v-if="showIosPrompt"
                class="pointer-events-auto flex w-full max-w-sm flex-col gap-2.5 rounded-2xl border border-zinc-200/80 bg-white/95 p-3.5 shadow-2xl backdrop-blur-md dark:border-zinc-800 dark:bg-zinc-900/95 dark:text-zinc-100"
            >
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <img
                            src="/icons/icon-192.png"
                            alt="HEY Events App Icon"
                            class="h-8 w-8 shrink-0 rounded-xl object-cover shadow-sm"
                        />
                        <div>
                            <p class="text-xs font-semibold">Install on iPhone</p>
                            <p class="text-[10px] text-zinc-500 dark:text-zinc-400">Add to your Home Screen for app experience</p>
                        </div>
                    </div>
                    <button
                        type="button"
                        @click="dismissIosPrompt"
                        class="rounded-lg p-1 text-zinc-400 transition hover:bg-zinc-100 hover:text-zinc-600 dark:hover:bg-zinc-800 dark:hover:text-zinc-200"
                        title="Dismiss"
                    >
                        <X class="h-4 w-4" />
                    </button>
                </div>

                <div class="rounded-xl bg-zinc-100/80 p-2.5 space-y-1.5 text-[11px] text-zinc-700 dark:bg-zinc-800/60 dark:text-zinc-300">
                    <div class="flex items-center gap-2">
                        <span class="flex h-4 w-4 shrink-0 items-center justify-center rounded-full bg-blue-600 text-[9px] font-bold text-white">1</span>
                        <span class="flex items-center gap-1">Tap the Safari <strong class="font-semibold text-zinc-900 dark:text-white">Share</strong> button <Share class="inline h-3.5 w-3.5 text-blue-600 dark:text-blue-400" /></span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="flex h-4 w-4 shrink-0 items-center justify-center rounded-full bg-blue-600 text-[9px] font-bold text-white">2</span>
                        <span class="flex items-center gap-1">Choose <strong class="font-semibold text-zinc-900 dark:text-white">Add to Home Screen</strong> <PlusSquare class="inline h-3.5 w-3.5 text-blue-600 dark:text-blue-400" /></span>
                    </div>
                </div>
            </div>
        </Transition>
    </div>
</template>
