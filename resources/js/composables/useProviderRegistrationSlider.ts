import { ref, computed } from 'vue';
import { useForm } from '@inertiajs/vue3';
import type { ExistingProviderProfile, ProviderRegistrationFormData } from '@/types/onboarding';

export const AVAILABLE_CATEGORIES = [
    { id: 'Music & DJ', label: 'Music & DJ', swLabel: 'Muziki & DJ', emoji: '🎵' },
    { id: 'Master of Ceremony (MC)', label: 'MC / Host', swLabel: 'Mshehereshaji (MC)', emoji: '🎤' },
    { id: 'Catering & Cake', label: 'Catering & Cake', swLabel: 'Chakula & Keki', emoji: '🎂' },
    { id: 'Photography & Video', label: 'Photo & Video', swLabel: 'Picha & Video', emoji: '📸' },
    { id: 'Decoration & Floral', label: 'Decor & Floral', swLabel: 'Mapambo & Maua', emoji: '💐' },
    { id: 'Venue & Hall', label: 'Venue & Hall', swLabel: 'Ukumbi & Maeneo', emoji: '🏛️' },
    { id: 'Sound & Lighting', label: 'Sound & Lighting', swLabel: 'Sauti & Taa', emoji: '💡' },
    { id: 'Transportation & Protocol', label: 'VIP Car & Protocol', swLabel: 'Magari ya Kifahari', emoji: '🚗' },
    { id: 'Security & Protocol', label: 'Event Security', swLabel: 'Ulinzi wa Matukio', emoji: '🛡️' },
    { id: 'Makeup & Beauty', label: 'Bridal & Makeup', swLabel: 'Urembo & Makeup', emoji: '💄' },
    { id: 'Other Services', label: 'Other Event Services', swLabel: 'Huduma Nyinginezo', emoji: '✨' },
];

export function useProviderRegistrationSlider(existingProfile?: ExistingProviderProfile | null) {
    const activeStep = ref<number>(1);
    const maxVisitedStep = ref<number>(1);
    const stepErrors = ref<Record<string, string>>({});

    const hasExistingLicence = computed(() => Boolean(existingProfile?.business_licence_path));
    const licenceFileName = ref<string | null>(
        existingProfile?.business_licence_path ? existingProfile.business_licence_path.split('/').pop() ?? 'Licence Document' : null
    );
    const licenceFileSize = ref<string | null>(null);

    const initialCategories = existingProfile?.categories ?? (existingProfile?.category ? [existingProfile.category] : []);

    const form = useForm<ProviderRegistrationFormData>({
        brand_name: existingProfile?.brand_name ?? '',
        contact_name: existingProfile?.contact_name ?? '',
        nida_number: existingProfile?.nida_number ?? '',
        primary_phone: existingProfile?.primary_phone ?? '',
        secondary_phone: existingProfile?.secondary_phone ?? '',
        categories: initialCategories,
        instagram: existingProfile?.instagram ?? '',
        whatsapp: existingProfile?.whatsapp ?? '',
        tiktok: existingProfile?.tiktok ?? '',
        social_media: existingProfile?.social_media ?? '',
        business_licence: null,
        description: existingProfile?.description ?? '',
    });

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
        }
    };

    const validateStep = (stepNumber: number): boolean => {
        stepErrors.value = {};

        if (stepNumber === 1) {
            if (!form.brand_name.trim()) {
                stepErrors.value.brand_name = 'Brand / Business Name is required.';
            }
            if (!form.contact_name.trim()) {
                stepErrors.value.contact_name = 'Contact Person Name is required.';
            }
            if (!form.nida_number.trim()) {
                stepErrors.value.nida_number = 'NIDA Number is required.';
            } else if (form.nida_number.replace(/[^0-9]/g, '').length < 8) {
                stepErrors.value.nida_number = 'Please enter a valid NIDA identification number.';
            }
            if (!form.primary_phone.trim()) {
                stepErrors.value.primary_phone = 'Primary phone number is required.';
            }
            return Object.keys(stepErrors.value).length === 0;
        }

        if (stepNumber === 2) {
            if (form.categories.length === 0) {
                stepErrors.value.categories = 'Please select at least one service category.';
                return false;
            }
            return true;
        }

        if (stepNumber === 3) {
            // Step 3 optional / format checks
            return true;
        }

        if (stepNumber === 4) {
            if (!form.business_licence && !hasExistingLicence.value) {
                stepErrors.value.business_licence = 'Please upload a business licence document.';
                return false;
            }
            return true;
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
        // Validate each step in between before jumping forward
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
        form.post('/onboarding/provider', {
            forceFormData: true,
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
    };
}
