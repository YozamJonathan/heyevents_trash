<script setup lang="ts">
import { Form, Head, usePage } from '@inertiajs/vue3';
import GoogleIcon from '@/components/GoogleIcon.vue';
import InputError from '@/components/InputError.vue';
import PasswordInput from '@/components/PasswordInput.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { login } from '@/routes';
import { store } from '@/routes/register';
import { useLanguage } from '@/composables/useLanguage';

const { t } = useLanguage();
const page = usePage();

defineProps<{
    passwordRules: string;
}>();

defineOptions({
    layout: {
        title: 'auth.register.title',
        description: 'auth.register.description',
        wide: true,
    },
});
</script>

<template>
    <Head :title="t('auth.register.pageTitle')" />

    <Form
        v-bind="store.form()"
        :reset-on-success="['password', 'password_confirmation']"
        v-slot="{ errors, processing }"
        class="flex flex-col gap-4"
    >
        <div class="grid gap-x-4 gap-y-3 sm:grid-cols-2">
            <!-- Row 1: Full Name -->
            <div class="grid gap-1.5">
                <Label for="name">{{ t('common.name') }}</Label>
                <Input
                    id="name"
                    type="text"
                    required
                    autofocus
                    :tabindex="1"
                    autocomplete="name"
                    name="name"
                    :placeholder="t('common.fullName')"
                />
                <InputError :message="errors.name" />
            </div>

            <!-- Row 1: Email -->
            <div class="grid gap-1.5">
                <Label for="email">{{ t('common.email') }}</Label>
                <Input
                    id="email"
                    type="email"
                    required
                    :tabindex="2"
                    autocomplete="email"
                    name="email"
                    placeholder="email@example.com"
                />
                <InputError :message="errors.email" />
            </div>

            <!-- Row 2: Password -->
            <div class="grid gap-1.5">
                <Label for="password">{{ t('common.password') }}</Label>
                <PasswordInput
                    id="password"
                    required
                    :tabindex="3"
                    autocomplete="new-password"
                    name="password"
                    :placeholder="t('common.password')"
                    :passwordrules="passwordRules"
                />
                <InputError :message="errors.password" />
            </div>

            <!-- Row 2: Confirm Password -->
            <div class="grid gap-1.5">
                <Label for="password_confirmation">{{
                    t('common.confirmPassword')
                }}</Label>
                <PasswordInput
                    id="password_confirmation"
                    required
                    :tabindex="4"
                    autocomplete="new-password"
                    name="password_confirmation"
                    :placeholder="t('common.confirmPassword')"
                    :passwordrules="passwordRules"
                />
                <InputError :message="errors.password_confirmation" />
            </div>

            <!-- Create Account Button -->
            <div class="sm:col-span-2 pt-1">
                <Button
                    type="submit"
                    class="h-10 w-full rounded-xl bg-[#ff545a] px-4.5 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-[#e0454b] disabled:opacity-50"
                    tabindex="5"
                    :disabled="processing"
                    data-test="register-user-button"
                >
                    <Spinner v-if="processing" />
                    {{ t('common.createAccount') }}
                </Button>
            </div>
        </div>
    </Form>

    <div class="mt-4 space-y-3">
        <div class="text-zinc-400 dark:text-zinc-500 flex items-center gap-3 text-xs">
            <div class="bg-zinc-200 dark:bg-zinc-800 h-px flex-1"></div>
            <span>{{ t('auth.register.orContinueWith') }}</span>
            <div class="bg-zinc-200 dark:bg-zinc-800 h-px flex-1"></div>
        </div>

        <InputError :message="page.props.errors?.google" />

        <a
            href="/auth/google/redirect"
            class="inline-flex h-10 w-full items-center justify-center gap-2.5 rounded-xl border border-zinc-200/90 bg-white/90 px-4 text-sm font-semibold text-zinc-800 shadow-2xs backdrop-blur-xs transition hover:bg-zinc-50 hover:border-zinc-300 dark:border-zinc-700/80 dark:bg-zinc-800/90 dark:text-zinc-100 dark:hover:bg-zinc-750 dark:hover:border-zinc-600 focus-visible:ring-2 focus-visible:outline-none"
        >
            <GoogleIcon class="size-4" />
            <span>{{ t('auth.register.googleButton') }}</span>
        </a>
    </div>

    <div class="mt-4 text-zinc-500 dark:text-zinc-400 text-center text-sm">
        {{ t('auth.register.alreadyHaveAccount') }}
        <TextLink
            :href="login()"
            class="font-semibold text-[#ff545a] hover:text-[#e0454a] hover:underline"
            :tabindex="6"
        >
            {{ t('common.logIn') }}
        </TextLink>
    </div>
</template>
