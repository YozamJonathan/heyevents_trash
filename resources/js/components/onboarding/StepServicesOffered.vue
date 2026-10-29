<script setup lang="ts">
import { Check } from '@lucide/vue';
import InputError from '@/components/InputError.vue';
import { useLanguage } from '@/composables/useLanguage';
import type { ServiceCategory } from '@/types/onboarding';

const props = defineProps<{
    selectedCategories: string[];
    categoriesList: ServiceCategory[];
    error?: string;
}>();

const emit = defineEmits<{
    (e: 'toggle', categoryId: string): void;
}>();

const { t, currentLanguage } = useLanguage();

const getCategoryLabel = (category: ServiceCategory) => {
    return currentLanguage.value === 'sw' ? category.swLabel : category.label;
};
</script>

<template>
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-xs font-bold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">
                    {{ t('onboarding.selectCategoryPrompt') }}
                </h3>
                <p class="text-[11px] text-zinc-500 dark:text-zinc-400">
                    {{ t('onboarding.servicesSubtitle') }}
                </p>
            </div>
            <span
                v-if="selectedCategories.length > 0"
                class="rounded-full bg-[#ff545a]/15 px-2.5 py-0.5 text-[11px] font-bold text-[#ff545a]"
            >
                {{ selectedCategories.length }} {{ t('onboarding.selectedCount') }}
            </span>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 gap-2.5">
            <button
                v-for="cat in categoriesList"
                :key="cat.id"
                type="button"
                @click="emit('toggle', cat.id)"
                class="relative flex items-center gap-2.5 rounded-xl border p-3 text-left transition-all duration-200 cursor-pointer"
                :class="[
                    selectedCategories.includes(cat.id)
                        ? 'border-[#ff545a] bg-[#ff545a]/10 text-zinc-900 dark:text-white ring-2 ring-[#ff545a]/30 shadow-xs'
                        : 'border-zinc-200/80 bg-white/70 text-zinc-700 hover:border-zinc-400 dark:border-zinc-800 dark:bg-zinc-800/70 dark:text-zinc-300 dark:hover:border-zinc-650',
                ]"
            >
                <span class="text-xl shrink-0">{{ cat.emoji }}</span>
                <span class="text-xs font-medium flex-1 truncate">{{ getCategoryLabel(cat) }}</span>

                <div
                    v-if="selectedCategories.includes(cat.id)"
                    class="flex size-4.5 items-center justify-center rounded-full bg-[#ff545a] text-white shrink-0"
                >
                    <Check class="size-3 stroke-[2.5]" />
                </div>
            </button>
        </div>

        <InputError :message="error" />
    </div>
</template>
