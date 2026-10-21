<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import PasswordInput from '@/components/PasswordInput.vue';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { store } from '@/routes/password/confirm';
import { useLanguage } from '@/composables/useLanguage';

const { t } = useLanguage();

defineOptions({
    layout: {
        title: 'auth.confirm.title',
        description: 'auth.confirm.description',
    },
});
</script>

<template>
    <Head :title="t('auth.confirm.pageTitle')" />

    <Form
        v-bind="store.form()"
        reset-on-success
        v-slot="{ errors, processing }"
    >
        <div class="space-y-6">
            <div class="grid gap-2">
                <Label htmlFor="password">{{ t('common.password') }}</Label>
                <PasswordInput
                    id="password"
                    name="password"
                    class="mt-1 block w-full"
                    required
                    autocomplete="current-password"
                    autofocus
                />

                <InputError :message="errors.password" />
            </div>

            <div class="flex items-center">
                <Button
                    class="h-10 w-full rounded-xl bg-[#ff545a] px-4.5 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-[#e0454b] disabled:opacity-50"
                    :disabled="processing"
                    data-test="confirm-password-button"
                >
                    <Spinner v-if="processing" />
                    {{ t('auth.confirm.submit') }}
                </Button>
            </div>
        </div>
    </Form>
</template>
