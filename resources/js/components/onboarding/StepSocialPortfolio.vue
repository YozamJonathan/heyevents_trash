<script setup lang="ts">
import { computed } from 'vue';
import { Camera, MessageCircle, Video, Globe, FileText, TriangleAlert } from '@lucide/vue';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { useLanguage, type TranslationKey } from '@/composables/useLanguage';
import { DESCRIPTION_MAX_LENGTH } from '@/lib/providerRegistrationValidation';

const props = defineProps<{
    form: any;
    stepErrors: Record<string, string>;
}>();

const emit = defineEmits<{
    (e: 'field-blur', field: string): void;
    (e: 'field-input', field: string): void;
}>();

const { t } = useLanguage();

type SocialField = {
    id: 'instagram' | 'whatsapp' | 'tiktok' | 'social_media';
    icon: typeof Camera;
    iconClass: string;
    label: TranslationKey;
    placeholder: TranslationKey;
    hint: TranslationKey;
    type: string;
    inputmode?: 'text' | 'url' | 'tel';
};

const fields: SocialField[] = [
    {
        id: 'instagram',
        icon: Camera,
        iconClass: 'text-pink-500',
        label: 'onboarding.instagram',
        placeholder: 'onboarding.instagramPlaceholder',
        hint: 'onboarding.instagramHint',
        type: 'text',
    },
    {
        id: 'whatsapp',
        icon: MessageCircle,
        iconClass: 'text-emerald-500',
        label: 'onboarding.whatsapp',
        placeholder: 'onboarding.whatsappPlaceholder',
        hint: 'onboarding.whatsappHint',
        type: 'text',
        inputmode: 'tel',
    },
    {
        id: 'tiktok',
        icon: Video,
        iconClass: 'text-indigo-500',
        label: 'onboarding.tiktok',
        placeholder: 'onboarding.tiktokPlaceholder',
        hint: 'onboarding.tiktokHint',
        type: 'text',
    },
    {
        id: 'social_media',
        icon: Globe,
        iconClass: 'text-blue-500',
        label: 'onboarding.website',
        placeholder: 'onboarding.websitePlaceholder',
        hint: 'onboarding.websiteHint',
        type: 'text',
        inputmode: 'url',
    },
];

const errorFor = (field: string): string | undefined =>
    props.stepErrors[field] || props.form.errors[field];

const descriptionCount = computed(() => String(props.form.description ?? '').length);
</script>

<template>
    <div class="space-y-4">
        <div>
            <h3 class="text-sm font-extrabold tracking-tight text-zinc-900 dark:text-white">
                {{ t('onboarding.socialChannelsTitle') }}
            </h3>
            <p class="text-[11px] font-medium text-zinc-500 dark:text-zinc-400">
                {{ t('onboarding.socialChannelsSubtitle') }}
            </p>
        </div>

        <div class="grid gap-x-4 gap-y-3.5 sm:grid-cols-2">
            <div v-for="field in fields" :key="field.id" class="grid gap-1.5 min-w-0">
                <Label :for="field.id" class="flex items-center gap-1.5 text-xs font-semibold">
                    <component :is="field.icon" class="size-3.5" :class="field.iconClass" />
                    <span>{{ t(field.label) }}</span>
                </Label>
                <Input
                    :id="field.id"
                    v-model="form[field.id]"
                    :type="field.type"
                    :inputmode="field.inputmode"
                    autocomplete="off"
                    maxlength="255"
                    :placeholder="t(field.placeholder)"
                    class="h-9.5 rounded-xl text-xs font-medium"
                    :aria-invalid="Boolean(errorFor(field.id))"
                    @input="emit('field-input', field.id)"
                    @blur="emit('field-blur', field.id)"
                />
                <p
                    v-if="errorFor(field.id)"
                    class="flex items-start gap-1.5 text-[11px] font-medium text-amber-600 dark:text-amber-400"
                    role="alert"
                    aria-live="polite"
                >
                    <TriangleAlert class="size-3.5 shrink-0 mt-0.5" />
                    <span>{{ errorFor(field.id) }}</span>
                </p>
                <p v-else class="text-[11px] text-zinc-500 dark:text-zinc-400">
                    {{ t(field.hint) }}
                </p>
            </div>
        </div>

        <!-- Business Description -->
        <div class="space-y-1.5 pt-3 border-t border-zinc-200/60 dark:border-zinc-800/80">
            <div class="flex items-center justify-between gap-2">
                <Label for="description" class="flex items-center gap-1.5 text-xs font-semibold">
                    <FileText class="size-3.5 text-[#ff545a]" />
                    <span>{{ t('onboarding.description') }}</span>
                </Label>
                <span
                    class="text-[10px] font-semibold tabular-nums"
                    :class="descriptionCount > DESCRIPTION_MAX_LENGTH ? 'text-rose-500' : 'text-zinc-400'"
                >
                    {{ descriptionCount }}/{{ DESCRIPTION_MAX_LENGTH }}
                </span>
            </div>
            <textarea
                id="description"
                v-model="form.description"
                rows="4"
                :placeholder="t('onboarding.descriptionPlaceholder')"
                :aria-invalid="Boolean(errorFor('description'))"
                class="w-full rounded-xl border border-zinc-200 bg-background p-2.5 text-xs font-medium text-foreground shadow-xs dark:border-zinc-800 focus:outline-none focus:ring-2 focus:ring-[#ff545a]/30 aria-invalid:border-amber-500 resize-none"
                @input="emit('field-input', 'description')"
                @blur="emit('field-blur', 'description')"
            ></textarea>
            <p
                v-if="errorFor('description')"
                class="flex items-start gap-1.5 text-[11px] font-medium text-amber-600 dark:text-amber-400"
                role="alert"
                aria-live="polite"
            >
                <TriangleAlert class="size-3.5 shrink-0 mt-0.5" />
                <span>{{ errorFor('description') }}</span>
            </p>
        </div>
    </div>
</template>
