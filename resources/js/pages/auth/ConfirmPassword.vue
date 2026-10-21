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
                    class="h-10 w-full rounded-xl bg-gradient-to-r from-[#ff545a] to-[#ff2a55] font-semibold text-white shadow-md shadow-[#ff545a]/25 transition-all hover:from-[#e0454a] hover:to-[#e02045] hover:shadow-lg hover:shadow-[#ff545a]/35 active:scale-[0.99] disabled:opacity-50"
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
