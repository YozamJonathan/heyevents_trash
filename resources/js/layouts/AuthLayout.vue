<script setup lang="ts">
import AuthLayout from '@/layouts/auth/AuthCardLayout.vue';
import PwaBanner from '@/components/pwa/PwaBanner.vue';
import type { TranslationKey } from '@/composables/useLanguage';

const { title, description, wide } = defineProps<{
    title?: TranslationKey;
    description?: TranslationKey;
    wide?: boolean;
}>();
</script>

<template>
    <AuthLayout :title="title" :description="description" :wide="wide">
        <slot />
        <PwaBanner />
    </AuthLayout>
</template>
