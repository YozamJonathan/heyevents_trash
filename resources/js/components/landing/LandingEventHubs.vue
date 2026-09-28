<script setup lang="ts">
import { useLandingData } from '@/composables/useLandingData';
import { Check, ArrowRight, Sparkles } from '@lucide/vue';

const { activeHubId, eventHubs, currentHub } = useLandingData();
</script>

<template>
    <section id="hubs" class="py-16 lg:py-24">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="mx-auto max-w-3xl text-center">
                <p class="text-xs font-semibold tracking-widest text-indigo-600 uppercase dark:text-indigo-400">4 Functional Hubs</p>
                <h2 class="mt-2 text-3xl font-bold tracking-tight text-zinc-950 sm:text-4xl dark:text-white">
                    Tailored Systems for Every Event Category
                </h2>
                <p class="mt-3 text-sm text-zinc-600 sm:text-base dark:text-zinc-400">
                    Switch between event hubs to see specialized capabilities built for your exact domain.
                </p>
            </div>

            <!-- Hub Buttons -->
            <div class="mt-10 grid grid-cols-2 gap-3 md:grid-cols-4">
                <button
                    v-for="hub in eventHubs"
                    :key="hub.id"
                    @click="activeHubId = hub.id"
                    :class="[
                        'flex flex-col items-center justify-center rounded-2xl border p-4 text-center transition-all duration-200',
                        activeHubId === hub.id
                            ? 'border-zinc-950 bg-zinc-950 text-white shadow-lg dark:border-white dark:bg-white dark:text-zinc-950'
                            : 'border-zinc-200 bg-white text-zinc-700 hover:border-zinc-300 dark:border-zinc-800 dark:bg-zinc-900 dark:text-zinc-300'
                    ]"
                >
                    <component :is="hub.icon" class="h-5 w-5" />
                    <span class="mt-2 text-xs font-bold">{{ hub.title }}</span>
                </button>
            </div>

            <!-- Dynamic Hub Showcase Box -->
            <div class="mt-8 rounded-3xl border border-zinc-200/90 bg-zinc-50/50 p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900/40 lg:p-10">
                <Transition name="fade" mode="out-in">
                    <div :key="currentHub.id" class="grid items-center gap-8 lg:grid-cols-12">
                        <!-- Left Details -->
                        <div class="space-y-5 lg:col-span-7">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="rounded-full bg-indigo-100 px-3 py-1 text-xs font-bold text-indigo-700 dark:bg-indigo-950 dark:text-indigo-300">
                                    {{ currentHub.badge }}
                                </span>
                            </div>

                            <h3 class="text-2xl font-bold text-zinc-950 dark:text-white">
                                Built for {{ currentHub.title }}
                            </h3>

                            <!-- Event type tags covered -->
                            <div class="flex flex-wrap gap-1.5">
                                <span
                                    v-for="(type, tIdx) in currentHub.eventTypes"
                                    :key="tIdx"
                                    class="rounded-md border border-zinc-200 bg-white px-2.5 py-1 text-[11px] font-medium text-zinc-600 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-300"
                                >
                                    {{ type }}
                                </span>
                            </div>

                            <!-- Key capabilities with checks -->
                            <div class="space-y-2.5 pt-2">
                                <div
                                    v-for="(hl, hIdx) in currentHub.highlights"
                                    :key="hIdx"
                                    class="flex items-center gap-2 text-xs font-medium text-zinc-700 dark:text-zinc-300"
                                >
                                    <div class="flex h-4 w-4 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-emerald-600 dark:bg-emerald-950 dark:text-emerald-400">
                                        <Check class="h-2.5 w-2.5" />
                                    </div>
                                    <span>{{ hl }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Right Live Hub Preview Card -->
                        <div class="lg:col-span-5">
                            <div class="rounded-2xl border border-zinc-200 bg-white p-6 shadow-md dark:border-zinc-800 dark:bg-zinc-900">
                                <div class="flex items-center justify-between border-b border-zinc-100 pb-3 dark:border-zinc-800">
                                    <div>
                                        <span class="text-[10px] font-bold text-zinc-400 uppercase">Active Workspace</span>
                                        <h4 class="text-sm font-bold text-zinc-900 dark:text-white">{{ currentHub.previewTitle }}</h4>
                                    </div>
                                    <div class="flex h-7 w-7 items-center justify-center rounded-lg bg-indigo-50 text-indigo-600 dark:bg-indigo-950 dark:text-indigo-400">
                                        <Sparkles class="h-3.5 w-3.5" />
                                    </div>
                                </div>

                                <div class="mt-4 grid grid-cols-2 gap-3">
                                    <div
                                        v-for="(metric, mIdx) in currentHub.previewMetrics"
                                        :key="mIdx"
                                        class="rounded-xl bg-zinc-50 p-3 dark:bg-zinc-800/50"
                                    >
                                        <div class="text-[10px] text-zinc-400 font-semibold">{{ metric.label }}</div>
                                        <div class="mt-1 text-base font-extrabold text-zinc-900 dark:text-white">{{ metric.value }}</div>
                                    </div>
                                </div>

                                <div class="mt-4 flex items-center justify-between rounded-xl bg-zinc-950 px-4 py-2.5 text-xs font-semibold text-white dark:bg-white dark:text-zinc-950">
                                    <span>{{ currentHub.sampleAction }}</span>
                                    <ArrowRight class="h-3.5 w-3.5" />
                                </div>
                            </div>
                        </div>
                    </div>
                </Transition>
            </div>
        </div>
    </section>
</template>

<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.2s ease, transform 0.2s ease;
}
.fade-enter-from,
.fade-leave-to {
    opacity: 0;
    transform: translateY(6px);
}
</style>
