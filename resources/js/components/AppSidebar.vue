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
import { dashboard } from '@/routes';
import type { NavItem } from '@/types';

const { can, canAny } = usePermission();

const mainNavItems = computed<NavItem[]>(() => {
    const items: NavItem[] = [
        {
            title: 'Dashboard',
            href: dashboard(),
            icon: LayoutGrid,
        },
    ];

    // System Administration Permissions
    if (can('view users')) {
        items.push({
            title: 'User Management',
            href: '#',
            icon: Users,
        });
    }

    if (can('manage roles and permissions')) {
        items.push({
            title: 'Roles & Permissions',
            href: '#',
            icon: Shield,
        });
    }

    if (can('manage platform settings')) {
        items.push({
            title: 'Platform Config',
            href: '#',
            icon: Sliders,
        });
    }

    // Operational & Moderation Permissions
    if (can('verify service providers')) {
        items.push({
            title: 'Vendor Approvals (KYC)',
            href: '#',
            icon: CheckSquare,
        });
    }

    if (can('moderate events')) {
        items.push({
            title: 'Event Moderation',
            href: '#',
            icon: Calendar,
        });
    }

    if (can('resolve disputes')) {
        items.push({
            title: 'Dispute Desk',
            href: '#',
            icon: Shield,
        });
    }

    if (can('view platform analytics')) {
        items.push({
            title: 'Business Analytics',
            href: '#',
            icon: TrendingUp,
        });
    }

    // Service Provider Permissions
    if (canAny(['create services', 'edit services', 'manage service packages'])) {
        items.push({
            title: 'My Services & Packages',
            href: '#',
            icon: Briefcase,
        });
    }

    if (can('view provider bookings')) {
        items.push({
            title: 'Provider Schedule',
            href: '#',
            icon: Calendar,
        });
    }

    if (can('view provider earnings')) {
        items.push({
            title: 'Earnings & Payouts',
            href: '#',
            icon: DollarSign,
        });
    }

    // Client / Discovery Permissions
    if (can('browse services')) {
        items.push({
            title: 'Find Services',
            href: '#',
            icon: Search,
        });
    }

    if (can('view my bookings')) {
        items.push({
            title: 'My Booked Events',
            href: '#',
            icon: Calendar,
        });
    }

    return items;
});

const footerNavItems: NavItem[] = [
    {
        title: 'Platform Help',
        href: '#',
        icon: BookOpen,
    },
];
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
