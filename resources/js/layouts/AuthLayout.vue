<script setup lang="ts">
import AuthLayout from '@/layouts/auth/AuthCardLayout.vue';
import PwaBanner from '@/components/pwa/PwaBanner.vue';
import type { TranslationKey } from '@/composables/useLanguage';

const { title, description } = defineProps<{
    title?: TranslationKey;
    description?: TranslationKey;
}>();
</script>

<template>
    <AuthLayout :title="title" :description="description">
        <slot />
        <PwaBanner />
    </AuthLayout>
</template>
