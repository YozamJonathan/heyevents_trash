<script setup lang="ts">
import { Camera, MessageCircle, Video, Globe, FileText } from '@lucide/vue';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import InputError from '@/components/InputError.vue';
import { useLanguage } from '@/composables/useLanguage';

defineProps<{
    form: any;
    stepErrors: Record<string, string>;
}>();

const { t } = useLanguage();
</script>

<template>
    <div class="space-y-4">
        <div>
            <h3 class="text-xs font-bold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">
                {{ t('onboarding.socialChannelsTitle') }}
            </h3>
            <p class="text-[11px] text-zinc-500 dark:text-zinc-400">
                {{ t('onboarding.socialChannelsSubtitle') }}
            </p>
        </div>

        <div class="grid gap-3 sm:grid-cols-2">
            <!-- Instagram -->
            <div class="grid gap-1.5 min-w-0">
                <Label for="instagram" class="flex items-center gap-1.5 text-xs font-semibold">
                    <Camera class="size-3.5 text-pink-500" />
                    <span>{{ t('onboarding.instagram') }}</span>
                </Label>
                <Input
                    id="instagram"
                    v-model="form.instagram"
                    :placeholder="t('onboarding.instagramPlaceholder')"
                    class="h-9.5 text-xs rounded-xl"
                />
                <InputError :message="stepErrors.instagram || form.errors.instagram" />
            </div>

            <!-- WhatsApp -->
            <div class="grid gap-1.5 min-w-0">
                <Label for="whatsapp" class="flex items-center gap-1.5 text-xs font-semibold">
                    <MessageCircle class="size-3.5 text-emerald-500" />
                    <span>{{ t('onboarding.whatsapp') }}</span>
                </Label>
                <Input
                    id="whatsapp"
                    v-model="form.whatsapp"
                    :placeholder="t('onboarding.whatsappPlaceholder')"
                    class="h-9.5 text-xs rounded-xl"
                />
                <InputError :message="stepErrors.whatsapp || form.errors.whatsapp" />
            </div>

            <!-- TikTok -->
            <div class="grid gap-1.5 min-w-0">
                <Label for="tiktok" class="flex items-center gap-1.5 text-xs font-semibold">
                    <Video class="size-3.5 text-indigo-500" />
                    <span>{{ t('onboarding.tiktok') }}</span>
                </Label>
                <Input
                    id="tiktok"
                    v-model="form.tiktok"
                    :placeholder="t('onboarding.tiktokPlaceholder')"
                    class="h-9.5 text-xs rounded-xl"
                />
                <InputError :message="stepErrors.tiktok || form.errors.tiktok" />
            </div>

            <!-- Website / Secondary Portfolio Link -->
            <div class="grid gap-1.5 min-w-0">
                <Label for="social_media" class="flex items-center gap-1.5 text-xs font-semibold">
                    <Globe class="size-3.5 text-blue-500" />
                    <span>{{ t('onboarding.website') }}</span>
                </Label>
                <Input
                    id="social_media"
                    v-model="form.social_media"
                    :placeholder="t('onboarding.websitePlaceholder')"
                    class="h-9.5 text-xs rounded-xl"
                />
                <InputError :message="stepErrors.social_media || form.errors.social_media" />
            </div>
        </div>

        <!-- Business Description -->
        <div class="space-y-1.5 pt-2 border-t border-zinc-200/60 dark:border-zinc-800/80">
            <Label for="description" class="flex items-center gap-1.5 text-xs font-semibold">
                <FileText class="size-3.5 text-[#ff545a]" />
                <span>{{ t('onboarding.description') }}</span>
            </Label>
            <textarea
                id="description"
                v-model="form.description"
                rows="3"
                :placeholder="t('onboarding.descriptionPlaceholder')"
                class="w-full rounded-xl border border-zinc-200 bg-background p-2.5 text-xs text-foreground shadow-xs dark:border-zinc-800 focus:outline-none focus:ring-2 focus:ring-[#ff545a]/30 resize-none"
            ></textarea>
            <InputError :message="stepErrors.description || form.errors.description" />
        </div>
    </div>
</template>
