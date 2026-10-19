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
import { dashboard } from '@/routes';
import type { NavItem } from '@/types';

const { can, canAny } = usePermission();
const { t } = useLanguage();

const mainNavItems = computed<NavItem[]>(() => {
    const items: NavItem[] = [
        {
            title: t('nav.dashboard'),
            href: dashboard(),
            icon: LayoutGrid,
        },
    ];

    // System Administration Permissions
    if (can('view users')) {
        items.push({
            title: t('nav.userManagement'),
            href: '#',
            icon: Users,
        });
    }

    if (can('manage roles and permissions')) {
        items.push({
            title: t('nav.rolesPermissions'),
            href: '#',
            icon: Shield,
        });
    }

    if (can('manage platform settings')) {
        items.push({
            title: t('nav.platformConfig'),
            href: '#',
            icon: Sliders,
        });
    }

    // Operational & Moderation Permissions
    if (can('verify service providers')) {
        items.push({
            title: t('nav.vendorApprovals'),
            href: '#',
            icon: CheckSquare,
        });
    }

    if (can('moderate events')) {
        items.push({
            title: t('nav.eventModeration'),
            href: '#',
            icon: Calendar,
        });
    }

    if (can('resolve disputes')) {
        items.push({
            title: t('nav.disputeDesk'),
            href: '#',
            icon: Shield,
        });
    }

    if (can('view platform analytics')) {
        items.push({
            title: t('nav.businessAnalytics'),
            href: '#',
            icon: TrendingUp,
        });
    }

    // Service Provider Permissions
    if (
        canAny(['create services', 'edit services', 'manage service packages'])
    ) {
        items.push({
            title: t('nav.myServices'),
            href: '#',
            icon: Briefcase,
        });
    }

    if (can('view provider bookings')) {
        items.push({
            title: t('nav.providerSchedule'),
            href: '#',
            icon: Calendar,
        });
    }

    if (can('view provider earnings')) {
        items.push({
            title: t('nav.earningsPayouts'),
            href: '#',
            icon: DollarSign,
        });
    }

    // Client / Discovery Permissions
    if (can('browse services')) {
        items.push({
            title: t('nav.findServices'),
            href: '#',
            icon: Search,
        });
    }

    if (can('view my bookings')) {
        items.push({
            title: t('nav.myBookedEvents'),
            href: '#',
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
