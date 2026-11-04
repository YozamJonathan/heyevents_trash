<script setup lang="ts">
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import AppLogoIcon from '@/components/AppLogoIcon.vue';

const page = usePage();
const appName = computed(() => {
    const raw = String(page.props.name ?? '').trim();
    return raw && raw.toLowerCase() !== 'laravel' ? raw : 'HEY Events';
});
</script>

<template>
    <div class="flex items-center gap-2">
        <AppLogoIcon class="size-8 drop-shadow-xs" />
        <div class="grid flex-1 text-left text-sm">
            <span class="truncate font-black tracking-tight text-zinc-900 dark:text-white leading-tight font-sans">
                <template v-if="appName === 'HEY Events'">
                    HEY <span class="text-[#ff545a]">Events</span>
                </template>
                <template v-else>
                    {{ appName }}
                </template>
            </span>
            <span class="text-[10px] uppercase tracking-widest text-zinc-400 dark:text-zinc-500 font-semibold leading-none">
                Platform
            </span>
        </div>
    </div>
</template>
