<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import AppLogoIcon from '@/components/AppLogoIcon.vue';

const page = usePage();
const name = page.props.name || 'HEY Events';
</script>

<template>
    <div class="flex items-center gap-2">
        <AppLogoIcon class="size-8 drop-shadow-xs" />
        <div class="grid flex-1 text-left text-sm">
            <span class="truncate font-black tracking-tight text-zinc-900 dark:text-white leading-tight font-sans">
                {{ name }}
            </span>
            <span class="text-[10px] uppercase tracking-widest text-zinc-400 dark:text-zinc-500 font-medium leading-none">
                Platform
            </span>
        </div>
    </div>
</template>
