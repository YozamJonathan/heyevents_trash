<script setup lang="ts">
import { ref } from 'vue';
import { UploadCloud, FileCheck, X } from '@lucide/vue';
import InputError from '@/components/InputError.vue';
import { useLanguage } from '@/composables/useLanguage';

defineProps<{
    form: any;
    licenceFileName: string | null;
    licenceFileSize: string | null;
    hasExistingLicence: boolean;
    error?: string;
}>();

const emit = defineEmits<{
    (e: 'file-selected', file: File): void;
    (e: 'clear-file'): void;
}>();

const { t } = useLanguage();
const fileInputRef = ref<HTMLInputElement | null>(null);
const isDragging = ref(false);

const onFileChange = (event: Event) => {
    const target = event.target as HTMLInputElement;
    if (target.files && target.files[0]) {
        emit('file-selected', target.files[0]);
    }
};

const onDrop = (event: DragEvent) => {
    isDragging.value = false;
    if (event.dataTransfer?.files && event.dataTransfer.files[0]) {
        emit('file-selected', event.dataTransfer.files[0]);
    }
};

const removeFile = (e: Event) => {
    e.stopPropagation();
    emit('clear-file');
    if (fileInputRef.value) {
        fileInputRef.value.value = '';
    }
};
</script>

<template>
    <div class="space-y-4">
        <div>
            <h3 class="text-xs font-bold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">
                {{ t('onboarding.dropzoneTitle') }}
            </h3>
            <p class="text-[11px] text-zinc-500 dark:text-zinc-400">
                {{ t('onboarding.dropzoneSubtitle') }}
            </p>
        </div>

        <div class="w-full">
            <label
                for="licence_upload"
                @dragover.prevent="isDragging = true"
                @dragleave.prevent="isDragging = false"
                @drop.prevent="onDrop"
                class="group relative flex flex-col items-center justify-center rounded-2xl border-2 border-dashed p-6 text-center transition-all cursor-pointer"
                :class="[
                    isDragging
                        ? 'border-[#ff545a] bg-[#ff545a]/5 ring-2 ring-[#ff545a]/20'
                        : 'border-zinc-300/90 bg-zinc-50/70 hover:border-[#ff545a] hover:bg-zinc-100/50 dark:border-zinc-700 dark:bg-zinc-800/40 dark:hover:bg-zinc-800/60',
                ]"
            >
                <!-- When no file is selected -->
                <div v-if="!licenceFileName" class="flex flex-col items-center gap-2.5">
                    <div class="rounded-full bg-[#ff545a]/10 p-3 text-[#ff545a] transition group-hover:scale-105">
                        <UploadCloud class="size-7" />
                    </div>
                    <div class="space-y-1">
                        <p class="text-xs font-semibold text-zinc-800 dark:text-zinc-200">
                            {{ t('onboarding.uploadDrag') }}
                        </p>
                        <p class="text-[11px] text-zinc-500 dark:text-zinc-400">
                            {{ t('onboarding.dropzoneFormats') }}
                        </p>
                    </div>
                </div>

                <!-- When file is selected or existing -->
                <div v-else class="flex items-center justify-between w-full gap-3 px-3">
                    <div class="flex items-center gap-3 min-w-0 flex-1">
                        <div class="rounded-xl bg-emerald-500/10 p-2.5 text-emerald-600 dark:text-emerald-400 shrink-0">
                            <FileCheck class="size-6" />
                        </div>
                        <div class="min-w-0 flex-1 text-left">
                            <p class="text-xs font-semibold text-zinc-800 dark:text-zinc-200 truncate">
                                {{ licenceFileName }}
                            </p>
                            <p class="text-[11px] text-zinc-500 dark:text-zinc-400">
                                <span v-if="licenceFileSize">{{ licenceFileSize }} • </span>
                                <span>{{ t('onboarding.readyForReview') }}</span>
                            </p>
                        </div>
                    </div>

                    <button
                        type="button"
                        @click="removeFile"
                        class="inline-flex size-7.5 items-center justify-center rounded-lg bg-zinc-200/80 text-zinc-600 hover:bg-red-100 hover:text-red-600 dark:bg-zinc-700 dark:text-zinc-300 dark:hover:bg-red-950/40 dark:hover:text-red-400 transition shrink-0"
                        :title="t('onboarding.removeFile')"
                    >
                        <X class="size-4" />
                    </button>
                </div>

                <input
                    ref="fileInputRef"
                    id="licence_upload"
                    type="file"
                    accept=".pdf,image/png,image/jpeg,image/jpg"
                    class="hidden"
                    @change="onFileChange"
                />
            </label>

            <InputError :message="error || form.errors.business_licence" />
        </div>
    </div>
</template>
