<script setup lang="ts">
import { useLandingData } from '@/composables/useLandingData';
import { Check, QrCode, Download, DollarSign, Ticket, BarChart2 } from '@lucide/vue';

const { activePipelinePhase, pipelinePhases, currentPipeline } = useLandingData();
</script>

<template>
    <section id="pipeline" class="border-t border-zinc-100 bg-zinc-50/50 py-16 lg:py-24 dark:border-zinc-800/80 dark:bg-zinc-950">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="mx-auto max-w-3xl text-center">
                <p class="text-xs font-semibold tracking-widest text-indigo-600 uppercase dark:text-indigo-400">Actionable Modules</p>
                <h2 class="mt-2 text-3xl font-bold tracking-tight text-zinc-950 sm:text-4xl dark:text-white">
                    An Integrated End-to-End Pipeline
                </h2>
                <p class="mt-3 text-sm text-zinc-600 sm:text-base dark:text-zinc-400">
                    Execute each operational phase with dedicated, production-ready modules.
                </p>
            </div>

            <!-- Pipeline Step Buttons -->
            <div class="mt-10 flex flex-wrap justify-center gap-2">
                <button
                    v-for="phase in pipelinePhases"
                    :key="phase.id"
                    @click="activePipelinePhase = phase.id"
                    :class="[
                        'flex items-center gap-2 rounded-full px-5 py-2.5 text-xs font-bold transition-all duration-200',
                        activePipelinePhase === phase.id
                            ? 'bg-zinc-950 text-white shadow-md dark:bg-white dark:text-zinc-950'
                            : 'border border-zinc-200 bg-white text-zinc-600 hover:border-zinc-300 dark:border-zinc-800 dark:bg-zinc-900 dark:text-zinc-400'
                    ]"
                >
                    <component :is="phase.icon" class="h-3.5 w-3.5" />
                    <span>{{ phase.step }}</span>
                </button>
            </div>

            <!-- Pipeline Live Module Preview Box -->
            <div class="mt-8 rounded-3xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 lg:p-10">
                <Transition name="fade" mode="out-in">
                    <div :key="currentPipeline.id" class="grid items-center gap-8 lg:grid-cols-12">
                        <!-- Module Details -->
                        <div class="space-y-4 lg:col-span-6">
                            <span class="rounded-md bg-indigo-50 px-2.5 py-1 text-xs font-bold text-indigo-700 dark:bg-indigo-950 dark:text-indigo-300">
                                {{ currentPipeline.step }} Module
                            </span>
                            <h3 class="text-2xl font-bold text-zinc-950 dark:text-white">{{ currentPipeline.title }}</h3>
                            <p class="text-sm leading-relaxed text-zinc-600 dark:text-zinc-400">{{ currentPipeline.subtitle }}</p>

                            <div class="space-y-2 pt-2">
                                <div
                                    v-for="(h, idx) in currentPipeline.highlights"
                                    :key="idx"
                                    class="flex items-center gap-2 text-xs font-medium text-zinc-700 dark:text-zinc-300"
                                >
                                    <Check class="h-3.5 w-3.5 text-emerald-500" />
                                    <span>{{ h }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Actionable Visual Representation -->
                        <div class="lg:col-span-6">
                            <!-- Visual 01: Budget Estimator -->
                            <div v-if="currentPipeline.moduleType === 'budget'" class="rounded-2xl border border-zinc-100 bg-zinc-50 p-5 dark:border-zinc-800 dark:bg-zinc-950">
                                <div class="flex justify-between text-xs font-bold">
                                    <span>Budget Allocation vs Spend</span>
                                    <span class="text-emerald-600">$38,400 of $45,000</span>
                                </div>
                                <div class="mt-3 space-y-2 text-xs">
                                    <div class="flex justify-between rounded-lg bg-white p-2.5 dark:bg-zinc-900">
                                        <span>Venue & Decor</span>
                                        <span class="font-mono font-bold">$18,000 / $20,000</span>
                                    </div>
                                    <div class="flex justify-between rounded-lg bg-white p-2.5 dark:bg-zinc-900">
                                        <span>Catering & Drinks</span>
                                        <span class="font-mono font-bold">$12,400 / $15,000</span>
                                    </div>
                                    <div class="flex justify-between rounded-lg bg-white p-2.5 dark:bg-zinc-900">
                                        <span>Sound & Lighting</span>
                                        <span class="font-mono font-bold">$8,000 / $10,000</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Visual 02: Ticket Tier Builder -->
                            <div v-else-if="currentPipeline.moduleType === 'ticket'" class="rounded-2xl border border-zinc-100 bg-zinc-50 p-5 dark:border-zinc-800 dark:bg-zinc-950">
                                <div class="text-xs font-bold">Active Ticket Tiers & Pricing</div>
                                <div class="mt-3 space-y-2 text-xs">
                                    <div class="flex items-center justify-between rounded-lg bg-white p-2.5 dark:bg-zinc-900">
                                        <div><strong>Early Bird Pass</strong><p class="text-[10px] text-zinc-400">Sold Out (500/500)</p></div>
                                        <span class="font-bold text-emerald-600">$25.00</span>
                                    </div>
                                    <div class="flex items-center justify-between rounded-lg bg-white p-2.5 dark:bg-zinc-900">
                                        <div><strong>Standard Admission</strong><p class="text-[10px] text-zinc-400">Available (1,240 sold)</p></div>
                                        <span class="font-bold">$40.00</span>
                                    </div>
                                    <div class="flex items-center justify-between rounded-lg bg-white p-2.5 dark:bg-zinc-900">
                                        <div><strong>VIP Table Package</strong><p class="text-[10px] text-zinc-400">Includes Dinner & Valet</p></div>
                                        <span class="font-bold text-indigo-600">$150.00</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Visual 03: Fast QR Scanner App -->
                            <div v-else-if="currentPipeline.moduleType === 'scanner'" class="mx-auto max-w-xs rounded-2xl border border-zinc-100 bg-zinc-950 p-4 text-white dark:border-zinc-800">
                                <div class="flex items-center justify-between text-xs">
                                    <span class="font-bold text-emerald-400">● 0.5s Scanner Active</span>
                                    <span class="rounded bg-zinc-800 px-2 py-0.5 text-[10px]">Gate 02</span>
                                </div>
                                <div class="my-4 flex flex-col items-center justify-center rounded-xl border border-dashed border-emerald-500/50 bg-emerald-950/20 py-6 text-center">
                                    <QrCode class="h-12 w-12 text-emerald-400" />
                                    <span class="mt-2 text-xs font-bold text-emerald-300">PASS VERIFIED • VALID</span>
                                </div>
                                <div class="text-center text-[10px] text-zinc-400">Jane Doe (VIP Guest #418) • Table 04</div>
                            </div>

                            <!-- Visual 04: Analytics & Export Reports -->
                            <div v-else class="rounded-2xl border border-zinc-100 bg-zinc-50 p-5 dark:border-zinc-800 dark:bg-zinc-950">
                                <div class="flex items-center justify-between text-xs font-bold">
                                    <span>Revenue & Attendance Audits</span>
                                    <button class="inline-flex items-center gap-1 rounded bg-zinc-900 px-2 py-1 text-[10px] text-white dark:bg-white dark:text-zinc-950">
                                        <Download class="h-2.5 w-2.5" /> Export PDF/CSV
                                    </button>
                                </div>
                                <div class="mt-3 grid grid-cols-2 gap-2 text-xs">
                                    <div class="rounded-lg bg-white p-3 dark:bg-zinc-900">
                                        <div class="text-[10px] text-zinc-400">Gross Ticket Sales</div>
                                        <div class="text-base font-bold text-zinc-900 dark:text-white">$64,500</div>
                                    </div>
                                    <div class="rounded-lg bg-white p-3 dark:bg-zinc-900">
                                        <div class="text-[10px] text-zinc-400">Attendance Rate</div>
                                        <div class="text-base font-bold text-emerald-600">96.8%</div>
                                    </div>
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
