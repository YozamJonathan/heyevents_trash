<script setup lang="ts">
import { useLanguage, type TranslationKey } from '@/composables/useLanguage';

const reviews = [
    {
        name: 'Elena Rostova',
        roleKey: 'review.1.role',
        eventTypeKey: 'review.1.category',
        contentKey: 'review.1.content',
    },
    {
        name: 'David Mwangi',
        roleKey: 'review.2.role',
        eventTypeKey: 'review.2.category',
        contentKey: 'review.2.content',
    },
    {
        name: 'Sarah Jenkins',
        roleKey: 'review.3.role',
        eventTypeKey: 'review.3.category',
        contentKey: 'review.3.content',
    },
] satisfies {
    name: string;
    roleKey: TranslationKey;
    eventTypeKey: TranslationKey;
    contentKey: TranslationKey;
}[];

const { t } = useLanguage();
</script>

<template>
    <section
        id="reviews"
        class="dark:border-zinc-850 border-t border-zinc-200/60 bg-zinc-50/70 py-16 sm:py-20 dark:bg-zinc-900/30"
    >
        <div class="mx-auto max-w-7xl px-6 sm:px-8">
            <div class="max-w-xl">
                <span
                    class="text-xs font-semibold tracking-wider text-[#ff545a] uppercase"
                >
                    {{ t('reviews.eyebrow') }}
                </span>
                <h2
                    class="mt-2 font-display text-2xl sm:text-3xl font-extrabold tracking-tight text-zinc-900 dark:text-white"
                >
                    {{ t('reviews.title') }}
                </h2>
            </div>

            <!-- Reviews Grid -->
            <div class="mt-10 grid grid-cols-1 gap-6 md:grid-cols-3">
                <div
                    v-for="(rev, idx) in reviews"
                    :key="idx"
                    class="flex flex-col justify-between rounded-2xl border border-zinc-200/70 bg-white p-6 shadow-xs dark:border-zinc-800 dark:bg-zinc-900"
                >
                    <p
                        class="text-sm leading-relaxed text-zinc-600 dark:text-zinc-300"
                    >
                        "{{ t(rev.contentKey) }}"
                    </p>

                    <div
                        class="mt-6 flex items-center justify-between border-t border-zinc-100 pt-4 dark:border-zinc-800"
                    >
                        <div>
                            <div
                                class="text-xs font-bold text-zinc-900 dark:text-white"
                            >
                                {{ rev.name }}
                            </div>
                            <div class="text-[11px] text-zinc-400">
                                {{ t(rev.roleKey) }}
                            </div>
                        </div>
                        <span
                            class="rounded-md bg-zinc-100 px-2 py-0.5 text-[10px] font-medium text-zinc-600 dark:bg-zinc-800 dark:text-zinc-400"
                        >
                            {{ t(rev.eventTypeKey) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>
