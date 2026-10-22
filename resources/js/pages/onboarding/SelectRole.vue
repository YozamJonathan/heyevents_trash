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
    User as UserIcon,
    Building2,
    Lock,
    Phone,
    PhoneCall,
    Check,
    X,
    FileCheck,
    Camera,
    MessageCircle,
    Video,
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
        primary_phone?: string;
        secondary_phone?: string;
        categories?: string[];
        category?: string;
        instagram?: string;
        whatsapp?: string;
        tiktok?: string;
        social_media?: string;
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

const availableCategories = [
    { id: 'Music & DJ', label: 'Music & DJ', emoji: '🎵' },
    { id: 'Master of Ceremony (MC)', label: 'MC / Host', emoji: '🎤' },
    { id: 'Catering & Cake', label: 'Catering & Cake', emoji: '🎂' },
    { id: 'Photography & Video', label: 'Photo & Video', emoji: '📸' },
    { id: 'Decoration & Floral', label: 'Decor & Floral', emoji: '💐' },
    { id: 'Venue & Hall', label: 'Venue & Hall', emoji: '🏛️' },
    { id: 'Sound & Lighting', label: 'Sound & Lighting', emoji: '💡' },
    { id: 'Transportation & Protocol', label: 'VIP Car & Protocol', emoji: '🚗' },
    { id: 'Security & Protocol', label: 'Event Security', emoji: '🛡️' },
    { id: 'Makeup & Beauty', label: 'Bridal & Makeup', emoji: '💄' },
    { id: 'Other Services', label: 'Other Event Services', emoji: '✨' },
];

const kycForm = useForm<{
    brand_name: string;
    contact_name: string;
    nida_number: string;
    primary_phone: string;
    secondary_phone: string;
    categories: string[];
    instagram: string;
    whatsapp: string;
    tiktok: string;
    business_licence: File | null;
    description: string;
}>({
    brand_name: props.existingProfile?.brand_name ?? '',
    contact_name: props.existingProfile?.contact_name ?? '',
    nida_number: props.existingProfile?.nida_number ?? '',
    primary_phone: (props.existingProfile as any)?.primary_phone ?? '',
    secondary_phone: (props.existingProfile as any)?.secondary_phone ?? '',
    categories: (props.existingProfile as any)?.categories ?? ((props.existingProfile as any)?.category ? [(props.existingProfile as any).category] : []),
    instagram: (props.existingProfile as any)?.instagram ?? '',
    whatsapp: (props.existingProfile as any)?.whatsapp ?? '',
    tiktok: (props.existingProfile as any)?.tiktok ?? '',
    business_licence: null,
    description: props.existingProfile?.description ?? '',
});

function toggleCategory(catId: string) {
    const idx = kycForm.categories.indexOf(catId);
    if (idx === -1) {
        kycForm.categories.push(catId);
    } else {
        kycForm.categories.splice(idx, 1);
    }
}

const licenceFileName = ref<string | null>(null);
const licenceFileSize = ref<string | null>(null);
const fileInputRef = ref<HTMLInputElement | null>(null);

const onLicenceSelected = (event: Event) => {
    const target = event.target as HTMLInputElement;
    if (target.files && target.files[0]) {
        const file = target.files[0];
        kycForm.business_licence = file;
        licenceFileName.value = file.name;
        const sizeMb = (file.size / (1024 * 1024)).toFixed(2);
        licenceFileSize.value = `${sizeMb} MB`;
    }
};

const clearLicence = (event?: Event) => {
    if (event) event.stopPropagation();
    kycForm.business_licence = null;
    licenceFileName.value = null;
    licenceFileSize.value = null;
    if (fileInputRef.value) {
        fileInputRef.value.value = '';
    }
};

const submitProviderKyc = () => {
    if (kycForm.categories.length === 0) {
        kycForm.setError('categories' as any, 'Please select at least one service category.');
        return;
    }
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
                class="w-full max-w-3xl space-y-6 animate-in fade-in slide-in-from-bottom-2"
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

                    <form @submit.prevent="submitProviderKyc" class="space-y-6">
                        <!-- 1. BUSINESS & REPRESENTATIVE IDENTITY -->
                        <div class="space-y-3">
                            <h3 class="text-xs font-bold uppercase tracking-wider text-zinc-400 dark:text-zinc-500">
                                1. Business & Contact Information
                            </h3>

                            <div class="grid gap-x-4 gap-y-3.5 sm:grid-cols-2">
                                <!-- Brand Name -->
                                <div class="grid gap-1.5 min-w-0">
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

                                <!-- Contact Name -->
                                <div class="grid gap-1.5 min-w-0">
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

                                <!-- NIDA Number -->
                                <div class="grid gap-1.5 min-w-0 sm:col-span-2">
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

                                <!-- Primary Phone Number -->
                                <div class="grid gap-1.5 min-w-0">
                                    <Label for="primary_phone" class="flex items-center gap-1.5 text-xs font-semibold">
                                        <Phone class="size-3.5 text-[#ff545a]" />
                                        <span>{{ t('onboarding.primaryPhone') }}</span>
                                    </Label>
                                    <Input
                                        id="primary_phone"
                                        v-model="kycForm.primary_phone"
                                        required
                                        :placeholder="t('onboarding.primaryPhonePlaceholder')"
                                        class="h-9.5 text-xs rounded-xl font-mono"
                                    />
                                    <InputError :message="kycForm.errors.primary_phone" />
                                </div>

                                <!-- Secondary Phone Number (Optional) -->
                                <div class="grid gap-1.5 min-w-0">
                                    <Label for="secondary_phone" class="flex items-center gap-1.5 text-xs font-semibold">
                                        <PhoneCall class="size-3.5 text-zinc-400" />
                                        <span>{{ t('onboarding.secondaryPhone') }}</span>
                                    </Label>
                                    <Input
                                        id="secondary_phone"
                                        v-model="kycForm.secondary_phone"
                                        :placeholder="t('onboarding.secondaryPhonePlaceholder')"
                                        class="h-9.5 text-xs rounded-xl font-mono"
                                    />
                                    <InputError :message="kycForm.errors.secondary_phone" />
                                </div>
                            </div>
                        </div>

                        <!-- 2. SERVICES OFFERED (SELECT MULTIPLE) -->
                        <div class="space-y-2.5 pt-2 border-t border-zinc-200/60 dark:border-zinc-800/80">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="text-xs font-bold uppercase tracking-wider text-zinc-400 dark:text-zinc-500">
                                        2. {{ t('onboarding.services') }}
                                    </h3>
                                    <p class="text-[11px] text-zinc-500 dark:text-zinc-400">
                                        {{ t('onboarding.servicesSubtitle') }}
                                    </p>
                                </div>
                                <span
                                    v-if="kycForm.categories.length > 0"
                                    class="rounded-full bg-[#ff545a]/15 px-2.5 py-0.5 text-[11px] font-bold text-[#ff545a]"
                                >
                                    {{ kycForm.categories.length }} selected
                                </span>
                            </div>

                            <div class="flex flex-wrap gap-2">
                                <button
                                    v-for="cat in availableCategories"
                                    :key="cat.id"
                                    type="button"
                                    @click="toggleCategory(cat.id)"
                                    class="inline-flex items-center gap-1.5 rounded-xl border px-3 py-1.5 text-xs transition duration-200"
                                    :class="
                                        kycForm.categories.includes(cat.id)
                                            ? 'border-[#ff545a] bg-[#ff545a]/10 text-[#ff545a] font-semibold dark:bg-[#ff545a]/20 shadow-xs ring-1 ring-[#ff545a]/30'
                                            : 'border-zinc-200/80 bg-white/60 text-zinc-700 hover:border-zinc-400 dark:border-zinc-750 dark:bg-zinc-800/60 dark:text-zinc-300 dark:hover:border-zinc-600'
                                    "
                                >
                                    <span>{{ cat.emoji }}</span>
                                    <span>{{ cat.label }}</span>
                                    <Check
                                        v-if="kycForm.categories.includes(cat.id)"
                                        class="size-3.5 text-[#ff545a] ml-0.5"
                                    />
                                </button>
                            </div>
                            <InputError :message="kycForm.errors.categories" />
                        </div>

                        <!-- 3. SOCIAL MEDIA & ONLINE CHANNELS -->
                        <div class="space-y-3 pt-2 border-t border-zinc-200/60 dark:border-zinc-800/80">
                            <div>
                                <h3 class="text-xs font-bold uppercase tracking-wider text-zinc-400 dark:text-zinc-500">
                                    3. Social Media & Portfolios
                                </h3>
                                <p class="text-[11px] text-zinc-500 dark:text-zinc-400">
                                    Showcase your recent events, videos, and portfolio to speed up verification.
                                </p>
                            </div>

                            <div class="grid gap-3 sm:grid-cols-3">
                                <!-- Instagram -->
                                <div class="grid gap-1.5 min-w-0">
                                    <Label for="instagram" class="flex items-center gap-1.5 text-xs font-semibold">
                                        <Camera class="size-3.5 text-pink-500" />
                                        <span>{{ t('onboarding.instagram') }}</span>
                                    </Label>
                                    <Input
                                        id="instagram"
                                        v-model="kycForm.instagram"
                                        :placeholder="t('onboarding.instagramPlaceholder')"
                                        class="h-9.5 text-xs rounded-xl"
                                    />
                                    <InputError :message="kycForm.errors.instagram" />
                                </div>

                                <!-- WhatsApp -->
                                <div class="grid gap-1.5 min-w-0">
                                    <Label for="whatsapp" class="flex items-center gap-1.5 text-xs font-semibold">
                                        <MessageCircle class="size-3.5 text-emerald-500" />
                                        <span>{{ t('onboarding.whatsapp') }}</span>
                                    </Label>
                                    <Input
                                        id="whatsapp"
                                        v-model="kycForm.whatsapp"
                                        :placeholder="t('onboarding.whatsappPlaceholder')"
                                        class="h-9.5 text-xs rounded-xl"
                                    />
                                    <InputError :message="kycForm.errors.whatsapp" />
                                </div>

                                <!-- TikTok -->
                                <div class="grid gap-1.5 min-w-0">
                                    <Label for="tiktok" class="flex items-center gap-1.5 text-xs font-semibold">
                                        <Video class="size-3.5 text-indigo-500" />
                                        <span>{{ t('onboarding.tiktok') }}</span>
                                    </Label>
                                    <Input
                                        id="tiktok"
                                        v-model="kycForm.tiktok"
                                        :placeholder="t('onboarding.tiktokPlaceholder')"
                                        class="h-9.5 text-xs rounded-xl"
                                    />
                                    <InputError :message="kycForm.errors.tiktok" />
                                </div>
                            </div>
                        </div>

                        <!-- 4. BUSINESS LICENCE DOCUMENT (OVERFLOW-PROOF FULL WIDTH DROPZONE) -->
                        <div class="space-y-3 pt-2 border-t border-zinc-200/60 dark:border-zinc-800/80">
                            <div>
                                <h3 class="text-xs font-bold uppercase tracking-wider text-zinc-400 dark:text-zinc-500">
                                    4. Official Business Licence
                                </h3>
                                <p class="text-[11px] text-zinc-500 dark:text-zinc-400">
                                    Upload official government business registration or tax certificate.
                                </p>
                            </div>

                            <div class="w-full min-w-0 overflow-hidden">
                                <label
                                    for="business_licence"
                                    class="group relative flex flex-col items-center justify-center rounded-2xl border-2 border-dashed border-zinc-300/80 bg-zinc-50/70 p-5 text-center transition hover:border-[#ff545a] hover:bg-zinc-100/50 dark:border-zinc-700/80 dark:bg-zinc-800/40 dark:hover:bg-zinc-800/60 cursor-pointer min-w-0 overflow-hidden"
                                >
                                    <!-- When no file is selected -->
                                    <div v-if="!licenceFileName" class="flex flex-col items-center gap-2 min-w-0 max-w-full">
                                        <div
                                            class="rounded-full bg-[#ff545a]/10 p-3 text-[#ff545a] transition group-hover:scale-105"
                                        >
                                            <UploadCloud class="size-6" />
                                        </div>
                                        <div class="space-y-0.5 min-w-0 max-w-full">
                                            <p class="text-xs font-semibold text-zinc-800 dark:text-zinc-200 truncate">
                                                Click to upload or drag & drop Business Licence
                                            </p>
                                            <p class="text-[11px] text-zinc-500 dark:text-zinc-400">
                                                PDF, PNG, or JPG (maximum 5MB)
                                            </p>
                                        </div>
                                    </div>

                                    <!-- When file is selected -->
                                    <div v-else class="flex items-center justify-between w-full min-w-0 gap-3 px-2">
                                        <div class="flex items-center gap-3 min-w-0 flex-1">
                                            <div class="rounded-xl bg-emerald-500/10 p-2 text-emerald-600 dark:text-emerald-400 shrink-0">
                                                <FileCheck class="size-6" />
                                            </div>
                                            <div class="min-w-0 flex-1 text-left">
                                                <p class="text-xs font-semibold text-zinc-800 dark:text-zinc-200 truncate">
                                                    {{ licenceFileName }}
                                                </p>
                                                <p v-if="licenceFileSize" class="text-[10px] text-zinc-500 dark:text-zinc-400">
                                                    {{ licenceFileSize }} • Ready for verification
                                                </p>
                                            </div>
                                        </div>

                                        <button
                                            type="button"
                                            @click="clearLicence"
                                            class="inline-flex size-7 items-center justify-center rounded-lg bg-zinc-200/80 text-zinc-600 hover:bg-red-100 hover:text-red-600 dark:bg-zinc-700 dark:text-zinc-300 dark:hover:bg-red-950/40 dark:hover:text-red-400 transition shrink-0"
                                            title="Remove file"
                                        >
                                            <X class="size-4" />
                                        </button>
                                    </div>

                                    <input
                                        ref="fileInputRef"
                                        id="business_licence"
                                        type="file"
                                        accept=".pdf,image/png,image/jpeg,image/jpg"
                                        class="hidden"
                                        @change="onLicenceSelected"
                                    />
                                </label>
                                <InputError :message="kycForm.errors.business_licence" />
                            </div>
                        </div>

                        <!-- 5. DESCRIPTION -->
                        <div class="space-y-1.5 pt-2 border-t border-zinc-200/60 dark:border-zinc-800/80">
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

                        <!-- Submit Button -->
                        <div class="pt-2">
                            <Button
                                type="submit"
                                :disabled="kycForm.processing"
                                class="h-10.5 w-full rounded-xl bg-[#ff545a] px-4.5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-[#e0454b] disabled:opacity-50"
                            >
                                <Spinner v-if="kycForm.processing" />
                                <span>{{ t('onboarding.submitKyc') }}</span>
                            </Button>
                        </div>
                    </form>

                    <!-- Trust Note -->
                    <div class="mt-4 flex items-center justify-center gap-1.5 text-center text-[11px] text-zinc-400 dark:text-zinc-500">
                        <Lock class="size-3 text-emerald-500 shrink-0" />
                        <span>All NIDA documents and business licences are securely encrypted for KYC verification.</span>
                    </div>
                </div>
            </div>
        </main>
    </div>
</template>
