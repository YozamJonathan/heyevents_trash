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
            href: '/admin/users',
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

    // Navigation based on active workspace mode:
    // When in Vendor Workspace: show Provider operational links (Services, Schedule, Earnings)
    // When in Client Mode (or normal client): show Client exploration links (Find Services, Book Event, My Bookings)
    if (isServiceProvider.value && !isClientMode.value) {
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
    } else {
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
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
