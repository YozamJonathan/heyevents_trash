<script setup lang="ts">
import { Form, Head, usePage } from '@inertiajs/vue3';
import GoogleIcon from '@/components/GoogleIcon.vue';
import InputError from '@/components/InputError.vue';
import PasswordInput from '@/components/PasswordInput.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
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
    },
});
</script>

<template>
    <Head :title="t('auth.register.pageTitle')" />

    <Form
        v-bind="store.form()"
        :reset-on-success="['password', 'password_confirmation']"
        v-slot="{ errors, processing }"
        class="flex flex-col gap-6"
    >
        <div class="grid gap-6">
            <div class="grid gap-2">
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

            <div class="grid gap-2">
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

            <div class="grid gap-2">
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

            <div class="grid gap-2">
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

            <Button
                type="submit"
                class="mt-2 w-full"
                tabindex="5"
                :disabled="processing"
                data-test="register-user-button"
            >
                <Spinner v-if="processing" />
                {{ t('common.createAccount') }}
            </Button>
        </div>
    </Form>

    <div class="mt-6 space-y-4">
        <div class="text-muted-foreground flex items-center gap-3 text-xs">
            <div class="bg-border h-px flex-1"></div>
            <span>{{ t('auth.register.orContinueWith') }}</span>
            <div class="bg-border h-px flex-1"></div>
        </div>

        <InputError :message="page.props.errors?.google" />

        <a
            href="/auth/google/redirect"
            class="border-input bg-background hover:bg-accent hover:text-accent-foreground focus-visible:ring-ring inline-flex h-10 w-full items-center justify-center gap-2 rounded-md border px-4 text-sm font-medium shadow-xs transition-colors focus-visible:ring-2 focus-visible:outline-none"
        >
            <GoogleIcon class="h-4 w-4" />
            {{ t('auth.register.googleButton') }}
        </a>
    </div>

    <div class="text-muted-foreground text-center text-sm">
        {{ t('auth.register.alreadyHaveAccount') }}
        <TextLink
            :href="login()"
            class="underline underline-offset-4"
            :tabindex="6"
        >
            {{ t('common.logIn') }}
        </TextLink>
    </div>
</template>
