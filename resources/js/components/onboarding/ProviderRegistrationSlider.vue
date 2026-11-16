<script setup lang="ts">
import { ArrowLeft, ArrowRight, CheckCircle2, MessageSquare } from '@lucide/vue';
import { router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Spinner } from '@/components/ui/spinner';
import { useLanguage } from '@/composables/useLanguage';
import { useProviderRegistrationSlider } from '@/composables/useProviderRegistrationSlider';
import type { ExistingProviderProfile } from '@/types/onboarding';
import RegistrationSliderProgress from './RegistrationSliderProgress.vue';
import StepBusinessInfo from './StepBusinessInfo.vue';
import StepServicesOffered from './StepServicesOffered.vue';
import StepSocialPortfolio from './StepSocialPortfolio.vue';
import StepBusinessLicence from './StepBusinessLicence.vue';
import StepReviewSubmit from './StepReviewSubmit.vue';

const props = defineProps<{
    existingProfile?: ExistingProviderProfile | null;
}>();

const emit = defineEmits<{
    (e: 'back-to-roles'): void;
}>();

const { t } = useLanguage();
const {
    activeStep,
    maxVisitedStep,
    stepErrors,
    form,
    AVAILABLE_CATEGORIES,
    licenceFileName,
    licenceFileSize,
    hasExistingLicence,
    toggleCategory,
    handleFileSelected,
    clearLicence,
    nextStep,
    prevStep,
    goToStep,
    submitApplication,
    markTouched,
    markLive,
} = useProviderRegistrationSlider(props.existingProfile);

const onPrevClick = () => {
    if (activeStep.value === 1) {
        if (props.existingProfile) {
            router.visit('/dashboard');
        } else {
            emit('back-to-roles');
        }
    } else {
        prevStep();
    }
};
</script>

<template>
    <div class="w-full max-w-3xl space-y-5 animate-in fade-in slide-in-from-bottom-2">
        <!-- Top bar with Back and KYC pill -->
        <div class="flex items-center justify-between">
            <button
                type="button"
                @click="onPrevClick"
                class="inline-flex items-center gap-1.5 text-xs font-semibold text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white transition"
            >
                <ArrowLeft class="size-3.5 text-[#ff545a]" />
                <span>{{ activeStep === 1 ? (existingProfile ? 'Back to Dashboard' : t('onboarding.backToRoles')) : t('onboarding.prevStep') }}</span>
            </button>
            <span class="rounded-full bg-[#ff545a]/10 px-2.5 py-1 text-[11px] font-bold text-[#ff545a]">
                {{ t('onboarding.step') }} {{ activeStep }} {{ t('onboarding.of') }} 5
            </span>
        </div>

        <!-- Admin Feedback Notice during Rectification -->
        <div
            v-if="existingProfile?.admin_feedback"
            class="flex items-start gap-2.5 rounded-xl border border-blue-500/30 bg-blue-50/80 p-3 text-xs dark:bg-blue-950/30"
        >
            <div class="rounded-lg bg-blue-500/15 p-1.5 text-blue-600 dark:text-blue-400 shrink-0 mt-0.5">
                <MessageSquare class="size-3.5" />
            </div>
            <div>
                <span class="font-bold text-foreground">Admin Feedback to Rectify:</span>
                <p class="text-zinc-700 dark:text-zinc-300 italic mt-0.5">
                    "{{ existingProfile.admin_feedback }}"
                </p>
            </div>
        </div>

        <!-- Main Card Form Container -->
        <div class="rounded-2xl border border-white/80 bg-white/95 p-5 sm:p-7 backdrop-blur-xl shadow-2xl shadow-zinc-900/10 dark:border-zinc-800/90 dark:bg-zinc-900/95 dark:shadow-black/50">
            <!-- 5-Step Horizontal Slider Progress Bar -->
            <RegistrationSliderProgress
                :active-step="activeStep"
                :max-visited-step="maxVisitedStep"
                @step-click="goToStep"
            />

            <!-- Form Content per Step -->
            <div class="mt-5 border-t border-zinc-200/60 dark:border-zinc-800/80 pt-5">
                <StepBusinessInfo
                    v-if="activeStep === 1"
                    :form="form"
                    :step-errors="stepErrors"
                    @field-blur="markTouched"
                    @field-input="markLive"
                />

                <StepServicesOffered
                    v-else-if="activeStep === 2"
                    :selected-categories="form.categories"
                    :categories-list="AVAILABLE_CATEGORIES"
                    :error="stepErrors.categories || form.errors.categories || form.errors['categories.0']"
                    @toggle="toggleCategory"
                />

                <StepSocialPortfolio
                    v-else-if="activeStep === 3"
                    :form="form"
                    :step-errors="stepErrors"
                    @field-blur="markTouched"
                    @field-input="markLive"
                />

                <StepBusinessLicence
                    v-else-if="activeStep === 4"
                    :form="form"
                    :licence-file-name="licenceFileName"
                    :licence-file-size="licenceFileSize"
                    :has-existing-licence="hasExistingLicence"
                    :error="stepErrors.business_licence"
                    @file-selected="handleFileSelected"
                    @clear-file="clearLicence"
                />

                <StepReviewSubmit
                    v-else-if="activeStep === 5"
                    :form="form"
                    :licence-file-name="licenceFileName"
                    :has-existing-licence="hasExistingLicence"
                    :categories-list="AVAILABLE_CATEGORIES"
                    @edit-step="goToStep"
                />
            </div>

            <!-- Bottom Navigation Actions -->
            <div class="mt-6 pt-4 border-t border-zinc-200/60 dark:border-zinc-800/80 flex items-center justify-between gap-3">
                <Button
                    type="button"
                    variant="outline"
                    @click="onPrevClick"
                    class="h-10 rounded-xl px-4 text-xs font-semibold"
                >
                    <ArrowLeft class="size-3.5 mr-1 text-[#ff545a]" />
                    <span>{{ activeStep === 1 ? t('onboarding.backToRoles') : t('onboarding.prevStep') }}</span>
                </Button>

                <Button
                    v-if="activeStep < 5"
                    type="button"
                    @click="nextStep"
                    class="h-10 rounded-xl bg-[#ff545a] px-5 text-xs font-semibold text-white shadow-sm hover:bg-[#e0454b]"
                >
                    <span>{{ t('onboarding.nextStep') }}</span>
                    <ArrowRight class="size-3.5 ml-1" />
                </Button>

                <Button
                    v-else
                    type="button"
                    @click="submitApplication"
                    :disabled="form.processing"
                    class="h-10 rounded-xl bg-[#ff545a] px-6 text-xs font-semibold text-white shadow-sm hover:bg-[#e0454b] disabled:opacity-50"
                >
                    <Spinner v-if="form.processing" class="mr-1.5" />
                    <CheckCircle2 v-else class="size-3.5 mr-1" />
                    <span>{{ t('onboarding.submitApplication') }}</span>
                </Button>
            </div>
        </div>
    </div>
</template>
