<script setup lang="ts">
import { Compass, CalendarCheck, QrCode } from '@lucide/vue';

const steps = [
    {
        icon: Compass,
        number: '01',
        title: 'Select Standard',
        description: 'Choose from Social, Corporate, Entertainment, or Non-Profit configurations.'
    },
    {
        icon: CalendarCheck,
        number: '02',
        title: 'Plan & Invite',
        description: 'Set up seating plans, multi-track agendas, ticket tiers, and guest invitations.'
    },
    {
        icon: QrCode,
        number: '03',
        title: 'Execute Gates',
        description: 'Scan tickets with sub-second offline gate apps and monitor real-time check-ins.'
    }
];
</script>

<template>
    <section id="how-it-works" class="relative py-16 sm:py-24 border-y border-zinc-200/50 dark:border-white/10 bg-white/40 dark:bg-zinc-950/40 backdrop-blur-md">
        <div class="mx-auto max-w-7xl px-6 sm:px-8">
            <!-- Header -->
            <div class="text-center max-w-xl mx-auto">
                <span class="text-xs font-bold tracking-wider uppercase text-[#ff545a]">
                    Simple Lifecycle
                </span>
                <h2 class="mt-2 text-2xl font-black uppercase tracking-wider text-zinc-900 sm:text-3xl dark:text-white font-['Poppins',sans-serif]">
                    How it works
                </h2>
                <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-300">
                    A streamlined workflow from event creation to gate execution.
                </p>
            </div>

            <!-- Steps Grid with Glass Cards -->
            <div class="mt-12 grid grid-cols-1 gap-8 md:grid-cols-3">
                <div
                    v-for="(step, index) in steps"
                    :key="index"
                    class="relative flex flex-col items-center rounded-2xl border border-zinc-200/80 bg-white/90 p-8 text-center shadow-md backdrop-blur-xl transition-all duration-300 hover:-translate-y-1.5 hover:shadow-xl dark:border-zinc-700/80 dark:bg-zinc-900/90 dark:hover:border-zinc-600"
                >
                    <span class="text-xs font-bold text-zinc-400 dark:text-zinc-400 mb-2">
                        STEP {{ step.number }}
                    </span>

                    <div class="mb-4 flex size-14 items-center justify-center rounded-2xl bg-red-50 text-[#ff545a] border border-red-100 dark:bg-zinc-800 dark:border-zinc-700 dark:text-red-400">
                        <component :is="step.icon" class="size-6 stroke-[2]" />
                    </div>

                    <h3 class="text-base font-bold text-zinc-900 dark:text-white font-['Poppins',sans-serif]">
                        {{ step.title }}
                    </h3>

                    <p class="mt-2 text-xs leading-relaxed text-zinc-600 dark:text-zinc-300 max-w-xs">
                        {{ step.description }}
                    </p>
                </div>
            </div>
        </div>
    </section>
</template>
