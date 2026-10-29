<script setup lang="ts">
import { ShieldCheck, Edit3, Lock, Building2, Briefcase, Share2, FileText } from '@lucide/vue';
import { useLanguage } from '@/composables/useLanguage';
import type { ServiceCategory } from '@/types/onboarding';

const props = defineProps<{
    form: any;
    licenceFileName: string | null;
    hasExistingLicence: boolean;
    categoriesList: ServiceCategory[];
}>();

const emit = defineEmits<{
    (e: 'edit-step', stepNumber: number): void;
}>();

const { t, currentLanguage } = useLanguage();

const getCategoryLabel = (id: string) => {
    const item = props.categoriesList.find((c) => c.id === id);
    if (!item) return id;
    return currentLanguage.value === 'sw' ? `${item.emoji} ${item.swLabel}` : `${item.emoji} ${item.label}`;
};
</script>

<template>
    <div class="space-y-4">
        <div>
            <h3 class="text-xs font-bold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">
                {{ t('onboarding.summaryTitle') }}
            </h3>
            <p class="text-[11px] text-zinc-500 dark:text-zinc-400">
                {{ t('onboarding.summarySubtitle') }}
            </p>
        </div>

        <div class="space-y-3 text-xs">
            <!-- 1. Business Info Summary -->
            <div class="rounded-xl border border-zinc-200/80 bg-zinc-50/70 p-3.5 dark:border-zinc-800 dark:bg-zinc-850/60">
                <div class="flex items-center justify-between pb-2 mb-2 border-b border-zinc-200/60 dark:border-zinc-800">
                    <div class="flex items-center gap-1.5 font-bold text-zinc-900 dark:text-white">
                        <Building2 class="size-4 text-[#ff545a]" />
                        <span>{{ t('onboarding.businessIdentity') }}</span>
                    </div>
                    <button type="button" @click="emit('edit-step', 1)" class="inline-flex items-center gap-1 text-[11px] font-semibold text-[#ff545a] hover:underline">
                        <Edit3 class="size-3" />
                        <span>{{ t('onboarding.editSection') }}</span>
                    </button>
                </div>
                <div class="grid grid-cols-2 gap-2 text-zinc-600 dark:text-zinc-300">
                    <div><span class="text-zinc-400 block text-[10px] uppercase font-medium">{{ t('onboarding.brandName') }}</span> <span class="font-semibold text-zinc-900 dark:text-white">{{ form.brand_name }}</span></div>
                    <div><span class="text-zinc-400 block text-[10px] uppercase font-medium">{{ t('onboarding.contactName') }}</span> {{ form.contact_name }}</div>
                    <div><span class="text-zinc-400 block text-[10px] uppercase font-medium">{{ t('onboarding.nida') }}</span> <span class="font-mono text-[11px]">{{ form.nida_number }}</span></div>
                    <div><span class="text-zinc-400 block text-[10px] uppercase font-medium">{{ t('onboarding.primaryPhone') }}</span> {{ form.primary_phone }} <span v-if="form.secondary_phone" class="text-zinc-400 text-[10px]">({{ form.secondary_phone }})</span></div>
                </div>
            </div>

            <!-- 2. Services Summary -->
            <div class="rounded-xl border border-zinc-200/80 bg-zinc-50/70 p-3.5 dark:border-zinc-800 dark:bg-zinc-850/60">
                <div class="flex items-center justify-between pb-2 mb-2 border-b border-zinc-200/60 dark:border-zinc-800">
                    <div class="flex items-center gap-1.5 font-bold text-zinc-900 dark:text-white">
                        <Briefcase class="size-4 text-purple-500" />
                        <span>{{ t('onboarding.servicesSummary') }}</span>
                    </div>
                    <button type="button" @click="emit('edit-step', 2)" class="inline-flex items-center gap-1 text-[11px] font-semibold text-[#ff545a] hover:underline">
                        <Edit3 class="size-3" />
                        <span>{{ t('onboarding.editSection') }}</span>
                    </button>
                </div>
                <div class="flex flex-wrap gap-1.5">
                    <span v-for="cat in form.categories" :key="cat" class="inline-flex items-center rounded-lg bg-zinc-200/80 dark:bg-zinc-800 px-2.5 py-1 text-[11px] font-medium text-zinc-800 dark:text-zinc-200">
                        {{ getCategoryLabel(cat) }}
                    </span>
                </div>
            </div>

            <!-- 3. Social & Media Summary -->
            <div class="rounded-xl border border-zinc-200/80 bg-zinc-50/70 p-3.5 dark:border-zinc-800 dark:bg-zinc-850/60">
                <div class="flex items-center justify-between pb-2 mb-2 border-b border-zinc-200/60 dark:border-zinc-800">
                    <div class="flex items-center gap-1.5 font-bold text-zinc-900 dark:text-white">
                        <Share2 class="size-4 text-blue-500" />
                        <span>{{ t('onboarding.socialSummary') }}</span>
                    </div>
                    <button type="button" @click="emit('edit-step', 3)" class="inline-flex items-center gap-1 text-[11px] font-semibold text-[#ff545a] hover:underline">
                        <Edit3 class="size-3" />
                        <span>{{ t('onboarding.editSection') }}</span>
                    </button>
                </div>
                <div class="flex flex-wrap gap-2 text-[11px]">
                    <span v-if="form.instagram" class="rounded bg-pink-500/10 px-2 py-0.5 text-pink-700 dark:text-pink-300">IG: {{ form.instagram }}</span>
                    <span v-if="form.whatsapp" class="rounded bg-emerald-500/10 px-2 py-0.5 text-emerald-700 dark:text-emerald-300">WA: {{ form.whatsapp }}</span>
                    <span v-if="form.tiktok" class="rounded bg-indigo-500/10 px-2 py-0.5 text-indigo-700 dark:text-indigo-300">TT: {{ form.tiktok }}</span>
                    <span v-if="form.social_media" class="rounded bg-blue-500/10 px-2 py-0.5 text-blue-700 dark:text-blue-300">{{ form.social_media }}</span>
                    <span v-if="!form.instagram && !form.whatsapp && !form.tiktok && !form.social_media" class="text-zinc-400 italic">{{ t('onboarding.noSocialProvided') }}</span>
                </div>
                <p v-if="form.description" class="mt-2 text-zinc-500 dark:text-zinc-400 text-[11px] italic">"{{ form.description }}"</p>
            </div>

            <!-- 4. Licence Document Summary -->
            <div class="rounded-xl border border-zinc-200/80 bg-zinc-50/70 p-3.5 dark:border-zinc-800 dark:bg-zinc-850/60">
                <div class="flex items-center justify-between pb-2 mb-2 border-b border-zinc-200/60 dark:border-zinc-800">
                    <div class="flex items-center gap-1.5 font-bold text-zinc-900 dark:text-white">
                        <FileText class="size-4 text-emerald-500" />
                        <span>{{ t('onboarding.licenceSummary') }}</span>
                    </div>
                    <button type="button" @click="emit('edit-step', 4)" class="inline-flex items-center gap-1 text-[11px] font-semibold text-[#ff545a] hover:underline">
                        <Edit3 class="size-3" />
                        <span>{{ t('onboarding.editSection') }}</span>
                    </button>
                </div>
                <div class="flex items-center gap-2 text-zinc-700 dark:text-zinc-300 text-[11px]">
                    <ShieldCheck class="size-4 text-emerald-500" />
                    <span class="font-medium truncate">{{ licenceFileName || t('onboarding.licenceSelected') }}</span>
                </div>
            </div>
        </div>

        <!-- Authenticity Declaration Note -->
        <div class="flex items-start gap-2 rounded-xl bg-emerald-500/10 p-3 text-emerald-800 dark:text-emerald-300 text-[11px]">
            <Lock class="size-4 shrink-0 mt-0.5 text-emerald-600 dark:text-emerald-400" />
            <div>
                <span class="font-bold">{{ t('onboarding.disclaimerTitle') }}: </span>
                <span>{{ t('onboarding.disclaimerText') }}</span>
            </div>
        </div>
    </div>
</template>
