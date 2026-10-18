<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import AdminDashboard from '@/components/dashboards/AdminDashboard.vue';
import ClientDashboard from '@/components/dashboards/ClientDashboard.vue';
import ServiceProviderDashboard from '@/components/dashboards/ServiceProviderDashboard.vue';
import SuperAdminDashboard from '@/components/dashboards/SuperAdminDashboard.vue';
import { usePermission } from '@/composables/usePermission';
import { dashboard } from '@/routes';
import { ShieldCheck, Eye, EyeOff } from '@lucide/vue';

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

const { stats, booked_events = [] } = defineProps<{
    stats?: {
        users_count: number;
        roles_count: number;
        permissions_count: number;
    };
    booked_events?: any[];
}>();

const { user, roles, permissions, isSuperAdmin, can, canAny } = usePermission();

// 1. Permission Gates for each dashboard section
const showGovernance = computed(() =>
    isSuperAdmin.value || canAny(['manage platform settings', 'manage roles and permissions', 'manage admin accounts'])
);

const showModeration = computed(() =>
    canAny(['verify service providers', 'moderate events', 'resolve disputes']) && !isSuperAdmin.value
);

const showProviderWorkspace = computed(() =>
    canAny(['create services', 'edit services', 'view provider earnings', 'manage service packages']) && !isSuperAdmin.value
);

const showClientExperience = computed(() => {
    // Show client tools if user has client booking permissions
    if (canAny(['browse services', 'create bookings', 'view my bookings'])) {
        // If not super admin or if specifically a client/organizer
        return !isSuperAdmin.value && !showModeration.value;
    }
    // Default fallback if no admin or provider rights
    return !showGovernance.value && !showModeration.value && !showProviderWorkspace.value;
});

// Optional toggle for Super Admin to preview client tools on demand
const superAdminShowClientPreview = ref(false);

const primaryRoleName = computed(() => {
    return roles.value[0] || 'Member';
});

const roleBadgeColor = computed(() => {
    switch (primaryRoleName.value) {
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
    <Head title="Dashboard" />

    <div class="flex h-full flex-1 flex-col gap-8 overflow-x-auto rounded-xl p-4 md:p-6" v-if="user">
        <!-- Clean, Single Dashboard Header -->
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between border-b border-border/60 pb-4">
            <div>
                <div class="flex items-center gap-2.5">
                    <h1 class="text-xl font-bold tracking-tight text-foreground sm:text-2xl">
                        {{ user.name }}
                    </h1>
                    <span
                        class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold tracking-wide"
                        :class="roleBadgeColor"
                    >
                        {{ primaryRoleName }}
                    </span>
                </div>
                <p class="mt-0.5 text-xs text-muted-foreground flex items-center gap-1.5">
                    <ShieldCheck class="size-3.5 text-emerald-600 dark:text-emerald-400" />
                    <span>Permission-governed workspace • Active Session</span>
                </p>
            </div>

            <!-- Super Admin quick toggle to preview client portal if desired -->
            <div v-if="isSuperAdmin" class="flex items-center gap-2">
                <button
                    type="button"
                    @click="superAdminShowClientPreview = !superAdminShowClientPreview"
                    class="inline-flex items-center gap-1.5 rounded-lg border border-border bg-background px-3 py-1.5 text-xs font-medium text-foreground transition hover:bg-muted"
                >
                    <component :is="superAdminShowClientPreview ? EyeOff : Eye" class="size-3.5" />
                    <span>{{ superAdminShowClientPreview ? 'Hide Client View' : 'Preview Client View' }}</span>
                </button>
            </div>
        </div>

        <!-- Single Unified Dashboard Items: Displayed based on User's Permissions -->
        <div class="space-y-8">
            <!-- 1. Platform Governance & Infrastructure (Super Admin / Governance) -->
            <section v-if="showGovernance" aria-labelledby="section-governance" class="space-y-4">
                <SuperAdminDashboard :stats="stats" />
            </section>

            <!-- 2. Operations & KYC Moderation (Admin Staff) -->
            <section v-if="showModeration" aria-labelledby="section-moderation" class="space-y-4">
                <AdminDashboard :stats="stats" />
            </section>

            <!-- 3. Provider Workspace (Service Providers / Vendors) -->
            <section v-if="showProviderWorkspace" aria-labelledby="section-provider" class="space-y-4">
                <ServiceProviderDashboard />
            </section>

            <!-- 4. Client Planning, Bookings & Portfolio (Clients & Event Organizers) -->
            <section v-if="showClientExperience || (isSuperAdmin && superAdminShowClientPreview)" aria-labelledby="section-client" class="space-y-4">
                <div v-if="isSuperAdmin && superAdminShowClientPreview" class="rounded-lg border border-purple-500/30 bg-purple-500/5 p-3 text-xs text-purple-700 dark:text-purple-300">
                    Previewing Client & Organizer Experience as Super Admin
                </div>
                <ClientDashboard :booked-events="booked_events" />
            </section>
        </div>
    </div>
</template>
