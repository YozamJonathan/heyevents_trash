<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import { useLanguage, type TranslationKey } from '@/composables/useLanguage';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { home } from '@/routes';

const props = defineProps<{
    title?: TranslationKey;
    description?: TranslationKey;
}>();

const { t } = useLanguage();
</script>

<template>
    <div
        class="bg-muted flex min-h-svh flex-col items-center justify-center gap-6 p-6 md:p-10"
    >
        <div class="flex w-full max-w-md flex-col gap-6">
            <Link
                :href="home()"
                class="flex items-center gap-2 self-center font-medium transition-transform hover:scale-105"
            >
                <AppLogoIcon class="size-10 drop-shadow-sm" />
            </Link>

            <div class="flex flex-col gap-6">
                <Card class="rounded-xl">
                    <CardHeader class="px-10 pt-8 pb-0 text-center">
                        <CardTitle class="text-xl">{{
                            props.title ? t(props.title) : ''
                        }}</CardTitle>
                        <CardDescription>
                            {{ props.description ? t(props.description) : '' }}
                        </CardDescription>
                    </CardHeader>
                    <CardContent class="px-10 py-8">
                        <slot />
                    </CardContent>
                </Card>
            </div>
        </div>
    </div>
</template>
