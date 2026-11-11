<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import {
    LayoutGrid,
    Shield,
    Users,
    Sliders,
    CheckSquare,
    Calendar,
    Briefcase,
    DollarSign,
    Search,
    Heart,
    BookOpen,
    TrendingUp,
    CalendarPlus,
    PartyPopper,
} from '@lucide/vue';
import { computed } from 'vue';
import AppLogo from '@/components/AppLogo.vue';
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { usePermission } from '@/composables/usePermission';
import { useLanguage } from '@/composables/useLanguage';
import { useWorkspaceMode } from '@/composables/useWorkspaceMode';
import { dashboard } from '@/routes';
import type { NavItem } from '@/types';

const { can, canAny, isSuperAdmin } = usePermission();
const { t } = useLanguage();
const { isServiceProvider, isClientMode } = useWorkspaceMode();

const mainNavItems = computed<NavItem[]>(() => {
    const items: NavItem[] = [
        {
            title: t('nav.dashboard'),
            href: dashboard(),
            icon: LayoutGrid,
        },
    ];

    // System Administration Permissions (Only shown if user has admin permissions)
    if (can('view users') || isSuperAdmin.value) {
        items.push({
            title: t('nav.userManagement'),
            href: '#',
            icon: Users,
        });
    }

    if (can('manage roles and permissions') || isSuperAdmin.value) {
        items.push({
            title: t('nav.rolesPermissions'),
            href: '#',
            icon: Shield,
        });
    }

    if (can('manage platform settings') || isSuperAdmin.value) {
        items.push({
            title: t('nav.platformConfig'),
            href: '#',
            icon: Sliders,
        });
    }

    // Operational & Moderation Permissions
    if (can('verify service providers') || isSuperAdmin.value) {
        items.push({
            title: t('nav.vendorApprovals'),
            href: '/admin/vendor-approvals',
            icon: CheckSquare,
        });
    }

    if (can('moderate events') || isSuperAdmin.value) {
        items.push({
            title: t('nav.eventModeration'),
            href: '#',
            icon: Calendar,
        });
    }

    if (can('resolve disputes') || isSuperAdmin.value) {
        items.push({
            title: t('nav.disputeDesk'),
            href: '#',
            icon: Shield,
        });
    }

    if (can('view platform analytics') || isSuperAdmin.value) {
        items.push({
            title: t('nav.businessAnalytics'),
            href: '#',
            icon: TrendingUp,
        });
    }

    // Service Provider Navigation: Shown for all Service Providers (vendors)
    if (isServiceProvider.value || canAny(['create services', 'edit services', 'manage service packages'])) {
        items.push({
            title: t('nav.myServices'),
            href: '#',
            icon: Briefcase,
        });

        items.push({
            title: t('nav.providerSchedule'),
            href: '#',
            icon: Calendar,
        });

        items.push({
            title: t('nav.earningsPayouts'),
            href: '#',
            icon: DollarSign,
        });
    }

    // Client / Discovery Navigation:
    // Shown for normal Clients OR Service Providers browsing in 'Client Mode'
    const showClientNav = !isServiceProvider.value || isClientMode.value;

    if (showClientNav) {
        items.push({
            title: t('nav.findServices'),
            href: '/explore',
            icon: Search,
        });

        items.push({
            title: t('nav.bookEvent'),
            href: '/events/book',
            icon: CalendarPlus,
        });

        items.push({
            title: t('nav.myBookedEvents'),
            href: '/events/my-bookings',
            icon: Calendar,
        });
    }

    return items;
});

const footerNavItems = computed<NavItem[]>(() => [
    {
        title: t('nav.platformHelp'),
        href: '#',
        icon: BookOpen,
    },
]);
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="dashboard()">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <!-- Provider Mode Switcher in Sidebar (Visible only to Service Providers) -->
            <div v-if="isServiceProvider" class="px-3 pt-2 pb-1">
                <div
                    class="flex items-center rounded-lg border border-sidebar-border bg-sidebar-accent/40 p-0.5 text-xs shadow-2xs"
                >
                    <button
                        type="button"
                        @click="useWorkspaceMode().setMode('provider')"
                        :class="[
                            'flex-1 flex items-center justify-center gap-1.5 rounded-md py-1.5 font-semibold transition-all text-xs',
                            !isClientMode
                                ? 'bg-background text-foreground shadow-xs'
                                : 'text-muted-foreground hover:text-foreground',
                        ]"
                    >
                        <Briefcase class="size-3.5" />
                        <span>{{ t('app.vendorWorkspace') }}</span>
                    </button>
                    <button
                        type="button"
                        @click="useWorkspaceMode().setMode('client')"
                        :class="[
                            'flex-1 flex items-center justify-center gap-1.5 rounded-md py-1.5 font-semibold transition-all text-xs',
                            isClientMode
                                ? 'bg-[#ff545a] text-white shadow-xs'
                                : 'text-muted-foreground hover:text-foreground',
                        ]"
                    >
                        <PartyPopper class="size-3.5" />
                        <span>{{ t('app.browseAsClient') }}</span>
                    </button>
                </div>
            </div>

            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
