<script setup lang="ts">
import { computed } from 'vue';
import { Building2, Briefcase, Share2, FileText, ClipboardCheck, Check } from '@lucide/vue';
import { useLanguage, type TranslationKey } from '@/composables/useLanguage';

const props = defineProps<{
    activeStep: number;
    maxVisitedStep: number;
}>();

const emit = defineEmits<{
    (e: 'step-click', step: number): void;
}>();

const { t } = useLanguage();

const steps: Array<{ number: number; titleKey: TranslationKey; icon: typeof Building2 }> = [
    { number: 1, titleKey: 'onboarding.step1Title', icon: Building2 },
    { number: 2, titleKey: 'onboarding.step2Title', icon: Briefcase },
    { number: 3, titleKey: 'onboarding.step3Title', icon: Share2 },
    { number: 4, titleKey: 'onboarding.step4Title', icon: FileText },
    { number: 5, titleKey: 'onboarding.step5Title', icon: ClipboardCheck },
];

const progressPercentage = computed(() => {
    return ((props.activeStep - 1) / (steps.length - 1)) * 100;
});

const onStepClick = (stepNumber: number) => {
    if (stepNumber <= props.maxVisitedStep || stepNumber < props.activeStep) {
        emit('step-click', stepNumber);
    }
};
</script>

<template>
    <div class="w-full py-4 px-2 sm:px-6">
        <div class="relative flex items-center justify-between">
            <!-- Background connecting line -->
            <div class="absolute top-1/2 left-0 h-1 w-full -translate-y-1/2 bg-zinc-200 dark:bg-zinc-800 rounded-full z-0"></div>

            <!-- Animated active progress line -->
            <div
                class="absolute top-1/2 left-0 h-1 -translate-y-1/2 bg-[#ff545a] transition-all duration-500 ease-out rounded-full z-0"
                :style="{ width: `${progressPercentage}%` }"
            ></div>

            <!-- Step items -->
            <div
                v-for="step in steps"
                :key="step.number"
                class="relative z-10 flex flex-col items-center group cursor-pointer"
                @click="onStepClick(step.number)"
            >
                <!-- Step Circle -->
                <button
                    type="button"
                    :disabled="step.number > maxVisitedStep && step.number > activeStep"
                    class="relative flex size-10 sm:size-11 items-center justify-center rounded-full border-2 transition-all duration-300 shadow-sm"
                    :class="[
                        step.number === activeStep
                            ? 'border-[#ff545a] bg-[#ff545a] text-white ring-4 ring-[#ff545a]/25 scale-110'
                            : step.number < activeStep
                              ? 'border-emerald-500 bg-emerald-500 text-white'
                              : 'border-zinc-300 bg-white text-zinc-400 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-500 hover:border-zinc-400',
                    ]"
                >
                    <Check v-if="step.number < activeStep" class="size-4.5 stroke-[2.5]" />
                    <component :is="step.icon" v-else class="size-4 sm:size-4.5" />

                    <!-- Step Number Badge -->
                    <span
                        class="absolute -bottom-1 -right-1 flex size-4 items-center justify-center rounded-full text-[9px] font-bold"
                        :class="step.number === activeStep ? 'bg-zinc-900 text-white dark:bg-white dark:text-zinc-900' : 'bg-zinc-200 text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300'"
                    >
                        {{ step.number }}
                    </span>
                </button>

                <!-- Step Label -->
                <span
                    class="mt-2 hidden text-center text-[11px] font-medium transition sm:block max-w-[80px] truncate"
                    :class="[
                        step.number === activeStep
                            ? 'font-bold text-[#ff545a]'
                            : step.number < activeStep
                              ? 'text-zinc-700 dark:text-zinc-300'
                              : 'text-zinc-400 dark:text-zinc-500',
                    ]"
                >
                    {{ t(step.titleKey) }}
                </span>
            </div>
        </div>
    </div>
</template>
