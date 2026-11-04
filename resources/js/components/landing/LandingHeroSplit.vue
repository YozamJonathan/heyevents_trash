<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { register, dashboard } from '@/routes';
import { ArrowRight, QrCode, DollarSign, CheckCircle2, MessageSquare, ShieldCheck, Users } from '@lucide/vue';

const page = usePage();
</script>

<template>
    <section class="relative overflow-hidden pt-12 pb-20 lg:pt-20 lg:pb-28">
        <!-- Ambient Light Gradient -->
        <div class="pointer-events-none absolute -top-40 left-1/2 -z-10 h-[500px] w-[850px] -translate-x-1/2 rounded-full bg-gradient-to-b from-indigo-500/10 via-purple-500/5 to-transparent blur-3xl" />

        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <!-- Headline & Subtitle -->
            <div class="mx-auto max-w-4xl text-center">
                <div class="inline-flex items-center gap-2 rounded-full border border-indigo-200/80 bg-indigo-50/80 px-4 py-1.5 text-xs font-semibold text-indigo-700 backdrop-blur-sm dark:border-indigo-900/60 dark:bg-indigo-950/40 dark:text-indigo-300">
                    <ShieldCheck class="h-3.5 w-3.5" />
                    <span>Complete Event Infrastructure</span>
                </div>

                <h1 class="mt-6 text-4xl font-black tracking-tight text-zinc-950 sm:text-5xl lg:text-6xl dark:text-white">
                    From Budgeting to Gate Check-In —
                    <span class="block bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600 bg-clip-text text-transparent">
                        Manage Any Event End-To-End.
                    </span>
                </h1>

                <p class="mx-auto mt-5 max-w-2xl text-base font-medium leading-relaxed text-zinc-600 sm:text-lg dark:text-zinc-300">
                    The all-in-one platform for organizers, venues, and teams to
                    <strong class="font-semibold text-zinc-900 dark:text-white">plan, market, execute, and track</strong>
                    events of any scale.
                </p>

                <div class="mt-8 flex flex-col items-center justify-center gap-4 sm:flex-row">
                    <Link
                        :href="page.props.auth?.user ? dashboard() : register()"
                        class="inline-flex items-center gap-2 rounded-full bg-zinc-950 px-8 py-3.5 text-sm font-semibold text-white shadow-md transition-all hover:bg-zinc-800 hover:shadow-lg active:scale-95 dark:bg-white dark:text-zinc-950 dark:hover:bg-zinc-100"
                    >
                        <span>{{ page.props.auth?.user ? 'Open Dashboard' : 'Start Planning Free' }}</span>
                        <ArrowRight class="h-4 w-4" />
                    </Link>
                    <a
                        href="#hubs"
                        class="inline-flex items-center gap-2 rounded-full border border-zinc-200 bg-white px-7 py-3.5 text-sm font-medium text-zinc-700 transition-all hover:border-zinc-300 hover:bg-zinc-50 dark:border-zinc-800 dark:bg-zinc-900 dark:text-zinc-300 dark:hover:bg-zinc-800"
                    >
                        <span>Explore 4 Event Hubs</span>
                    </a>
                </div>
            </div>

            <!-- Split-Hero Visuals: Both Sides of the System -->
            <div class="mt-14 grid items-center gap-8 lg:grid-cols-12">
                <!-- Left: The Organizer Operating System (Desktop Preview) -->
                <div class="rounded-2xl border border-zinc-200/90 bg-white/90 p-5 shadow-xl backdrop-blur-sm dark:border-zinc-800 dark:bg-zinc-900/90 lg:col-span-7">
                    <div class="flex items-center justify-between border-b border-zinc-100 pb-3 dark:border-zinc-800">
                        <div class="flex items-center gap-2">
                            <div class="h-3 w-3 rounded-full bg-rose-400"></div>
                            <div class="h-3 w-3 rounded-full bg-amber-400"></div>
                            <div class="h-3 w-3 rounded-full bg-emerald-400"></div>
                            <span class="ml-2 text-xs font-bold text-zinc-700 dark:text-zinc-300">Organizer Console • eSherehe OS</span>
                        </div>
                        <span class="rounded bg-emerald-50 px-2 py-0.5 text-[10px] font-bold text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300">LIVE SYNC</span>
                    </div>

                    <!-- Mini metrics row -->
                    <div class="mt-4 grid grid-cols-3 gap-3">
                        <div class="rounded-xl bg-zinc-50 p-3 dark:bg-zinc-800/60">
                            <div class="text-[10px] text-zinc-400 uppercase font-semibold">Budget Spend</div>
                            <div class="text-base font-bold text-zinc-900 dark:text-white">TZS 38.4M <span class="text-[10px] font-normal text-zinc-400">/ 45M</span></div>
                        </div>
                        <div class="rounded-xl bg-zinc-50 p-3 dark:bg-zinc-800/60">
                            <div class="text-[10px] text-zinc-400 uppercase font-semibold">Gate Check-in</div>
                            <div class="text-base font-bold text-emerald-600 dark:text-emerald-400">842 <span class="text-[10px] font-normal text-zinc-400">/ 900</span></div>
                        </div>
                        <div class="rounded-xl bg-zinc-50 p-3 dark:bg-zinc-800/60">
                            <div class="text-[10px] text-zinc-400 uppercase font-semibold">Vendors Ready</div>
                            <div class="text-base font-bold text-indigo-600 dark:text-indigo-400">6 of 6</div>
                        </div>
                    </div>

                    <!-- Interactive Vendor & Seating snapshot -->
                    <div class="mt-3 space-y-2 text-xs">
                        <div class="flex items-center justify-between rounded-lg border border-zinc-100 p-2.5 dark:border-zinc-800">
                            <span class="font-medium text-zinc-800 dark:text-zinc-200">Stage Sound & Lighting (ProAudio Ltd)</span>
                            <span class="rounded bg-emerald-100 px-2 py-0.5 text-[10px] font-semibold text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300">Contract Signed</span>
                        </div>
                        <div class="flex items-center justify-between rounded-lg border border-zinc-100 p-2.5 dark:border-zinc-800">
                            <span class="font-medium text-zinc-800 dark:text-zinc-200">Main Hall Seating Chart (Tables 1–40)</span>
                            <span class="rounded bg-indigo-100 px-2 py-0.5 text-[10px] font-semibold text-indigo-800 dark:bg-indigo-950 dark:text-indigo-300">Seated 95%</span>
                        </div>
                    </div>
                </div>

                <!-- Right: The Guest Experience (Smartphone Mockup) -->
                <div class="mx-auto w-full max-w-xs rounded-3xl border-4 border-zinc-900 bg-zinc-950 p-3 shadow-2xl dark:border-zinc-700 lg:col-span-5">
                    <div class="rounded-2xl bg-white p-4 text-zinc-900 dark:bg-zinc-900 dark:text-white">
                        <!-- Notification banner -->
                        <div class="flex items-center gap-2 rounded-xl bg-emerald-50 p-2 text-xs text-emerald-800 dark:bg-emerald-950/80 dark:text-emerald-300">
                            <MessageSquare class="h-3.5 w-3.5 text-emerald-600" />
                            <span class="text-[11px] font-semibold">WhatsApp Pass Delivered</span>
                        </div>

                        <!-- Guest Ticket -->
                        <div class="mt-3 rounded-xl border border-zinc-200 p-3 text-center dark:border-zinc-800">
                            <span class="rounded-full bg-zinc-100 px-2 py-0.5 text-[10px] font-bold text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300">VIP GUEST PASS</span>
                            <h4 class="mt-1 text-sm font-black">Mwangi & Sarah Wedding</h4>
                            <p class="text-[10px] text-zinc-400">Table 04 • Gate North</p>

                            <div class="my-3 flex justify-center">
                                <div class="rounded-lg bg-zinc-100 p-2 dark:bg-zinc-800">
                                    <QrCode class="h-16 w-16 text-zinc-900 dark:text-white" />
                                </div>
                            </div>
                            <div class="text-[10px] font-mono font-bold text-emerald-600 dark:text-emerald-400">● 0.5s FAST-SCAN READY</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>
