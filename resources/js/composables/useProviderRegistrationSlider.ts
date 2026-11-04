import { ref, computed, watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
import {
    Headphones,
    Mic,
    UtensilsCrossed,
    Camera,
    Flower2,
    Landmark,
    Volume2,
    Car,
    ShieldCheck,
    Sparkles,
    Layers,
} from '@lucide/vue';
import { useLanguage, type TranslationKey } from '@/composables/useLanguage';
import type { ExistingProviderProfile, ProviderRegistrationFormData, ServiceCategory } from '@/types/onboarding';
import {
    brandNameError,
    contactNameError,
    descriptionError,
    instagramError,
    licenceError,
    nidaError,
    normaliseWebsite,
    phoneError,
    secondaryPhoneError,
    stepForField,
    tiktokError,
    websiteError,
    whatsappError,
    type FieldError,
} from '@/lib/providerRegistrationValidation';

export const AVAILABLE_CATEGORIES: ServiceCategory[] = [
    { id: 'Music & DJ', label: 'Music & DJ', swLabel: 'Muziki & DJ', icon: Headphones },
    { id: 'Master of Ceremony (MC)', label: 'MC / Host', swLabel: 'Mshehereshaji (MC)', icon: Mic },
    { id: 'Catering & Cake', label: 'Catering & Cake', swLabel: 'Chakula & Keki', icon: UtensilsCrossed },
    { id: 'Photography & Video', label: 'Photo & Video', swLabel: 'Picha & Video', icon: Camera },
    { id: 'Decoration & Floral', label: 'Decor & Floral', swLabel: 'Mapambo & Maua', icon: Flower2 },
    { id: 'Venue & Hall', label: 'Venue & Hall', swLabel: 'Ukumbi & Maeneo', icon: Landmark },
    { id: 'Sound & Lighting', label: 'Sound & Lighting', swLabel: 'Sauti & Taa', icon: Volume2 },
    { id: 'Transportation & Protocol', label: 'VIP Car & Protocol', swLabel: 'Magari ya Kifahari', icon: Car },
    { id: 'Security & Protocol', label: 'Event Security', swLabel: 'Ulinzi wa Matukio', icon: ShieldCheck },
    { id: 'Makeup & Beauty', label: 'Bridal & Makeup', swLabel: 'Urembo & Makeup', icon: Sparkles },
    { id: 'Other Services', label: 'Other Event Services', swLabel: 'Huduma Nyinginezo', icon: Layers },
];

export function useProviderRegistrationSlider(existingProfile?: ExistingProviderProfile | null) {
    const { t } = useLanguage();
    const activeStep = ref<number>(1);
    const maxVisitedStep = ref<number>(1);
    const stepErrors = ref<Record<string, string>>({});
    const touched = ref<Record<string, boolean>>({});

    const hasExistingLicence = computed(() => Boolean(existingProfile?.business_licence_path));
    const licenceFileName = ref<string | null>(
        existingProfile?.business_licence_path ? existingProfile.business_licence_path.split('/').pop() ?? 'Licence Document' : null
    );
    const licenceFileSize = ref<string | null>(null);

    const initialCategories = existingProfile?.categories ?? (existingProfile?.category ? [existingProfile.category] : []);

    const form = useForm<ProviderRegistrationFormData>({
        brand_name: existingProfile?.brand_name ?? '',
        contact_name: existingProfile?.contact_name ?? '',
        nida_number: String(existingProfile?.nida_number ?? ''),
        primary_phone: String(existingProfile?.primary_phone ?? ''),
        secondary_phone: String(existingProfile?.secondary_phone ?? ''),
        categories: initialCategories,
        instagram: existingProfile?.instagram ?? '',
        whatsapp: existingProfile?.whatsapp ?? '',
        tiktok: existingProfile?.tiktok ?? '',
        social_media: existingProfile?.social_media ?? '',
        business_licence: null,
        description: existingProfile?.description ?? '',
    });

    const messageFor = (error: FieldError | null): string => {
        if (!error) {
            return '';
        }

        const template = t(`onboarding.${error.key}` as TranslationKey);

        return template
            .replace(':count', String(error.count ?? 0))
            .replace(':expected', String(error.expected ?? 0));
    };

    const setError = (field: string, error: FieldError | null) => {
        const message = messageFor(error);

        if (message) {
            stepErrors.value[field] = message;
        } else {
            delete stepErrors.value[field];
        }
    };

    const markTouched = (field: string) => {
        touched.value[field] = true;
        if (field === 'social_media' && form.social_media) {
            form.social_media = normaliseWebsite(form.social_media);
        }
        validateActiveStep();
    };

    const markLive = (field: string) => {
        if (field === 'nida_number' || field === 'primary_phone' || field === 'secondary_phone') {
            if (String(form[field] ?? '').length > 0) {
                touched.value[field] = true;
            }
        } else {
            touched.value[field] = true;
        }

        validateActiveStep();
    };

    const validateActiveStep = () => {
        if (activeStep.value === 1) {
            validateBusinessInfo(false);
        } else if (activeStep.value === 3) {
            validateSocialPortfolio(false);
        }
    };

    const validateBusinessInfo = (requireEmpty: boolean): boolean => {
        const brandTouched = requireEmpty || Boolean(touched.value.brand_name);
        const contactTouched = requireEmpty || Boolean(touched.value.contact_name);
        const nidaTouched =
            requireEmpty || Boolean(touched.value.nida_number) || String(form.nida_number ?? '').length > 0;
        const primaryTouched =
            requireEmpty || Boolean(touched.value.primary_phone) || String(form.primary_phone ?? '').length > 0;
        const secondaryTouched =
            requireEmpty || Boolean(touched.value.secondary_phone) || String(form.secondary_phone ?? '').length > 0;

        if (brandTouched) {
            setError('brand_name', brandNameError(form.brand_name, requireEmpty || Boolean(touched.value.brand_name)));
        }

        if (contactTouched) {
            setError(
                'contact_name',
                contactNameError(form.contact_name, requireEmpty || Boolean(touched.value.contact_name)),
            );
        }

        if (nidaTouched) {
            setError('nida_number', nidaError(form.nida_number, requireEmpty || Boolean(touched.value.nida_number)));
        }

        if (primaryTouched) {
            setError(
                'primary_phone',
                phoneError(form.primary_phone, requireEmpty || Boolean(touched.value.primary_phone)),
            );
        }

        if (secondaryTouched) {
            setError('secondary_phone', secondaryPhoneError(form.secondary_phone, form.primary_phone));
        }

        return !['brand_name', 'contact_name', 'nida_number', 'primary_phone', 'secondary_phone'].some(
            (field) => stepErrors.value[field],
        );
    };

    const validateSocialPortfolio = (force: boolean): boolean => {
        const checks: Array<[string, () => FieldError | null]> = [
            ['instagram', () => instagramError(form.instagram)],
            ['whatsapp', () => whatsappError(form.whatsapp)],
            ['tiktok', () => tiktokError(form.tiktok)],
            ['social_media', () => websiteError(form.social_media)],
            ['description', () => descriptionError(form.description)],
        ];

        for (const [field, check] of checks) {
            // Show errors once a field was touched, or always when the description overflows.
            if (force || touched.value[field] || field === 'description') {
                setError(field, check());
            }
        }

        return !checks.some(([field]) => stepErrors.value[field]);
    };

    watch(
        () => [
            form.brand_name,
            form.contact_name,
            form.nida_number,
            form.primary_phone,
            form.secondary_phone,
            form.instagram,
            form.whatsapp,
            form.tiktok,
            form.social_media,
            form.description,
        ],
        () => validateActiveStep(),
    );

    const toggleCategory = (catId: string) => {
        delete stepErrors.value.categories;
        const index = form.categories.indexOf(catId);
        if (index === -1) {
            form.categories.push(catId);
        } else {
            form.categories.splice(index, 1);
        }
    };

    const handleFileSelected = (file: File) => {
        const error = licenceError(file, hasExistingLicence.value);

        if (error && error.key !== 'licenceRequired') {
            form.business_licence = null;
            setError('business_licence', error);
            return;
        }

        delete stepErrors.value.business_licence;
        form.business_licence = file;
        licenceFileName.value = file.name;
        const sizeMb = (file.size / (1024 * 1024)).toFixed(2);
        licenceFileSize.value = `${sizeMb} MB`;
    };

    const clearLicence = () => {
        form.business_licence = null;
        if (!hasExistingLicence.value) {
            licenceFileName.value = null;
            licenceFileSize.value = null;
            setError('business_licence', { key: 'licenceRequired' });
        } else {
            delete stepErrors.value.business_licence;
        }
    };

    const validateStep = (stepNumber: number): boolean => {
        if (stepNumber === 1) {
            touched.value.brand_name = true;
            touched.value.contact_name = true;
            touched.value.nida_number = true;
            touched.value.primary_phone = true;
            if (form.secondary_phone) {
                touched.value.secondary_phone = true;
            }

            return validateBusinessInfo(true);
        }

        if (stepNumber === 2) {
            if (form.categories.length === 0) {
                setError('categories', { key: 'categoriesRequired' });
                return false;
            }
            delete stepErrors.value.categories;
            return true;
        }

        if (stepNumber === 3) {
            if (form.social_media) {
                form.social_media = normaliseWebsite(form.social_media);
            }

            return validateSocialPortfolio(true);
        }

        if (stepNumber === 4) {
            const error = licenceError(form.business_licence, hasExistingLicence.value);
            setError('business_licence', error);
            return !error;
        }

        return true;
    };

    const nextStep = () => {
        if (!validateStep(activeStep.value)) {
            return false;
        }
        if (activeStep.value < 5) {
            activeStep.value += 1;
            if (activeStep.value > maxVisitedStep.value) {
                maxVisitedStep.value = activeStep.value;
            }
        }
        return true;
    };

    const prevStep = () => {
        if (activeStep.value > 1) {
            activeStep.value -= 1;
        }
    };

    const goToStep = (targetStep: number) => {
        if (targetStep < activeStep.value) {
            activeStep.value = targetStep;
            return;
        }
        for (let s = activeStep.value; s < targetStep; s++) {
            if (!validateStep(s)) {
                return;
            }
        }
        activeStep.value = targetStep;
        if (targetStep > maxVisitedStep.value) {
            maxVisitedStep.value = targetStep;
        }
    };

    const submitApplication = () => {
        for (let s = 1; s <= 4; s++) {
            if (!validateStep(s)) {
                activeStep.value = s;
                return;
            }
        }
        form.clearErrors();
        form.post('/onboarding/provider', {
            forceFormData: true,
            preserveScroll: true,
            onError: (errors) => {
                // Jump back to the earliest step that the server rejected.
                const fields = Object.keys(errors);
                if (fields.length > 0) {
                    activeStep.value = Math.min(...fields.map(stepForField));
                }
            },
        });
    };

    return {
        activeStep,
        maxVisitedStep,
        stepErrors,
        form,
        AVAILABLE_CATEGORIES,
        licenceFileName,
        licenceFileSize,
        hasExistingLicence,
        toggleCategory,
        handleFileSelected,
        clearLicence,
        nextStep,
        prevStep,
        goToStep,
        submitApplication,
        markTouched,
        markLive,
    };
}
