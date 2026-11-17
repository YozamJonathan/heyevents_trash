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
    PackageCheck,
    Truck,
    ShieldCheck,
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

    // 1. Admin Moderation & Operations (Super Admin & Staff)
    if (isSuperAdmin.value || can('verify service providers')) {
        // A. Vendor KYC Registration Approvals
        items.push({
            title: t('nav.vendorApprovals') || 'Vendor KYC Approvals',
            href: '/admin/vendor-approvals',
            icon: ShieldCheck,
        });

        // B. Equipment & Services Upload Approvals
        items.push({
            title: t('nav.itemApprovals') || 'Equipment Upload Approvals',
            href: '/admin/item-approvals',
            icon: PackageCheck,
        });

        // C. Client Event Bookings & Fulfillment Desk
        items.push({
            title: t('nav.clientBookings') || 'Client Bookings & Orders',
            href: '/admin/bookings',
            icon: Truck,
        });

        // D. User Management
        if (can('view users') || isSuperAdmin.value) {
            items.push({
                title: t('nav.userManagement'),
                href: '/admin/users',
                icon: Users,
            });
        }
    }

    // 2. Service Provider Workspace (Posting tools & gear)
    if (isServiceProvider.value && !isClientMode.value) {
        items.push({
            title: 'My Equipment & Tools',
            href: '/provider/services',
            icon: Briefcase,
        });

        items.push({
            title: t('nav.findServices'),
            href: '/explore',
            icon: Search,
        });
    }

    // 3. Client Navigation (Exploring & Booking)
    if (!isSuperAdmin.value && (!isServiceProvider.value || isClientMode.value)) {
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
