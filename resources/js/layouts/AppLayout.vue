<script setup lang="ts">
import AppLayout from '@/layouts/app/AppSidebarLayout.vue';
import PwaBanner from '@/components/pwa/PwaBanner.vue';
import type { BreadcrumbItem } from '@/types';

const { breadcrumbs = [] } = defineProps<{
    breadcrumbs?: BreadcrumbItem[];
}>();
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <slot />
        <PwaBanner />
    </AppLayout>
</template>
