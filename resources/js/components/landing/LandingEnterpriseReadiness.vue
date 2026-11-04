<script setup lang="ts">
import { useLandingData } from '@/composables/useLandingData';
import { ShieldCheck } from '@lucide/vue';

const { enterpriseFeatures } = useLandingData();
</script>

<template>
    <section id="enterprise" class="py-16 lg:py-24">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="mx-auto max-w-3xl text-center">
                <div class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-3.5 py-1 text-xs font-semibold text-emerald-700 dark:bg-emerald-950/80 dark:text-emerald-300">
                    <ShieldCheck class="h-3.5 w-3.5" />
                    <span>Built for Ground Realities</span>
                </div>

                <h2 class="mt-4 text-3xl font-black tracking-tight text-zinc-950 sm:text-4xl dark:text-white">
                    Enterprise & Team Readiness
                </h2>

                <p class="mt-3 text-sm text-zinc-600 sm:text-base dark:text-zinc-400">
                    Designed for heavy-duty production environments, multi-user teams, and local payment infrastructures.
                </p>
            </div>

            <div class="mt-12 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                <div
                    v-for="(item, idx) in enterpriseFeatures"
                    :key="idx"
                    class="group relative rounded-2xl border border-zinc-200/90 bg-white p-6 shadow-sm transition-all duration-200 hover:-translate-y-1 hover:border-zinc-300 hover:shadow-md dark:border-zinc-800 dark:bg-zinc-900"
                >
                    <div class="flex items-center justify-between">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-zinc-950 text-white dark:bg-white dark:text-zinc-950">
                            <component :is="item.icon" class="h-5 w-5" />
                        </div>
                        <span class="rounded bg-zinc-100 px-2 py-0.5 text-[10px] font-bold text-zinc-600 dark:bg-zinc-800 dark:text-zinc-400">
                            {{ item.badge }}
                        </span>
                    </div>

                    <h3 class="mt-5 text-base font-bold text-zinc-950 dark:text-white">
                        {{ item.title }}
                    </h3>

                    <p class="mt-2 text-xs leading-relaxed text-zinc-500 dark:text-zinc-400">
                        {{ item.description }}
                    </p>
                </div>
            </div>
        </div>
    </section>
</template>
