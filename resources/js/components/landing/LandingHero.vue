<script setup lang="ts">
import { ref } from 'vue';
import { Search, MapPin, SlidersHorizontal } from '@lucide/vue';
import { useLanguage } from '@/composables/useLanguage';

const searchQuery = ref('');
const locationQuery = ref('');
const { t } = useLanguage();

const handleSearch = () => {
    const exploreSection = document.getElementById('explore');
    if (exploreSection) {
        exploreSection.scrollIntoView({ behavior: 'smooth' });
    }
};
</script>

<template>
    <section
        id="home"
        class="relative flex min-h-[580px] items-center justify-center overflow-hidden py-20 sm:py-28 lg:py-32"
    >
        <div
            class="relative z-10 mx-auto max-w-5xl px-4 pb-10 text-center sm:px-6 sm:pb-14 lg:px-8"
        >
            <!-- Main Hero Typography -->
            <h1
                class="font-sans text-3xl leading-tight font-black tracking-wider text-zinc-950 uppercase drop-shadow-xs sm:text-4xl md:text-5xl lg:text-[46px] dark:text-white dark:drop-shadow-md"
            >
                {{ t('hero.title') }}
            </h1>

            <!-- Tagline reflecting the 4 standards -->
            <p
                class="mx-auto mt-4 max-w-3xl text-sm font-medium text-zinc-700 sm:text-base md:text-lg dark:text-zinc-200"
            >
                {{ t('hero.description') }}
            </p>

            <!-- Floating Multi-segment Glass Search Bar -->
            <div
                class="mx-auto mt-10 max-w-4xl rounded-2xl border border-zinc-200/90 bg-white/95 p-2.5 shadow-2xl backdrop-blur-2xl transition-all dark:border-white/15 dark:bg-zinc-900/95"
            >
                <form
                    @submit.prevent="handleSearch"
                    class="flex flex-col gap-2 md:flex-row md:items-center"
                >
                    <!-- Segment 1: What? (Event or Category) -->
                    <div
                        class="group flex flex-1 items-center gap-3 rounded-xl px-3.5 py-2.5 transition hover:bg-zinc-50 dark:hover:bg-zinc-800/50"
                    >
                        <div
                            class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-zinc-100 text-zinc-700 transition group-hover:bg-red-50 group-hover:text-[#ff545a] dark:bg-zinc-800 dark:text-zinc-300 dark:group-hover:bg-zinc-700 dark:group-hover:text-red-400"
                        >
                            <SlidersHorizontal class="size-4.5" />
                        </div>
                        <div class="min-w-0 flex-1 text-left">
                            <label
                                class="block text-[10px] font-bold tracking-wider text-zinc-500 uppercase dark:text-zinc-400"
                            >
                                {{ t('hero.eventLabel') }}
                            </label>
                            <input
                                v-model="searchQuery"
                                type="text"
                                :placeholder="t('hero.eventPlaceholder')"
                                class="w-full truncate border-0 bg-transparent p-0 text-sm font-semibold text-zinc-900 placeholder-zinc-400 focus:ring-0 focus:outline-none dark:text-white dark:placeholder-zinc-500"
                            />
                        </div>
                    </div>

                    <!-- Clean Vertical Divider (Desktop) -->
                    <div
                        class="hidden h-9 w-px bg-zinc-200 md:block dark:bg-zinc-800"
                    ></div>

                    <!-- Segment 2: Location -->
                    <div
                        class="group flex flex-1 items-center gap-3 rounded-xl px-3.5 py-2.5 transition hover:bg-zinc-50 dark:hover:bg-zinc-800/50"
                    >
                        <div
                            class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-zinc-100 text-zinc-700 transition group-hover:bg-red-50 group-hover:text-[#ff545a] dark:bg-zinc-800 dark:text-zinc-300 dark:group-hover:bg-zinc-700 dark:group-hover:text-red-400"
                        >
                            <MapPin class="size-4.5" />
                        </div>
                        <div class="min-w-0 flex-1 text-left">
                            <label
                                class="block text-[10px] font-bold tracking-wider text-zinc-500 uppercase dark:text-zinc-400"
                            >
                                {{ t('hero.locationLabel') }}
                            </label>
                            <input
                                v-model="locationQuery"
                                type="text"
                                :placeholder="t('hero.locationPlaceholder')"
                                class="w-full truncate border-0 bg-transparent p-0 text-sm font-semibold text-zinc-900 placeholder-zinc-400 focus:ring-0 focus:outline-none dark:text-white dark:placeholder-zinc-500"
                            />
                        </div>
                    </div>

                    <!-- Action: Red Search Button (#ff545a) -->
                    <button
                        type="submit"
                        class="inline-flex shrink-0 items-center justify-center gap-2 rounded-xl bg-[#ff545a] px-7 py-3.5 text-xs font-bold tracking-wider text-white uppercase shadow-md transition-all hover:bg-[#e0454b] hover:shadow-lg focus:ring-2 focus:ring-[#ff545a] focus:ring-offset-2 focus:outline-none md:w-auto"
                    >
                        <Search class="size-4 stroke-[2.5]" />
                        <span>{{ t('hero.search') }}</span>
                    </button>
                </form>
            </div>
        </div>
    </section>
</template>
