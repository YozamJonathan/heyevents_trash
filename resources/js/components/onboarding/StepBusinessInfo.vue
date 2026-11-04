<script setup lang="ts">
import { computed } from 'vue';
import { Building2, User, ShieldCheck, Phone, PhoneCall, Info, TriangleAlert } from '@lucide/vue';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { useLanguage } from '@/composables/useLanguage';
import { NIDA_LENGTH, phoneDigitProgress } from '@/lib/providerRegistrationValidation';

const props = defineProps<{
    form: any;
    stepErrors: Record<string, string>;
}>();

const emit = defineEmits<{
    (e: 'field-blur', field: string): void;
    (e: 'field-input', field: string): void;
}>();

const { t } = useLanguage();

const nidaCount = computed(() => String(props.form.nida_number ?? '').length);
const nidaComplete = computed(() => nidaCount.value === NIDA_LENGTH && !props.stepErrors.nida_number);

const primaryProgress = computed(() => phoneDigitProgress(props.form.primary_phone ?? ''));
const secondaryProgress = computed(() => phoneDigitProgress(props.form.secondary_phone ?? ''));
</script>

<template>
    <div class="space-y-4">
        <div class="grid gap-x-4 gap-y-3.5 sm:grid-cols-2">
            <!-- Brand Name -->
            <div class="grid gap-1.5 min-w-0">
                <Label for="brand_name" class="flex items-center gap-1.5 text-xs font-semibold">
                    <Building2 class="size-3.5 text-[#ff545a]" />
                    <span>{{ t('onboarding.brandName') }}</span>
                    <span class="text-rose-500">*</span>
                </Label>
                <Input
                    id="brand_name"
                    v-model="form.brand_name"
                    required
                    :placeholder="t('onboarding.brandNamePlaceholder')"
                    class="h-9.5 rounded-xl text-xs font-medium"
                    :aria-invalid="Boolean(stepErrors.brand_name || form.errors.brand_name)"
                    @input="emit('field-input', 'brand_name')"
                    @blur="emit('field-blur', 'brand_name')"
                />
                <p
                    v-if="stepErrors.brand_name || form.errors.brand_name"
                    class="flex items-start gap-1.5 text-[11px] font-medium text-amber-600 dark:text-amber-400"
                    role="alert"
                    aria-live="polite"
                >
                    <TriangleAlert class="size-3.5 shrink-0 mt-0.5" />
                    <span>{{ stepErrors.brand_name || form.errors.brand_name }}</span>
                </p>
            </div>

            <!-- Contact Name -->
            <div class="grid gap-1.5 min-w-0">
                <Label for="contact_name" class="flex items-center gap-1.5 text-xs font-semibold">
                    <User class="size-3.5 text-[#ff545a]" />
                    <span>{{ t('onboarding.contactName') }}</span>
                    <span class="text-rose-500">*</span>
                </Label>
                <Input
                    id="contact_name"
                    v-model="form.contact_name"
                    required
                    :placeholder="t('onboarding.contactNamePlaceholder')"
                    class="h-9.5 rounded-xl text-xs font-medium"
                    :aria-invalid="Boolean(stepErrors.contact_name || form.errors.contact_name)"
                    @input="emit('field-input', 'contact_name')"
                    @blur="emit('field-blur', 'contact_name')"
                />
                <p
                    v-if="stepErrors.contact_name || form.errors.contact_name"
                    class="flex items-start gap-1.5 text-[11px] font-medium text-amber-600 dark:text-amber-400"
                    role="alert"
                    aria-live="polite"
                >
                    <TriangleAlert class="size-3.5 shrink-0 mt-0.5" />
                    <span>{{ stepErrors.contact_name || form.errors.contact_name }}</span>
                </p>
            </div>

            <!-- NIDA Number -->
            <div class="grid gap-1.5 min-w-0 sm:col-span-2">
                <div class="flex items-center justify-between gap-2">
                    <Label for="nida_number" class="flex items-center gap-1.5 text-xs font-semibold">
                        <ShieldCheck class="size-3.5 text-[#ff545a]" />
                        <span>{{ t('onboarding.nida') }}</span>
                        <span class="text-rose-500">*</span>
                    </Label>
                    <span
                        class="text-[10px] font-semibold tabular-nums"
                        :class="nidaComplete ? 'text-emerald-600' : nidaCount > NIDA_LENGTH ? 'text-rose-500' : 'text-zinc-400'"
                    >
                        {{ nidaCount }}/{{ NIDA_LENGTH }}
                    </span>
                </div>
                <Input
                    id="nida_number"
                    v-model="form.nida_number"
                    type="text"
                    inputmode="numeric"
                    autocomplete="off"
                    maxlength="40"
                    required
                    :placeholder="t('onboarding.nidaPlaceholder')"
                    class="h-9.5 rounded-xl text-xs font-medium tracking-wide tabular-nums"
                    :aria-invalid="Boolean(stepErrors.nida_number || form.errors.nida_number)"
                    @input="emit('field-input', 'nida_number')"
                    @blur="emit('field-blur', 'nida_number')"
                />
                <p
                    v-if="stepErrors.nida_number || form.errors.nida_number"
                    class="flex items-start gap-1.5 text-[11px] font-medium text-amber-600 dark:text-amber-400"
                    role="alert"
                    aria-live="polite"
                >
                    <TriangleAlert class="size-3.5 shrink-0 mt-0.5" />
                    <span>{{ stepErrors.nida_number || form.errors.nida_number }}</span>
                </p>
                <p v-else class="text-[11px] text-zinc-500 dark:text-zinc-400">
                    {{ t('onboarding.nidaHint') }}
                </p>

                <div class="flex items-start gap-1.5 rounded-lg bg-zinc-100/80 p-2 dark:bg-zinc-800/60 text-[11px] text-zinc-500 dark:text-zinc-400 mt-0.5">
                    <Info class="size-3.5 text-amber-500 shrink-0 mt-0.5" />
                    <span>{{ t('onboarding.nidaNotice') }}</span>
                </div>
            </div>

            <!-- Primary Phone -->
            <div class="grid gap-1.5 min-w-0">
                <div class="flex items-center justify-between gap-2">
                    <Label for="primary_phone" class="flex items-center gap-1.5 text-xs font-semibold">
                        <Phone class="size-3.5 text-[#ff545a]" />
                        <span>{{ t('onboarding.primaryPhone') }}</span>
                        <span class="text-rose-500">*</span>
                    </Label>
                    <span
                        v-if="primaryProgress.expected"
                        class="text-[10px] font-semibold tabular-nums"
                        :class="primaryProgress.digits === primaryProgress.expected && !stepErrors.primary_phone ? 'text-emerald-600' : 'text-zinc-400'"
                    >
                        {{ primaryProgress.digits }}/{{ primaryProgress.expected }}
                    </span>
                </div>
                <Input
                    id="primary_phone"
                    v-model="form.primary_phone"
                    type="tel"
                    autocomplete="tel"
                    maxlength="20"
                    required
                    :placeholder="t('onboarding.primaryPhonePlaceholder')"
                    class="h-9.5 rounded-xl text-xs font-medium"
                    :aria-invalid="Boolean(stepErrors.primary_phone || form.errors.primary_phone)"
                    @input="emit('field-input', 'primary_phone')"
                    @blur="emit('field-blur', 'primary_phone')"
                />
                <p
                    v-if="stepErrors.primary_phone || form.errors.primary_phone"
                    class="flex items-start gap-1.5 text-[11px] font-medium text-amber-600 dark:text-amber-400"
                    role="alert"
                    aria-live="polite"
                >
                    <TriangleAlert class="size-3.5 shrink-0 mt-0.5" />
                    <span>{{ stepErrors.primary_phone || form.errors.primary_phone }}</span>
                </p>
                <p v-else class="text-[11px] text-zinc-500 dark:text-zinc-400">
                    {{ t('onboarding.phoneHint') }}
                </p>
            </div>

            <!-- Secondary Phone -->
            <div class="grid gap-1.5 min-w-0">
                <div class="flex items-center justify-between gap-2">
                    <Label for="secondary_phone" class="flex items-center gap-1.5 text-xs font-semibold">
                        <PhoneCall class="size-3.5 text-zinc-400" />
                        <span>{{ t('onboarding.secondaryPhone') }}</span>
                    </Label>
                    <span
                        v-if="secondaryProgress.expected"
                        class="text-[10px] font-semibold tabular-nums"
                        :class="secondaryProgress.digits === secondaryProgress.expected && !stepErrors.secondary_phone ? 'text-emerald-600' : 'text-zinc-400'"
                    >
                        {{ secondaryProgress.digits }}/{{ secondaryProgress.expected }}
                    </span>
                </div>
                <Input
                    id="secondary_phone"
                    v-model="form.secondary_phone"
                    type="tel"
                    autocomplete="tel"
                    maxlength="20"
                    :placeholder="t('onboarding.secondaryPhonePlaceholder')"
                    class="h-9.5 rounded-xl text-xs font-medium"
                    :aria-invalid="Boolean(stepErrors.secondary_phone || form.errors.secondary_phone)"
                    @input="emit('field-input', 'secondary_phone')"
                    @blur="emit('field-blur', 'secondary_phone')"
                />
                <p
                    v-if="stepErrors.secondary_phone || form.errors.secondary_phone"
                    class="flex items-start gap-1.5 text-[11px] font-medium text-amber-600 dark:text-amber-400"
                    role="alert"
                    aria-live="polite"
                >
                    <TriangleAlert class="size-3.5 shrink-0 mt-0.5" />
                    <span>{{ stepErrors.secondary_phone || form.errors.secondary_phone }}</span>
                </p>
                <p v-else class="text-[11px] text-zinc-500 dark:text-zinc-400">
                    {{ t('onboarding.phoneHint') }}
                </p>
            </div>
        </div>
    </div>
</template>
