<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { login } from '@/routes';
import { email } from '@/routes/password';
import { useLanguage } from '@/composables/useLanguage';

const { t } = useLanguage();

defineOptions({
    layout: {
        title: 'auth.forgot.title',
        description: 'auth.forgot.description',
    },
});

defineProps<{
    status?: string;
}>();
</script>

<template>
    <Head :title="t('auth.forgot.pageTitle')" />

    <div
        v-if="status"
        class="mb-4 text-center text-sm font-medium text-green-600"
    >
        {{ status }}
    </div>

    <div class="space-y-6">
        <Form v-bind="email.form()" v-slot="{ errors, processing }">
            <div class="grid gap-2">
                <Label for="email">{{ t('common.email') }}</Label>
                <Input
                    id="email"
                    type="email"
                    name="email"
                    autocomplete="off"
                    autofocus
                    placeholder="email@example.com"
                />
                <InputError :message="errors.email" />
            </div>

            <div class="my-6 flex items-center justify-start">
                <Button
                    class="h-10 w-full rounded-xl bg-[#ff545a] px-4.5 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-[#e0454b] disabled:opacity-50"
                    :disabled="processing"
                    data-test="email-password-reset-link-button"
                >
                    <Spinner v-if="processing" />
                    {{ t('auth.forgot.sendLink') }}
                </Button>
            </div>
        </Form>

        <div class="text-zinc-500 dark:text-zinc-400 space-x-1 text-center text-sm">
            <span>{{ t('common.orReturnTo') }}</span>
            <TextLink :href="login()" class="font-semibold text-[#ff545a] hover:text-[#e0454a] hover:underline">{{ t('common.logIn') }}</TextLink>
        </div>
    </div>
</template>
