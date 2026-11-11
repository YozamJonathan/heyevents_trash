<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import AdminDashboard from '@/components/dashboards/AdminDashboard.vue';
import ClientDashboard from '@/components/dashboards/ClientDashboard.vue';
import ServiceProviderDashboard from '@/components/dashboards/ServiceProviderDashboard.vue';
import SuperAdminDashboard from '@/components/dashboards/SuperAdminDashboard.vue';
import { usePermission } from '@/composables/usePermission';
import { useWorkspaceMode } from '@/composables/useWorkspaceMode';
import { dashboard } from '@/routes';
import { ShieldCheck, Eye, EyeOff, Briefcase, PartyPopper } from '@lucide/vue';
import { useLanguage } from '@/composables/useLanguage';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Dashboard',
                href: dashboard(),
            },
        ],
    },
});

const {
    stats,
    booked_events = [],
    pending_vendors = [],
    verified_vendors = [],
    rejected_vendors = [],
    provider_profile = null,
} = defineProps<{
    stats?: {
        users_count: number;
        roles_count: number;
        permissions_count: number;
        pending_kyc_count?: number;
        verified_vendors_count?: number;
    };
    booked_events?: any[];
    pending_vendors?: any[];
    verified_vendors?: any[];
    rejected_vendors?: any[];
    provider_profile?: any;
}>();

const { user, roles, permissions, isSuperAdmin, can, canAny } = usePermission();
const { t } = useLanguage();
const { isServiceProvider, effectiveMode, setMode, isClientMode } = useWorkspaceMode();

// 1. Permission Gates for each dashboard section
const showGovernance = computed(
    () =>
        isSuperAdmin.value ||
        canAny([
            'manage platform settings',
            'manage roles and permissions',
            'manage admin accounts',
        ]),
);

const showModeration = computed(
    () =>
        canAny([
            'verify service providers',
            'moderate events',
            'resolve disputes',
        ]) && !isSuperAdmin.value,
);

const showProviderWorkspace = computed(() => {
    if (isSuperAdmin.value) return false;
    if (isServiceProvider.value) {
        return effectiveMode.value === 'provider';
    }
    return canAny([
        'create services',
        'edit services',
        'view provider earnings',
        'manage service packages',
    ]);
});

const showClientExperience = computed(() => {
    if (isSuperAdmin.value) return false;
    if (showModeration.value) return false;

    if (isServiceProvider.value) {
        return effectiveMode.value === 'client';
    }

    return true;
});

// Optional toggle for Super Admin to preview client tools on demand
const superAdminShowClientPreview = ref(false);

const primaryRole = computed(() => roles.value[0] || 'Member');

const primaryRoleName = computed(() => {
    const roleKeys: Record<
        string,
        'roles.superAdmin' | 'roles.admin' | 'roles.provider' | 'roles.client'
    > = {
        'Super Admin': 'roles.superAdmin',
        Admin: 'roles.admin',
        'Service Provider': 'roles.provider',
        Client: 'roles.client',
    };

    const translationKey = roleKeys[primaryRole.value];

    return translationKey ? t(translationKey) : t('roles.member');
});

const roleBadgeColor = computed(() => {
    switch (primaryRole.value) {
        case 'Super Admin':
            return 'bg-purple-500/10 text-purple-700 dark:text-purple-300 border-purple-500/30';
        case 'Admin':
            return 'bg-blue-500/10 text-blue-700 dark:text-blue-300 border-blue-500/30';
        case 'Service Provider':
            return 'bg-emerald-500/10 text-emerald-700 dark:text-emerald-300 border-emerald-500/30';
        default:
            return 'bg-amber-500/10 text-amber-700 dark:text-amber-300 border-amber-500/30';
    }
});
</script>

<template>
    <Head :title="t('app.dashboard')" />

    <div
        class="flex h-full flex-1 flex-col gap-8 overflow-x-auto rounded-xl p-4 md:p-6"
        v-if="user"
    >
        <!-- Clean, Single Dashboard Header -->
        <div
            class="border-border/60 flex flex-col gap-2 border-b pb-4 sm:flex-row sm:items-center sm:justify-between"
        >
            <div>
                <div class="flex items-center gap-2.5">
                    <h1
                        class="text-foreground text-xl font-bold tracking-tight sm:text-2xl"
                    >
                        {{ user.name }}
                    </h1>
                    <span
                        class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold tracking-wide"
                        :class="roleBadgeColor"
                    >
                        {{ primaryRoleName }}
                    </span>
                </div>
                <p
                    class="text-muted-foreground mt-0.5 flex items-center gap-1.5 text-xs"
                >
                    <ShieldCheck
                        class="size-3.5 text-emerald-600 dark:text-emerald-400"
                    />
                    <span>{{ t('app.permissionSession') }}</span>
                </p>
            </div>

            <!-- Super Admin quick toggle to preview client portal if desired -->
            <div v-if="isSuperAdmin" class="flex items-center gap-2">
                <button
                    type="button"
                    @click="
                        superAdminShowClientPreview =
                            !superAdminShowClientPreview
                    "
                    class="border-border bg-background text-foreground hover:bg-muted inline-flex items-center gap-1.5 rounded-lg border px-3 py-1.5 text-xs font-medium transition"
                >
                    <component
                        :is="superAdminShowClientPreview ? EyeOff : Eye"
                        class="size-3.5"
                    />
                    <span>{{
                        superAdminShowClientPreview
                            ? t('app.hideClientView')
                            : t('app.previewClientView')
                    }}</span>
                </button>
            </div>
        </div>

        <!-- Single Unified Dashboard Items: Displayed based on User's Permissions -->
        <div class="space-y-8">
            <!-- 1. Platform Governance & Infrastructure (Super Admin / Governance) -->
            <section
                v-if="showGovernance"
                aria-labelledby="section-governance"
                class="space-y-4"
            >
                <SuperAdminDashboard :stats="stats" />
            </section>

            <!-- 2. Operations & KYC Moderation (Admin Staff) -->
            <section
                v-if="showModeration || isSuperAdmin"
                aria-labelledby="section-moderation"
                class="space-y-4"
            >
                <AdminDashboard
                    :stats="stats"
                    :pending-vendors="pending_vendors"
                    :verified-vendors="verified_vendors"
                    :rejected-vendors="rejected_vendors"
                />
            </section>

            <!-- 3. Provider Workspace (Service Providers / Vendors) -->
            <section
                v-if="showProviderWorkspace"
                aria-labelledby="section-provider"
                class="space-y-4"
            >
                <ServiceProviderDashboard
                    :provider-profile="provider_profile"
                />
            </section>

            <!-- 4. Client Planning, Bookings & Portfolio (Clients & Event Organizers) -->
            <section
                v-if="
                    showClientExperience ||
                    (isSuperAdmin && superAdminShowClientPreview)
                "
                aria-labelledby="section-client"
                class="space-y-4"
            >
                <div
                    v-if="isSuperAdmin && superAdminShowClientPreview"
                    class="rounded-lg border border-purple-500/30 bg-purple-500/5 p-3 text-xs text-purple-700 dark:text-purple-300"
                >
                    {{ t('app.previewBanner') }}
                </div>

                <!-- Banner when Service Provider is in Client Mode -->
                <div
                    v-if="isServiceProvider && isClientMode"
                    class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 rounded-2xl border border-red-200/80 bg-red-50/70 p-4 text-xs dark:border-red-900/60 dark:bg-red-950/30"
                >
                    <div class="flex items-center gap-3">
                        <div
                            class="flex size-9 shrink-0 items-center justify-center rounded-xl bg-red-100 text-[#ff545a] dark:bg-red-900/50 dark:text-red-300"
                        >
                            <PartyPopper class="size-5" />
                        </div>
                        <div>
                            <h4 class="font-bold text-zinc-900 dark:text-white">
                                {{ t('app.clientModeTitle') }}
                            </h4>
                            <p class="text-zinc-600 dark:text-zinc-400 mt-0.5">
                                {{ t('app.clientModeDesc') }}
                            </p>
                        </div>
                    </div>
                    <button
                        type="button"
                        @click="setMode('provider')"
                        class="inline-flex shrink-0 items-center gap-1.5 rounded-lg border border-zinc-200/80 bg-white px-3 py-1.5 font-semibold text-zinc-800 shadow-2xs hover:bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-200 dark:hover:bg-zinc-800 transition-colors"
                    >
                        <Briefcase class="size-3.5" />
                        <span>{{ t('app.backToVendor') }}</span>
                    </button>
                </div>

                <ClientDashboard :booked-events="booked_events" />
            </section>
        </div>
    </div>
</template>
