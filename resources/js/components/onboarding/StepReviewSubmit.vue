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

const getCategory = (id: string) => {
    return props.categoriesList.find((c) => c.id === id);
};

const getCategoryLabel = (id: string) => {
    const item = getCategory(id);
    if (!item) return id;
    return currentLanguage.value === 'sw' ? item.swLabel : item.label;
};
</script>

<template>
    <div class="space-y-4">
        <div>
            <h3 class="text-sm font-extrabold tracking-tight text-zinc-900 dark:text-white">
                {{ t('onboarding.summaryTitle') }}
            </h3>
            <p class="text-[11px] text-zinc-500 dark:text-zinc-400">
                {{ t('onboarding.summarySubtitle') }}
            </p>
        </div>

        <div class="space-y-3 text-xs">
            <!-- 1. Business Info Summary -->
            <div class="rounded-xl border border-zinc-200/90 bg-zinc-50/80 p-3.5 sm:p-4 shadow-2xs dark:border-zinc-800 dark:bg-zinc-950/80">
                <div class="flex items-center justify-between pb-2.5 mb-2.5 border-b border-zinc-200/80 dark:border-zinc-800">
                    <div class="flex items-center gap-1.5 font-extrabold text-zinc-900 dark:text-white">
                        <Building2 class="size-4 text-[#ff545a]" />
                        <span>{{ t('onboarding.businessIdentity') }}</span>
                    </div>
                    <button
                        type="button"
                        @click="emit('edit-step', 1)"
                        class="inline-flex items-center gap-1 text-[11px] font-semibold text-[#ff545a] hover:text-[#ff7075] hover:underline"
                    >
                        <Edit3 class="size-3" />
                        <span>{{ t('onboarding.editSection') }}</span>
                    </button>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-zinc-900 dark:text-white">
                    <div>
                        <span class="text-zinc-500 dark:text-zinc-400 block text-[10px] uppercase font-semibold tracking-wider mb-0.5">
                            {{ t('onboarding.brandName') }}
                        </span>
                        <span class="font-bold text-xs text-zinc-900 dark:text-white">
                            {{ form.brand_name || '—' }}
                        </span>
                    </div>
                    <div>
                        <span class="text-zinc-500 dark:text-zinc-400 block text-[10px] uppercase font-semibold tracking-wider mb-0.5">
                            {{ t('onboarding.contactName') }}
                        </span>
                        <span class="font-bold text-xs text-zinc-900 dark:text-white">
                            {{ form.contact_name || '—' }}
                        </span>
                    </div>
                    <div>
                        <span class="text-zinc-500 dark:text-zinc-400 block text-[10px] uppercase font-semibold tracking-wider mb-0.5">
                            {{ t('onboarding.nida') }}
                        </span>
                        <span class="font-mono text-xs font-semibold text-zinc-900 dark:text-white tracking-wider">
                            {{ form.nida_number || '—' }}
                        </span>
                    </div>
                    <div>
                        <span class="text-zinc-500 dark:text-zinc-400 block text-[10px] uppercase font-semibold tracking-wider mb-0.5">
                            {{ t('onboarding.primaryPhone') }}
                        </span>
                        <span class="font-bold text-xs text-zinc-900 dark:text-white">
                            {{ form.primary_phone || '—' }}
                        </span>
                        <span v-if="form.secondary_phone" class="text-zinc-500 dark:text-zinc-400 text-[11px] font-normal ml-1">
                            ({{ form.secondary_phone }})
                        </span>
                    </div>
                </div>
            </div>

            <!-- 2. Services Summary -->
            <div class="rounded-xl border border-zinc-200/90 bg-zinc-50/80 p-3.5 sm:p-4 shadow-2xs dark:border-zinc-800 dark:bg-zinc-950/80">
                <div class="flex items-center justify-between pb-2.5 mb-2.5 border-b border-zinc-200/80 dark:border-zinc-800">
                    <div class="flex items-center gap-1.5 font-extrabold text-zinc-900 dark:text-white">
                        <Briefcase class="size-4 text-purple-500" />
                        <span>{{ t('onboarding.servicesSummary') }}</span>
                    </div>
                    <button
                        type="button"
                        @click="emit('edit-step', 2)"
                        class="inline-flex items-center gap-1 text-[11px] font-semibold text-[#ff545a] hover:text-[#ff7075] hover:underline"
                    >
                        <Edit3 class="size-3" />
                        <span>{{ t('onboarding.editSection') }}</span>
                    </button>
                </div>
                <div class="flex flex-wrap gap-1.5">
                    <span
                        v-for="cat in form.categories"
                        :key="cat"
                        class="inline-flex items-center gap-1.5 rounded-lg border border-zinc-200/80 bg-white px-2.5 py-1 text-[11px] font-semibold text-zinc-900 shadow-2xs dark:border-zinc-700/80 dark:bg-zinc-800 dark:text-white"
                    >
                        <component
                            :is="getCategory(cat)?.icon"
                            v-if="getCategory(cat)?.icon"
                            class="size-3.5 text-[#ff545a] shrink-0"
                        />
                        <span>{{ getCategoryLabel(cat) }}</span>
                    </span>
                    <span v-if="form.categories.length === 0" class="text-zinc-400 dark:text-zinc-500 italic text-[11px]">
                        {{ t('onboarding.noServicesSelected') || 'No services selected' }}
                    </span>
                </div>
            </div>

            <!-- 3. Social & Media Summary -->
            <div class="rounded-xl border border-zinc-200/90 bg-zinc-50/80 p-3.5 sm:p-4 shadow-2xs dark:border-zinc-800 dark:bg-zinc-950/80">
                <div class="flex items-center justify-between pb-2.5 mb-2.5 border-b border-zinc-200/80 dark:border-zinc-800">
                    <div class="flex items-center gap-1.5 font-extrabold text-zinc-900 dark:text-white">
                        <Share2 class="size-4 text-blue-500" />
                        <span>{{ t('onboarding.socialSummary') }}</span>
                    </div>
                    <button
                        type="button"
                        @click="emit('edit-step', 3)"
                        class="inline-flex items-center gap-1 text-[11px] font-semibold text-[#ff545a] hover:text-[#ff7075] hover:underline"
                    >
                        <Edit3 class="size-3" />
                        <span>{{ t('onboarding.editSection') }}</span>
                    </button>
                </div>
                <div class="flex flex-wrap gap-2 text-[11px]">
                    <span
                        v-if="form.instagram"
                        class="inline-flex items-center gap-1 rounded-md border border-pink-500/20 bg-pink-500/10 px-2 py-0.5 font-semibold text-pink-700 dark:border-pink-500/30 dark:bg-pink-950/50 dark:text-pink-200"
                    >
                        IG: {{ form.instagram }}
                    </span>
                    <span
                        v-if="form.whatsapp"
                        class="inline-flex items-center gap-1 rounded-md border border-emerald-500/20 bg-emerald-500/10 px-2 py-0.5 font-semibold text-emerald-700 dark:border-emerald-500/30 dark:bg-emerald-950/50 dark:text-emerald-200"
                    >
                        WA: {{ form.whatsapp }}
                    </span>
                    <span
                        v-if="form.tiktok"
                        class="inline-flex items-center gap-1 rounded-md border border-indigo-500/20 bg-indigo-500/10 px-2 py-0.5 font-semibold text-indigo-700 dark:border-indigo-500/30 dark:bg-indigo-950/50 dark:text-indigo-200"
                    >
                        TT: {{ form.tiktok }}
                    </span>
                    <span
                        v-if="form.social_media"
                        class="inline-flex items-center gap-1 rounded-md border border-blue-500/20 bg-blue-500/10 px-2 py-0.5 font-semibold text-blue-700 dark:border-blue-500/30 dark:bg-blue-950/50 dark:text-blue-200"
                    >
                        {{ form.social_media }}
                    </span>
                    <span v-if="!form.instagram && !form.whatsapp && !form.tiktok && !form.social_media" class="text-zinc-400 dark:text-zinc-500 italic">
                        {{ t('onboarding.noSocialProvided') }}
                    </span>
                </div>
                <div
                    v-if="form.description"
                    class="mt-2.5 rounded-lg border border-zinc-200/70 bg-white/70 p-2.5 text-xs text-zinc-900 dark:border-zinc-800 dark:bg-zinc-900/90 dark:text-zinc-100"
                >
                    <p class="italic leading-relaxed font-medium">"{{ form.description }}"</p>
                </div>
            </div>

            <!-- 4. Licence Document Summary -->
            <div class="rounded-xl border border-zinc-200/90 bg-zinc-50/80 p-3.5 sm:p-4 shadow-2xs dark:border-zinc-800 dark:bg-zinc-950/80">
                <div class="flex items-center justify-between pb-2.5 mb-2.5 border-b border-zinc-200/80 dark:border-zinc-800">
                    <div class="flex items-center gap-1.5 font-extrabold text-zinc-900 dark:text-white">
                        <FileText class="size-4 text-emerald-500" />
                        <span>{{ t('onboarding.licenceSummary') }}</span>
                    </div>
                    <button
                        type="button"
                        @click="emit('edit-step', 4)"
                        class="inline-flex items-center gap-1 text-[11px] font-semibold text-[#ff545a] hover:text-[#ff7075] hover:underline"
                    >
                        <Edit3 class="size-3" />
                        <span>{{ t('onboarding.editSection') }}</span>
                    </button>
                </div>
                <div class="flex items-center gap-2.5 text-xs">
                    <div class="flex size-7 shrink-0 items-center justify-center rounded-lg bg-emerald-500/15 text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400">
                        <ShieldCheck class="size-4 stroke-[2.5]" />
                    </div>
                    <span class="font-bold text-zinc-900 dark:text-white truncate">
                        {{ licenceFileName || t('onboarding.licenceSelected') }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Authenticity Declaration Note -->
        <div class="flex items-start gap-2.5 rounded-xl border border-emerald-500/20 bg-emerald-500/10 p-3 text-emerald-800 dark:border-emerald-500/30 dark:bg-emerald-950/40 dark:text-emerald-300 text-[11px]">
            <Lock class="size-4 shrink-0 mt-0.5 text-emerald-600 dark:text-emerald-400" />
            <div>
                <span class="font-bold">{{ t('onboarding.disclaimerTitle') }}: </span>
                <span class="font-medium">{{ t('onboarding.disclaimerText') }}</span>
            </div>
        </div>
    </div>
</template>
