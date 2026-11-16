<script setup lang="ts">
import { Ticket, Briefcase, CheckCircle2, ShieldCheck, ArrowRight } from '@lucide/vue';
import { Button } from '@/components/ui/button';
import { Spinner } from '@/components/ui/spinner';
import { useLanguage } from '@/composables/useLanguage';

defineProps<{
    clientSubmitting: boolean;
}>();

const emit = defineEmits<{
    (e: 'select-client'): void;
    (e: 'select-provider'): void;
}>();

const { t } = useLanguage();
</script>

<template>
    <div class="w-full max-w-4xl space-y-8">
        <div class="text-center space-y-2">
            <h1 class="font-sans text-2xl sm:text-3xl font-black tracking-tight text-zinc-900 dark:text-white">
                {{ t('onboarding.title') }}
            </h1>
            <p class="text-sm text-zinc-500 dark:text-zinc-400 max-w-lg mx-auto">
                {{ t('onboarding.subtitle') }}
            </p>
        </div>

        <div class="grid gap-6 md:grid-cols-2">
            <!-- Client Card -->
            <div class="group relative flex flex-col justify-between rounded-2xl border border-white/80 bg-white/90 p-6 sm:p-8 backdrop-blur-xl shadow-xl shadow-zinc-900/5 transition-all duration-300 hover:-translate-y-1 hover:border-[#ff545a] hover:shadow-2xl dark:border-zinc-800/90 dark:bg-zinc-900/90 dark:hover:border-[#ff545a]">
                <div class="space-y-4">
                    <div class="inline-flex size-13 items-center justify-center rounded-2xl bg-amber-500/10 text-amber-600 dark:bg-amber-500/20 dark:text-amber-400">
                        <Ticket class="size-7" />
                    </div>
                    <div class="space-y-1.5">
                        <h3 class="font-sans text-lg font-extrabold text-zinc-900 dark:text-white">
                            {{ t('onboarding.clientTitle') }}
                        </h3>
                        <p class="text-xs sm:text-sm text-zinc-500 dark:text-zinc-400 leading-relaxed">
                            {{ t('onboarding.clientSubtitle') }}
                        </p>
                    </div>
                    <ul class="space-y-2 text-xs text-zinc-600 dark:text-zinc-300 pt-2 border-t border-zinc-200/60 dark:border-zinc-800/80">
                        <li class="flex items-center gap-2">
                            <CheckCircle2 class="size-4 text-emerald-500 shrink-0" />
                            <span>Direct event package reservations</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <CheckCircle2 class="size-4 text-emerald-500 shrink-0" />
                            <span>Browse verified caterers, MCs, DJs & venues</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <CheckCircle2 class="size-4 text-emerald-500 shrink-0" />
                            <span>Immediate access without verification delay</span>
                        </li>
                    </ul>
                </div>

                <div class="pt-6 mt-4">
                    <Button
                        @click="emit('select-client')"
                        :disabled="clientSubmitting"
                        class="h-11 w-full rounded-xl bg-[#ff545a] font-semibold text-white shadow-sm transition hover:bg-[#e0454b] disabled:opacity-50"
                    >
                        <Spinner v-if="clientSubmitting" />
                        <span>{{ t('onboarding.clientBtn') }}</span>
                        <ArrowRight class="size-4 ml-1.5" />
                    </Button>
                </div>
            </div>

            <!-- Service Provider Card -->
            <div class="group relative flex flex-col justify-between rounded-2xl border border-white/80 bg-white/90 p-6 sm:p-8 backdrop-blur-xl shadow-xl shadow-zinc-900/5 transition-all duration-300 hover:-translate-y-1 hover:border-[#ff545a] hover:shadow-2xl dark:border-zinc-800/90 dark:bg-zinc-900/90 dark:hover:border-[#ff545a]">
                <div class="space-y-4">
                    <div class="inline-flex size-13 items-center justify-center rounded-2xl bg-purple-500/10 text-purple-600 dark:bg-purple-500/20 dark:text-purple-400">
                        <Briefcase class="size-7" />
                    </div>
                    <div class="space-y-1.5">
                        <div class="flex items-center gap-2">
                            <h3 class="font-sans text-lg font-extrabold text-zinc-900 dark:text-white">
                                {{ t('onboarding.providerTitle') }}
                            </h3>
                            <span class="rounded-full bg-purple-500/15 px-2 py-0.5 text-[10px] font-bold text-purple-700 dark:text-purple-300 uppercase">
                                KYC Required
                            </span>
                        </div>
                        <p class="text-xs sm:text-sm text-zinc-500 dark:text-zinc-400 leading-relaxed">
                            {{ t('onboarding.providerSubtitle') }}
                        </p>
                    </div>
                    <ul class="space-y-2 text-xs text-zinc-600 dark:text-zinc-300 pt-2 border-t border-zinc-200/60 dark:border-zinc-800/80">
                        <li class="flex items-center gap-2">
                            <CheckCircle2 class="size-4 text-emerald-500 shrink-0" />
                            <span>List custom service packages and pricing</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <CheckCircle2 class="size-4 text-emerald-500 shrink-0" />
                            <span>Receive booking contracts from organizers</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <ShieldCheck class="size-4 text-[#ff545a] shrink-0" />
                            <span>5-step fast verification & verified badge</span>
                        </li>
                    </ul>
                </div>

                <div class="pt-6 mt-4">
                    <Button
                        @click="emit('select-provider')"
                        variant="outline"
                        class="h-11 w-full rounded-xl border-zinc-300 dark:border-zinc-700 bg-white/80 dark:bg-zinc-800/80 font-semibold text-zinc-900 dark:text-white shadow-xs transition hover:border-[#ff545a] hover:bg-zinc-50 dark:hover:bg-zinc-750"
                    >
                        <span>{{ t('onboarding.providerBtn') }}</span>
                        <ArrowRight class="size-4 ml-1.5" />
                    </Button>
                </div>
            </div>
        </div>
    </div>
</template>
