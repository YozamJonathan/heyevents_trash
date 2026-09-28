<script setup lang="ts">
import { ref } from 'vue';

const reviews = [
    {
        name: 'Elena Rostova',
        role: 'Wedding Coordinator',
        eventType: 'Social & Celebrations',
        content: 'The interactive seating arrangement tool and digital RSVP dietary tracking saved us weeks of manual work. Our guests checked in seamlessly via QR code.'
    },
    {
        name: 'David Mwangi',
        role: 'Festival Director',
        eventType: 'Entertainment & Festivals',
        content: 'Multi-lane gate check-ins were completely flawless. Even when cell network dipped, offline sync kept lines moving with zero delay.'
    },
    {
        name: 'Sarah Jenkins',
        role: 'Corporate VP',
        eventType: 'Corporate & Business',
        content: 'Multi-track session scheduling, instant badge generation, and sponsor lead retrieval made our summit our highest rated conference ever.'
    }
];
</script>

<template>
    <section id="reviews" class="py-16 sm:py-20 bg-zinc-50/70 border-t border-zinc-200/60 dark:bg-zinc-900/30 dark:border-zinc-850">
        <div class="mx-auto max-w-7xl px-6 sm:px-8">
            <div class="max-w-xl">
                <span class="text-xs font-semibold tracking-wider uppercase text-[#ff545a]">
                    Trusted by Organizers
                </span>
                <h2 class="mt-2 text-2xl font-bold tracking-tight text-zinc-900 sm:text-3xl dark:text-white">
                    What hosts say about HEY Events.
                </h2>
            </div>

            <!-- Reviews Grid -->
            <div class="mt-10 grid grid-cols-1 gap-6 md:grid-cols-3">
                <div
                    v-for="(rev, idx) in reviews"
                    :key="idx"
                    class="flex flex-col justify-between rounded-2xl border border-zinc-200/70 bg-white p-6 shadow-xs dark:border-zinc-800 dark:bg-zinc-900"
                >
                    <p class="text-sm leading-relaxed text-zinc-600 dark:text-zinc-300">
                        "{{ rev.content }}"
                    </p>

                    <div class="mt-6 border-t border-zinc-100 pt-4 dark:border-zinc-800 flex items-center justify-between">
                        <div>
                            <div class="text-xs font-bold text-zinc-900 dark:text-white">
                                {{ rev.name }}
                            </div>
                            <div class="text-[11px] text-zinc-400">
                                {{ rev.role }}
                            </div>
                        </div>
                        <span class="rounded-md bg-zinc-100 px-2 py-0.5 text-[10px] font-medium text-zinc-600 dark:bg-zinc-800 dark:text-zinc-400">
                            {{ rev.eventType }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>
