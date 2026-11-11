<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import {
    ArrowRight,
    CalendarCheck,
    Search,
} from '@lucide/vue';
import { useLanguage } from '@/composables/useLanguage';

const { t } = useLanguage();
</script>

<template>
    <section
        id="home"
        class="relative flex min-h-[580px] items-center justify-center overflow-hidden py-24 sm:py-32 lg:py-36"
    >
        <div
            class="relative z-10 mx-auto max-w-5xl px-4 text-center sm:px-6 lg:px-8"
        >
            <!-- Eyebrow Badge -->
            <div
                class="inline-flex items-center gap-2 rounded-full border border-white/20 bg-black/40 px-4 py-1.5 text-xs font-bold uppercase tracking-wider text-white backdrop-blur-md shadow-sm"
            >
                <span class="size-2 rounded-full bg-[#ff545a] animate-pulse"></span>
                <span>Tanzania's Event Marketplace</span>
            </div>

            <!-- Main Hero Typography in Rounded Smooth Bold Display Font -->
            <h1
                class="mt-6 font-display text-3xl leading-[1.14] font-black tracking-tight text-[#ff545a] sm:text-4xl md:text-5xl lg:text-6xl dark:text-white drop-shadow-sm"
            >
                {{ t('hero.title') }}
            </h1>

            <!-- Tagline reflecting full ecosystem -->
            <p
                class="mx-auto mt-5 max-w-3xl font-sans text-sm font-bold tracking-normal text-white sm:text-base md:text-lg dark:text-zinc-200"
            >
                {{ t('hero.description') }}
            </p>

            <!-- High-Impact Action CTAs -->
            <div
                class="mt-10 flex flex-col items-center justify-center gap-3.5 sm:flex-row"
            >
                <!-- Primary Action: Explore All Rentals & Services -->
                <Link
                    href="/explore"
                    class="group inline-flex w-full items-center justify-center gap-2.5 rounded-2xl bg-[#ff545a] px-8 py-4 text-sm font-extrabold text-white shadow-xl shadow-red-500/25 transition-all duration-300 hover:scale-[1.02] hover:bg-[#e0454b] hover:shadow-2xl hover:shadow-red-500/35 sm:w-auto"
                >
                    <Search class="size-4.5 stroke-[2.5]" />
                    <span>Explore Equipment & Services</span>
                    <ArrowRight class="size-4 transition-transform group-hover:translate-x-1" />
                </Link>

                <!-- Secondary Action: Plan My Event Booking -->
                <Link
                    href="/events/book"
                    class="inline-flex w-full items-center justify-center gap-2.5 rounded-2xl border border-white/30 bg-black/40 px-8 py-4 text-sm font-extrabold text-white shadow-lg backdrop-blur-md transition-all duration-300 hover:scale-[1.02] hover:border-white hover:bg-black/60 sm:w-auto dark:border-white/20 dark:bg-white/10 dark:hover:bg-white/20"
                >
                    <CalendarCheck class="size-4.5 text-[#ff545a]" />
                    <span>Plan My Event Checklist</span>
                </Link>
            </div>
        </div>
    </section>
</template>
