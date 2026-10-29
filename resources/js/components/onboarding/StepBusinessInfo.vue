<script setup lang="ts">
import { Building2, User, ShieldCheck, Phone, PhoneCall, Info } from '@lucide/vue';
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
                    class="h-9.5 text-xs rounded-xl"
                />
                <InputError :message="stepErrors.brand_name || form.errors.brand_name" />
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
                    class="h-9.5 text-xs rounded-xl"
                />
                <InputError :message="stepErrors.contact_name || form.errors.contact_name" />
            </div>

            <!-- NIDA Number -->
            <div class="grid gap-1.5 min-w-0 sm:col-span-2">
                <div class="flex items-center justify-between">
                    <Label for="nida_number" class="flex items-center gap-1.5 text-xs font-semibold">
                        <ShieldCheck class="size-3.5 text-[#ff545a]" />
                        <span>{{ t('onboarding.nida') }}</span>
                        <span class="text-rose-500">*</span>
                    </Label>
                    <span class="text-[10px] text-zinc-400">e.g. 19900101-12345-00001-20</span>
                </div>
                <Input
                    id="nida_number"
                    v-model="form.nida_number"
                    required
                    :placeholder="t('onboarding.nidaPlaceholder')"
                    class="h-9.5 text-xs rounded-xl font-mono"
                />
                <InputError :message="stepErrors.nida_number || form.errors.nida_number" />

                <!-- NIDA Disclaimer -->
                <div class="flex items-start gap-1.5 rounded-lg bg-zinc-100/80 p-2 dark:bg-zinc-800/60 text-[11px] text-zinc-500 dark:text-zinc-400 mt-0.5">
                    <Info class="size-3.5 text-amber-500 shrink-0 mt-0.5" />
                    <span>{{ t('onboarding.nidaNotice') }}</span>
                </div>
            </div>

            <!-- Primary Phone -->
            <div class="grid gap-1.5 min-w-0">
                <Label for="primary_phone" class="flex items-center gap-1.5 text-xs font-semibold">
                    <Phone class="size-3.5 text-[#ff545a]" />
                    <span>{{ t('onboarding.primaryPhone') }}</span>
                    <span class="text-rose-500">*</span>
                </Label>
                <Input
                    id="primary_phone"
                    v-model="form.primary_phone"
                    required
                    :placeholder="t('onboarding.primaryPhonePlaceholder')"
                    class="h-9.5 text-xs rounded-xl font-mono"
                />
                <InputError :message="stepErrors.primary_phone || form.errors.primary_phone" />
            </div>

            <!-- Secondary Phone -->
            <div class="grid gap-1.5 min-w-0">
                <Label for="secondary_phone" class="flex items-center gap-1.5 text-xs font-semibold">
                    <PhoneCall class="size-3.5 text-zinc-400" />
                    <span>{{ t('onboarding.secondaryPhone') }}</span>
                </Label>
                <Input
                    id="secondary_phone"
                    v-model="form.secondary_phone"
                    :placeholder="t('onboarding.secondaryPhonePlaceholder')"
                    class="h-9.5 text-xs rounded-xl font-mono"
                />
                <InputError :message="stepErrors.secondary_phone || form.errors.secondary_phone" />
            </div>
        </div>
    </div>
</template>
