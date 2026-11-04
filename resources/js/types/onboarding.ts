import type { Component } from 'vue';

export interface ExistingProviderProfile {
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
    business_licence_path?: string;
    status?: string;
    rejection_reason?: string;
}

export interface ServiceCategory {
    id: string;
    label: string;
    swLabel: string;
    emoji?: string;
    icon?: Component;
}

export interface ProviderRegistrationFormData {
    brand_name: string;
    contact_name: string;
    nida_number: string;
    primary_phone: string;
    secondary_phone: string;
    categories: string[];
    instagram: string;
    whatsapp: string;
    tiktok: string;
    social_media: string;
    business_licence: File | null;
    description: string;
}

export interface SliderStepItem {
    step: number;
    titleKey: string;
    descKey: string;
    icon: string;
}
