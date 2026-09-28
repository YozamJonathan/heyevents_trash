<script setup lang="ts">
import type { HTMLAttributes } from 'vue';

defineOptions({
    inheritAttrs: false,
});

type Props = {
    className?: HTMLAttributes['class'];
};

defineProps<Props>();
</script>

<template>
    <div
        :class="[
            'relative inline-flex shrink-0 items-center justify-center overflow-hidden rounded-full',
            className
        ]"
        v-bind="$attrs"
    >
        <!-- Light Mode Medallion Logo -->
        <img
            src="/images/logo-light.png"
            alt="HEY Events Logo"
            class="h-full w-full object-contain dark:hidden"
        />
        <!-- Dark Mode Medallion Logo -->
        <img
            src="/images/logo-dark.png"
            alt="HEY Events Logo"
            class="h-full w-full object-contain hidden dark:block"
        />
    </div>
</template>

