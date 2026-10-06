<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';
import AdminDashboard from '@/components/dashboards/AdminDashboard.vue';
import ClientDashboard from '@/components/dashboards/ClientDashboard.vue';
import ServiceProviderDashboard from '@/components/dashboards/ServiceProviderDashboard.vue';
import SuperAdminDashboard from '@/components/dashboards/SuperAdminDashboard.vue';
import { usePermission } from '@/composables/usePermission';
import { dashboard } from '@/routes';
import { ShieldAlert, ShieldCheck, Briefcase, User, Sparkles } from '@lucide/vue';

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

const { user, roles, permissions, isSuperAdmin, can, canAny } = usePermission();

// 1. Evaluate permission capabilities
const hasGovernanceAccess = computed(() =>
    isSuperAdmin.value || canAny(['manage platform settings', 'manage roles and permissions', 'view audit logs', 'manage commission rates'])
);

const hasModerationAccess = computed(() =>
    !isSuperAdmin.value && canAny(['verify service providers', 'moderate events', 'resolve disputes', 'view platform analytics'])
);

const hasProviderAccess = computed(() =>
    !isSuperAdmin.value && canAny(['create services', 'edit services', 'view provider bookings', 'view provider earnings', 'manage service packages'])
);

const hasClientAccess = computed(() =>
    !isSuperAdmin.value && canAny(['browse services', 'create bookings', 'view my bookings', 'submit reviews'])
);

// 2. Build unlocked dashboard views based on actual permissions
const availableViews = computed(() => {
    const views: { id: string; label: string; icon: any }[] = [];

    if (hasGovernanceAccess.value) {
        views.push({ id: 'governance', label: 'Platform Governance', icon: ShieldAlert });
    }
    if (hasModerationAccess.value) {
        views.push({ id: 'moderation', label: 'Operations & Moderation', icon: ShieldCheck });
    }
    if (hasProviderAccess.value) {
        views.push({ id: 'provider', label: 'Provider Workspace', icon: Briefcase });
    }
    if (hasClientAccess.value || views.length === 0) {
        views.push({ id: 'client', label: 'Client Events', icon: User });
    }

    return views;
});

// Default active view to the first available module
const activeView = ref(availableViews.value[0]?.id || 'client');

watch(
    availableViews,
    (newViews) => {
        if (!newViews.some((v) => v.id === activeView.value) && newViews.length > 0) {
            activeView.value = newViews[0].id;
        }
    },
    { immediate: true }
);

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

    <div class="flex h-full flex-1 flex-col gap-6 overflow-x-auto rounded-xl p-4 md:p-6" v-if="user">
        <!-- Clean, Minimal Header with Name & Role Badge -->
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
                <p class="mt-0.5 text-xs text-muted-foreground">
                    Event Management Workspace
                </p>
            </div>

            <!-- Multi-Permission View Selector (If multiple workspaces unlocked) -->
            <div v-if="availableViews.length > 1" class="flex flex-wrap items-center gap-1.5">
                <button
                    v-for="v in availableViews"
                    :key="v.id"
                    @click="activeView = v.id"
                    class="inline-flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-xs font-medium transition-all"
                    :class="
                        activeView === v.id
                            ? 'bg-primary text-primary-foreground shadow-sm'
                            : 'border border-border/70 bg-background text-muted-foreground hover:bg-muted hover:text-foreground'
                    "
                >
                    <component :is="v.icon" class="size-3.5" />
                    {{ v.label }}
                </button>
            </div>
        </div>

        <!-- Render Dynamic Permission-Driven Dashboard Module -->
        <div class="transition-opacity duration-200">
            <SuperAdminDashboard v-if="activeView === 'governance'" />
            <AdminDashboard v-else-if="activeView === 'moderation'" />
            <ServiceProviderDashboard v-else-if="activeView === 'provider'" />
            <ClientDashboard v-else />
        </div>
    </div>
</template>
