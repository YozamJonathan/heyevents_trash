<script setup lang="ts">
import AuthLayout from '@/layouts/auth/AuthCardLayout.vue';
import PwaBanner from '@/components/pwa/PwaBanner.vue';

const { title = '', description = '' } = defineProps<{
    title?: string;
    description?: string;
}>();
</script>

<template>
    <AuthLayout :title="title" :description="description">
        <slot />
        <PwaBanner />
    </AuthLayout>
</template>
