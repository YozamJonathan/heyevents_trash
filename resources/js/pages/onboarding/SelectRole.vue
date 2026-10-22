<script setup lang="ts">
import { ref } from 'vue';
import { Head, router, useForm, Link } from '@inertiajs/vue3';
import {
    Ticket,
    Briefcase,
    ShieldCheck,
    ArrowRight,
    ArrowLeft,
    UploadCloud,
    FileText,
    CheckCircle2,
    Sun,
    Moon,
    LogOut,
    Sparkles,
    User as UserIcon,
    Globe,
    Building2,
    Lock,
} from '@lucide/vue';
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import LanguageSwitcher from '@/components/LanguageSwitcher.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { useLanguage } from '@/composables/useLanguage';
import { useAppearance } from '@/composables/useAppearance';
import { logout } from '@/routes';

const { t } = useLanguage();
const { resolvedAppearance, updateAppearance } = useAppearance();

const toggleTheme = () => {
    updateAppearance(resolvedAppearance.value === 'dark' ? 'light' : 'dark');
};

const props = defineProps<{
    existingProfile?: {
        brand_name?: string;
        contact_name?: string;
        nida_number?: string;
        social_media?: string;
        category?: string;
        description?: string;
        status?: string;
        rejection_reason?: string;
    } | null;
}>();

const currentStep = ref<'select' | 'provider-kyc'>(
    props.existingProfile?.status === 'rejected' ? 'provider-kyc' : 'select',
);
const clientSubmitting = ref(false);

const selectClient = () => {
    clientSubmitting.value = true;
    router.post(
        '/onboarding/client',
        {},
        {
            onFinish: () => {
                clientSubmitting.value = false;
            },
        },
    );
};

const kycForm = useForm<{
    brand_name: string;
    contact_name: string;
    nida_number: string;
    business_licence: File | null;
    social_media: string;
    category: string;
    description: string;
}>({
    brand_name: props.existingProfile?.brand_name ?? '',
    contact_name: props.existingProfile?.contact_name ?? '',
    nida_number: props.existingProfile?.nida_number ?? '',
    business_licence: null,
    social_media: props.existingProfile?.social_media ?? '',
    category: props.existingProfile?.category ?? '',
    description: props.existingProfile?.description ?? '',
});

const licenceFileName = ref<string | null>(null);

const onLicenceSelected = (event: Event) => {
    const target = event.target as HTMLInputElement;
    if (target.files && target.files[0]) {
        kycForm.business_licence = target.files[0];
        licenceFileName.value = target.files[0].name;
    }
};

const submitProviderKyc = () => {
    kycForm.post('/onboarding/provider', {
        forceFormData: true,
    });
};
</script>

<template>
    <Head :title="t('onboarding.title')" />

    <div
        class="relative flex min-h-screen flex-col font-['Poppins',sans-serif] text-zinc-900 antialiased selection:bg-[#ff545a] selection:text-white dark:text-zinc-100"
    >
        <!-- Persistent City Background (Synchronized with Landing & Auth) -->
        <div class="pointer-events-none fixed inset-0 z-0 overflow-hidden">
            <img
                src="/images/hero-city-day.jpg"
                :alt="t('page.cityAlt')"
                class="h-full w-full object-cover object-center dark:hidden"
            />
            <img
                src="/images/hero-city-night.jpg"
                :alt="t('page.cityAlt')"
                class="hidden h-full w-full object-cover object-center dark:block"
            />
            <div
                class="absolute inset-0 bg-slate-100/75 backdrop-blur-[2px] dark:bg-zinc-950/80"
            ></div>
        </div>

        <!-- Navigation Header -->
        <header
            class="sticky top-0 z-30 w-full border-b border-zinc-200/50 bg-white/70 backdrop-blur-md dark:border-zinc-800/50 dark:bg-zinc-950/70"
        >
            <div
                class="mx-auto flex max-w-7xl items-center justify-between px-4 py-3 sm:px-8"
            >
                <div class="flex items-center gap-3">
                    <AppLogoIcon class="size-9 drop-shadow-md" />
                    <div class="flex flex-col text-left">
                        <div
                            class="flex items-center gap-1.5 font-['Poppins',sans-serif] text-base font-extrabold tracking-tight text-zinc-900 dark:text-white"
                        >
                            <span>HEY</span>
                            <span class="text-[#ff545a]">EVENTS</span>
                        </div>
                        <span
                            class="text-[9px] font-semibold tracking-wider text-zinc-500 uppercase dark:text-zinc-400"
                        >
                            Onboarding
                        </span>
                    </div>
                </div>

                <div class="flex items-center gap-2 sm:gap-3">
                    <LanguageSwitcher compact />

                    <button
                        type="button"
                        @click="toggleTheme"
                        :title="t('nav.themeToggle')"
                        class="inline-flex size-9 items-center justify-center rounded-lg border border-zinc-200/80 bg-white/80 text-zinc-700 shadow-2xs backdrop-blur-xs transition hover:bg-zinc-100 dark:border-zinc-700/80 dark:bg-zinc-900/80 dark:text-zinc-300 dark:hover:bg-zinc-800"
                    >
                        <Sun
                            v-if="resolvedAppearance === 'dark'"
                            class="size-4 text-amber-400"
                        />
                        <Moon v-else class="size-4 text-zinc-600" />
                    </button>

                    <Link
                        :href="logout()"
                        method="post"
                        as="button"
                        class="inline-flex size-9 items-center justify-center rounded-lg border border-zinc-200/80 bg-white/80 text-zinc-600 transition hover:bg-red-50 hover:text-red-600 dark:border-zinc-750 dark:bg-zinc-900/80 dark:text-zinc-400 dark:hover:bg-red-950/40 dark:hover:text-red-400"
                        :title="t('app.logout')"
                    >
                        <LogOut class="size-4" />
                    </Link>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main
            class="relative z-10 flex flex-1 items-center justify-center px-4 py-8 sm:px-6 sm:py-12"
        >
            <!-- Step 1: Role Selection Cards -->
            <div
                v-if="currentStep === 'select'"
                class="w-full max-w-4xl space-y-8"
            >
                <div class="text-center space-y-2">
                    <span
                        class="inline-flex items-center gap-1.5 rounded-full bg-[#ff545a]/10 px-3 py-1 text-xs font-bold text-[#ff545a]"
                    >
                        <Sparkles class="size-3.5" />
                        Account Personalization
                    </span>
                    <h1
                        class="font-['Poppins',sans-serif] text-2xl sm:text-3xl font-extrabold tracking-tight text-zinc-900 dark:text-white"
                    >
                        {{ t('onboarding.title') }}
                    </h1>
                    <p class="text-sm text-zinc-500 dark:text-zinc-400 max-w-lg mx-auto">
                        {{ t('onboarding.subtitle') }}
                    </p>
                </div>

                <div class="grid gap-6 md:grid-cols-2">
                    <!-- Option 1: Client Card -->
                    <div
                        class="group relative flex flex-col justify-between rounded-2xl border border-white/80 bg-white/90 p-6 sm:p-8 backdrop-blur-xl shadow-xl shadow-zinc-900/5 transition-all duration-300 hover:-translate-y-1 hover:border-[#ff545a] hover:shadow-2xl dark:border-zinc-800/90 dark:bg-zinc-900/90 dark:hover:border-[#ff545a]"
                    >
                        <div class="space-y-4">
                            <div
                                class="inline-flex size-13 items-center justify-center rounded-2xl bg-amber-500/10 text-amber-600 dark:bg-amber-500/20 dark:text-amber-400"
                            >
                                <Ticket class="size-7" />
                            </div>

                            <div class="space-y-1.5">
                                <h3
                                    class="font-['Poppins',sans-serif] text-lg font-bold text-zinc-900 dark:text-white"
                                >
                                    {{ t('onboarding.clientTitle') }}
                                </h3>
                                <p class="text-xs sm:text-sm text-zinc-500 dark:text-zinc-400 leading-relaxed">
                                    {{ t('onboarding.clientSubtitle') }}
                                </p>
                            </div>

                            <ul class="space-y-2 text-xs text-zinc-600 dark:text-zinc-300 pt-2 border-t border-zinc-200/60 dark:border-zinc-800/80">
                                <li class="flex items-center gap-2">
                                    <CheckCircle2 class="size-4 text-emerald-500 shrink-0" />
                                    <span>Direct event package reservations</span>
                                </li>
                                <li class="flex items-center gap-2">
                                    <CheckCircle2 class="size-4 text-emerald-500 shrink-0" />
                                    <span>Browse verified caterers, MCs, DJs & venues</span>
                                </li>
                                <li class="flex items-center gap-2">
                                    <CheckCircle2 class="size-4 text-emerald-500 shrink-0" />
                                    <span>Immediate access without verification delay</span>
                                </li>
                            </ul>
                        </div>

                        <div class="pt-6 mt-4">
                            <Button
                                @click="selectClient"
                                :disabled="clientSubmitting"
                                class="h-11 w-full rounded-xl bg-[#ff545a] font-semibold text-white shadow-sm transition hover:bg-[#e0454b] disabled:opacity-50"
                            >
                                <Spinner v-if="clientSubmitting" />
                                <span>{{ t('onboarding.clientBtn') }}</span>
                                <ArrowRight class="size-4 ml-1.5" />
                            </Button>
                        </div>
                    </div>

                    <!-- Option 2: Service Provider Card -->
                    <div
                        class="group relative flex flex-col justify-between rounded-2xl border border-white/80 bg-white/90 p-6 sm:p-8 backdrop-blur-xl shadow-xl shadow-zinc-900/5 transition-all duration-300 hover:-translate-y-1 hover:border-[#ff545a] hover:shadow-2xl dark:border-zinc-800/90 dark:bg-zinc-900/90 dark:hover:border-[#ff545a]"
                    >
                        <div class="space-y-4">
                            <div
                                class="inline-flex size-13 items-center justify-center rounded-2xl bg-purple-500/10 text-purple-600 dark:bg-purple-500/20 dark:text-purple-400"
                            >
                                <Briefcase class="size-7" />
                            </div>

                            <div class="space-y-1.5">
                                <div class="flex items-center gap-2">
                                    <h3
                                        class="font-['Poppins',sans-serif] text-lg font-bold text-zinc-900 dark:text-white"
                                    >
                                        {{ t('onboarding.providerTitle') }}
                                    </h3>
                                    <span
                                        class="rounded-full bg-purple-500/15 px-2 py-0.5 text-[10px] font-bold text-purple-700 dark:text-purple-300 uppercase"
                                    >
                                        KYC Required
                                    </span>
                                </div>
                                <p class="text-xs sm:text-sm text-zinc-500 dark:text-zinc-400 leading-relaxed">
                                    {{ t('onboarding.providerSubtitle') }}
                                </p>
                            </div>

                            <ul class="space-y-2 text-xs text-zinc-600 dark:text-zinc-300 pt-2 border-t border-zinc-200/60 dark:border-zinc-800/80">
                                <li class="flex items-center gap-2">
                                    <CheckCircle2 class="size-4 text-emerald-500 shrink-0" />
                                    <span>List custom service packages and pricing</span>
                                </li>
                                <li class="flex items-center gap-2">
                                    <CheckCircle2 class="size-4 text-emerald-500 shrink-0" />
                                    <span>Receive booking contracts from organizers</span>
                                </li>
                                <li class="flex items-center gap-2">
                                    <ShieldCheck class="size-4 text-[#ff545a] shrink-0" />
                                    <span>Verified badge with NIDA & Business Licence</span>
                                </li>
                            </ul>
                        </div>

                        <div class="pt-6 mt-4">
                            <Button
                                @click="currentStep = 'provider-kyc'"
                                variant="outline"
                                class="h-11 w-full rounded-xl border-zinc-300 dark:border-zinc-700 bg-white/80 dark:bg-zinc-800/80 font-semibold text-zinc-900 dark:text-white shadow-xs transition hover:border-[#ff545a] hover:bg-zinc-50 dark:hover:bg-zinc-750"
                            >
                                <span>{{ t('onboarding.providerBtn') }}</span>
                                <ArrowRight class="size-4 ml-1.5" />
                            </Button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Step 2: Service Provider KYC Verification Form -->
            <div
                v-else
                class="w-full max-w-2xl space-y-6 animate-in fade-in slide-in-from-bottom-2"
            >
                <div class="flex items-center justify-between">
                    <button
                        type="button"
                        @click="currentStep = 'select'"
                        class="inline-flex items-center gap-1.5 text-xs font-semibold text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white transition"
                    >
                        <ArrowLeft class="size-3.5 text-[#ff545a]" />
                        <span>{{ t('onboarding.backToRoles') }}</span>
                    </button>
                    <span
                        class="rounded-full bg-[#ff545a]/10 px-2.5 py-1 text-[11px] font-bold text-[#ff545a]"
                    >
                        KYC Verification
                    </span>
                </div>

                <div
                    class="relative rounded-2xl border border-white/80 bg-white/90 p-6 sm:p-8 backdrop-blur-xl shadow-2xl shadow-zinc-900/10 dark:border-zinc-800/90 dark:bg-zinc-900/90 dark:shadow-black/50"
                >
                    <div class="mb-6 space-y-1 text-center">
                        <h2
                            class="font-['Poppins',sans-serif] text-xl sm:text-2xl font-bold tracking-tight text-zinc-900 dark:text-white"
                        >
                            {{ t('onboarding.kycTitle') }}
                        </h2>
                        <p class="text-xs sm:text-sm text-zinc-500 dark:text-zinc-400 max-w-md mx-auto">
                            {{ t('onboarding.kycSubtitle') }}
                        </p>
                    </div>

                    <form @submit.prevent="submitProviderKyc" class="space-y-4">
                        <div class="grid gap-x-4 gap-y-3.5 sm:grid-cols-2">
                            <!-- 1. Brand Name -->
                            <div class="grid gap-1.5">
                                <Label for="brand_name" class="flex items-center gap-1.5 text-xs font-semibold">
                                    <Building2 class="size-3.5 text-[#ff545a]" />
                                    <span>{{ t('onboarding.brandName') }}</span>
                                </Label>
                                <Input
                                    id="brand_name"
                                    v-model="kycForm.brand_name"
                                    required
                                    :placeholder="t('onboarding.brandNamePlaceholder')"
                                    class="h-9.5 text-xs rounded-xl"
                                />
                                <InputError :message="kycForm.errors.brand_name" />
                            </div>

                            <!-- 2. Personal / Representative Name -->
                            <div class="grid gap-1.5">
                                <Label for="contact_name" class="flex items-center gap-1.5 text-xs font-semibold">
                                    <UserIcon class="size-3.5 text-[#ff545a]" />
                                    <span>{{ t('onboarding.contactName') }}</span>
                                </Label>
                                <Input
                                    id="contact_name"
                                    v-model="kycForm.contact_name"
                                    required
                                    :placeholder="t('onboarding.contactNamePlaceholder')"
                                    class="h-9.5 text-xs rounded-xl"
                                />
                                <InputError :message="kycForm.errors.contact_name" />
                            </div>

                            <!-- 3. NIDA Number -->
                            <div class="grid gap-1.5">
                                <Label for="nida_number" class="flex items-center gap-1.5 text-xs font-semibold">
                                    <ShieldCheck class="size-3.5 text-[#ff545a]" />
                                    <span>{{ t('onboarding.nida') }}</span>
                                </Label>
                                <Input
                                    id="nida_number"
                                    v-model="kycForm.nida_number"
                                    required
                                    :placeholder="t('onboarding.nidaPlaceholder')"
                                    class="h-9.5 text-xs rounded-xl font-mono"
                                />
                                <InputError :message="kycForm.errors.nida_number" />
                            </div>

                            <!-- 4. Social Media Account -->
                            <div class="grid gap-1.5">
                                <Label for="social_media" class="flex items-center gap-1.5 text-xs font-semibold">
                                    <Globe class="size-3.5 text-[#ff545a]" />
                                    <span>{{ t('onboarding.socialMedia') }}</span>
                                </Label>
                                <Input
                                    id="social_media"
                                    v-model="kycForm.social_media"
                                    required
                                    :placeholder="t('onboarding.socialMediaPlaceholder')"
                                    class="h-9.5 text-xs rounded-xl"
                                />
                                <InputError :message="kycForm.errors.social_media" />
                            </div>

                            <!-- 5. Category Selection -->
                            <div class="grid gap-1.5">
                                <Label for="category" class="flex items-center gap-1.5 text-xs font-semibold">
                                    <Briefcase class="size-3.5 text-[#ff545a]" />
                                    <span>{{ t('onboarding.category') }}</span>
                                </Label>
                                <select
                                    id="category"
                                    v-model="kycForm.category"
                                    class="h-9.5 w-full rounded-xl border border-zinc-200 bg-background px-3 text-xs text-foreground shadow-xs dark:border-zinc-800 focus:outline-none focus:ring-2 focus:ring-[#ff545a]/30"
                                >
                                    <option value="" disabled>Select Primary Category</option>
                                    <option value="Music & DJ">Music & DJ Sound System</option>
                                    <option value="Master of Ceremony (MC)">Master of Ceremony (MC)</option>
                                    <option value="Catering & Cake">Catering & Banquet Cakes</option>
                                    <option value="Photography & Video">Photography & Cinematography</option>
                                    <option value="Decoration & Floral">Decoration & Floral Architecture</option>
                                    <option value="Venue & Hall">Event Venue & Garden</option>
                                    <option value="Transportation & Protocol">VIP Protocol & Car Hire</option>
                                    <option value="Other Event Services">Other Event Services</option>
                                </select>
                                <InputError :message="kycForm.errors.category" />
                            </div>

                            <!-- 6. Business Licence Document File Upload -->
                            <div class="grid gap-1.5">
                                <Label for="business_licence" class="flex items-center gap-1.5 text-xs font-semibold">
                                    <FileText class="size-3.5 text-[#ff545a]" />
                                    <span>{{ t('onboarding.licence') }}</span>
                                </Label>
                                <label
                                    class="flex h-9.5 cursor-pointer items-center justify-between rounded-xl border border-dashed border-zinc-300 dark:border-zinc-700 bg-zinc-50/50 dark:bg-zinc-800/50 px-3 text-xs hover:border-[#ff545a] transition"
                                >
                                    <span class="truncate text-zinc-600 dark:text-zinc-300">
                                        {{ licenceFileName || t('onboarding.licenceHelp') }}
                                    </span>
                                    <UploadCloud class="size-4 text-[#ff545a] shrink-0 ml-2" />
                                    <input
                                        id="business_licence"
                                        type="file"
                                        accept=".pdf,image/png,image/jpeg,image/jpg"
                                        class="hidden"
                                        required
                                        @change="onLicenceSelected"
                                    />
                                </label>
                                <InputError :message="kycForm.errors.business_licence" />
                            </div>

                            <!-- 7. Short Description (Full Width) -->
                            <div class="sm:col-span-2 grid gap-1.5">
                                <Label for="description" class="text-xs font-semibold">
                                    {{ t('onboarding.description') }}
                                </Label>
                                <textarea
                                    id="description"
                                    v-model="kycForm.description"
                                    rows="2"
                                    :placeholder="t('onboarding.descriptionPlaceholder')"
                                    class="w-full rounded-xl border border-zinc-200 bg-background p-2.5 text-xs text-foreground shadow-xs dark:border-zinc-800 focus:outline-none focus:ring-2 focus:ring-[#ff545a]/30 resize-none"
                                ></textarea>
                                <InputError :message="kycForm.errors.description" />
                            </div>
                        </div>

                        <div class="pt-2">
                            <Button
                                type="submit"
                                :disabled="kycForm.processing"
                                class="h-10 w-full rounded-xl bg-[#ff545a] px-4.5 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-[#e0454b] disabled:opacity-50"
                            >
                                <Spinner v-if="kycForm.processing" />
                                <span>{{ t('onboarding.submitKyc') }}</span>
                            </Button>
                        </div>
                    </form>

                    <!-- Trust Note -->
                    <div class="mt-4 flex items-center justify-center gap-1.5 text-center text-[11px] text-zinc-400 dark:text-zinc-500">
                        <Lock class="size-3 text-emerald-500 shrink-0" />
                        <span>All NIDA documents are securely encrypted for KYC verification.</span>
                    </div>
                </div>
            </div>
        </main>
    </div>
</template>
