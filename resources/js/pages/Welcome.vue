<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import LandingNavbar from '@/components/landing/LandingNavbar.vue';
import LandingHero from '@/components/landing/LandingHero.vue';
import LandingCategoryCards from '@/components/landing/LandingCategoryCards.vue';
import LandingHowItWorks from '@/components/landing/LandingHowItWorks.vue';
import LandingExplore from '@/components/landing/LandingExplore.vue';
import LandingReviews from '@/components/landing/LandingReviews.vue';
import LandingFooter from '@/components/landing/LandingFooter.vue';
import PwaBanner from '@/components/pwa/PwaBanner.vue';
import { useLanguage } from '@/composables/useLanguage';

const { t } = useLanguage();
</script>

<template>
    <Head :title="t('page.title')">
        <link rel="preconnect" href="https://fonts.googleapis.com" />
        <link
            rel="preconnect"
            href="https://fonts.gstatic.com"
            crossorigin="anonymous"
        />
        <link
            href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap"
            rel="stylesheet"
        />
    </Head>

    <div
        class="relative min-h-screen bg-slate-100 font-['Poppins',sans-serif] text-zinc-900 antialiased selection:bg-[#ff545a] selection:text-white dark:bg-zinc-950 dark:text-zinc-100"
    >
        <!-- Persistent Background Layer (City Day for Light Mode, City Night for Dark Mode) -->
        <div class="pointer-events-none fixed inset-0 z-0 overflow-hidden">
            <img
                src="/images/hero-city-day.jpg"
                :alt="t('page.cityAlt')"
                class="h-full w-full object-cover object-center dark:hidden"
            />
            <img
                src="/images/hero-city-night.jpg"
                :alt="t('page.cityAlt')"
                class="hidden h-full w-full object-cover object-center dark:block"
            />
            <!-- Atmospheric Contrast Layer: balanced so skyline & lights are clearly visible while text has high readability -->
            <div
                class="absolute inset-0 bg-white/45 backdrop-blur-[0.5px] dark:bg-black/55"
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

                <!-- Clean Explore Section -->
                <LandingExplore />

                <!-- Organizer Reviews -->
                <LandingReviews />
            </main>

            <!-- Minimalist Clean Footer -->
            <LandingFooter />
        </div>

        <!-- PWA Status and Install Notification -->
        <PwaBanner />
    </div>
</template>
