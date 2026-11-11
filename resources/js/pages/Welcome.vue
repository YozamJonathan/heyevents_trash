<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import LandingNavbar from '@/components/landing/LandingNavbar.vue';
import LandingHero from '@/components/landing/LandingHero.vue';
import LandingCategoryCards from '@/components/landing/LandingCategoryCards.vue';
import LandingHowItWorks from '@/components/landing/LandingHowItWorks.vue';
import LandingAbout from '@/components/landing/LandingAbout.vue';
import LandingReviews from '@/components/landing/LandingReviews.vue';
import LandingFooter from '@/components/landing/LandingFooter.vue';
import PwaBanner from '@/components/pwa/PwaBanner.vue';
import WhatsAppCornerButton from '@/components/WhatsAppCornerButton.vue';
import { useLanguage } from '@/composables/useLanguage';

const { t } = useLanguage();
</script>

<template>
    <Head :title="t('page.title')" />

    <div
        class="relative min-h-screen bg-slate-100 font-sans text-zinc-900 antialiased selection:bg-[#ff545a] selection:text-white dark:bg-zinc-950 dark:text-zinc-100"
    >
        <!-- Persistent Background Layer (Dar es Salaam Day for Light Mode, Dar es Salaam Night for Dark Mode) -->
        <div class="pointer-events-none fixed inset-0 z-0 overflow-hidden">
            <img
                src="/images/hero-dar-day.jpg?v=2"
                :alt="t('page.cityAlt')"
                class="h-full w-full object-cover object-[center_25%] brightness-100 contrast-[1.05] dark:hidden"
            />
            <img
                src="/images/hero-dar-night.jpg?v=2"
                :alt="t('page.cityAlt')"
                class="hidden h-full w-full object-cover object-[center_25%] brightness-100 contrast-[1.05] dark:block"
            />
            <!-- Atmospheric Contrast Layer: minimal top vignette so the day image remains 100% crystal clear and crisp -->
            <div
                class="absolute inset-0 bg-gradient-to-b from-black/10 via-transparent to-slate-100/90 dark:from-zinc-950/60 dark:via-zinc-950/30 dark:to-zinc-950/90"
            ></div>
        </div>

        <div class="relative z-10 flex min-h-screen flex-col">
            <!-- Main Header Navbar with HEY Events Monogram and Theme Switcher -->
            <LandingNavbar />

            <main class="flex-1">
                <!-- Clean Hero Section -->
                <LandingHero />

                <!-- The 4 Core Standards (Social, Corporate, Entertainment, Community) -->
                <LandingCategoryCards />

                <!-- Streamlined How It Works -->
                <LandingHowItWorks />

                <!-- About Us Section: What is Hey Events and the Ecosystem -->
                <LandingAbout />

                <!-- Organizer Reviews -->
                <LandingReviews />
            </main>

            <!-- Minimalist Clean Footer -->
            <LandingFooter />
        </div>

        <!-- Floating Corner WhatsApp Support -->
        <WhatsAppCornerButton />

        <!-- PWA Status and Install Notification -->
        <PwaBanner />
    </div>
</template>
