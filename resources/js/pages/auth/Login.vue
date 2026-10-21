<script setup lang="ts">
import { Form, Head, usePage } from '@inertiajs/vue3';
import GoogleIcon from '@/components/GoogleIcon.vue';
import InputError from '@/components/InputError.vue';
import PasswordInput from '@/components/PasswordInput.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { register } from '@/routes';
import { store } from '@/routes/login';
import { request } from '@/routes/password';
import { useLanguage } from '@/composables/useLanguage';

const { t } = useLanguage();
const page = usePage();

defineOptions({
    layout: {
        title: 'auth.login.title',
        description: 'auth.login.description',
    },
});

defineProps<{
    status?: string;
    canResetPassword: boolean;
}>();
</script>

<template>
    <Head :title="t('auth.login.pageTitle')" />

    <div
        v-if="status"
        class="mb-4 text-center text-sm font-medium text-green-600"
    >
        {{ status }}
    </div>

    <Form
        v-bind="store.form()"
        :reset-on-success="['password']"
        v-slot="{ errors, processing }"
        class="flex flex-col gap-6"
    >
        <div class="grid gap-6">
            <div class="grid gap-2">
                <Label for="email">{{ t('common.email') }}</Label>
                <Input
                    id="email"
                    type="email"
                    name="email"
                    required
                    autofocus
                    :tabindex="1"
                    autocomplete="email"
                    placeholder="email@example.com"
                />
                <InputError :message="errors.email" />
            </div>

            <div class="grid gap-2">
                <div class="flex items-center justify-between">
                    <Label for="password">{{ t('common.password') }}</Label>
                    <TextLink
                        v-if="canResetPassword"
                        :href="request()"
                        class="text-sm"
                        :tabindex="5"
                    >
                        {{ t('common.forgotPassword') }}
                    </TextLink>
                </div>
                <PasswordInput
                    id="password"
                    name="password"
                    required
                    :tabindex="2"
                    autocomplete="current-password"
                    :placeholder="t('common.password')"
                />
                <InputError :message="errors.password" />
            </div>

            <div class="flex items-center justify-between">
                <Label for="remember" class="flex items-center space-x-3">
                    <Checkbox id="remember" name="remember" :tabindex="3" />
                    <span>{{ t('common.rememberMe') }}</span>
                </Label>
            </div>

            <Button
                type="submit"
                class="mt-2 h-10 w-full rounded-xl bg-[#ff545a] px-4.5 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-[#e0454b] disabled:opacity-50"
                :tabindex="4"
                :disabled="processing"
                data-test="login-button"
            >
                <Spinner v-if="processing" />
                {{ t('common.logIn') }}
            </Button>
        </div>
    </Form>

    <div class="mt-6 space-y-4">
        <div class="text-zinc-400 dark:text-zinc-500 flex items-center gap-3 text-xs">
            <div class="bg-zinc-200 dark:bg-zinc-800 h-px flex-1"></div>
            <span>{{ t('auth.login.orContinueWith') }}</span>
            <div class="bg-zinc-200 dark:bg-zinc-800 h-px flex-1"></div>
        </div>

        <InputError :message="page.props.errors?.google" />

        <a
            href="/auth/google/redirect"
            class="inline-flex h-10 w-full items-center justify-center gap-2.5 rounded-xl border border-zinc-200/90 bg-white/90 px-4 text-sm font-semibold text-zinc-800 shadow-2xs backdrop-blur-xs transition hover:bg-zinc-50 hover:border-zinc-300 dark:border-zinc-700/80 dark:bg-zinc-800/90 dark:text-zinc-100 dark:hover:bg-zinc-750 dark:hover:border-zinc-600 focus-visible:ring-2 focus-visible:outline-none"
        >
            <GoogleIcon class="size-4" />
            <span>{{ t('auth.login.googleButton') }}</span>
        </a>
    </div>

    <div class="text-zinc-500 dark:text-zinc-400 text-center text-sm">
        {{ t('auth.login.noAccount') }}
        <TextLink :href="register()" :tabindex="5" class="font-semibold text-[#ff545a] hover:text-[#e0454a] hover:underline">{{
            t('common.signUp')
        }}</TextLink>
    </div>
</template>
