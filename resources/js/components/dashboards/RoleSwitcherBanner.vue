<script setup lang="ts">
import { ShieldCheck, ShieldAlert, Briefcase, User, Key } from '@lucide/vue';
import { computed } from 'vue';
import type { User as UserType } from '@/types/auth';

const props = withDefaults(
    defineProps<{
        user: UserType;
        activeRole?: string;
        permissionCount?: number;
        isSuperAdmin?: boolean;
    }>(),
    {
        activeRole: 'Member',
        permissionCount: 0,
        isSuperAdmin: false,
    }
);

const roleMeta = computed(() => {
    if (props.isSuperAdmin || props.activeRole === 'Super Admin') {
        return {
            title: 'Platform Governance Portal',
            description: 'Full infrastructure control, database security, permission matrix & root access.',
            badgeColor: 'bg-purple-500/10 text-purple-700 dark:text-purple-300 border-purple-500/30',
            icon: ShieldAlert,
            accentBg: 'from-purple-600/20 via-indigo-600/10 to-transparent',
        };
    }

    if (props.activeRole === 'Admin') {
        return {
            title: 'Operations & Moderation Hub',
            description: 'Manage service provider KYC, review event listings, resolve disputes & track platform GMV.',
            badgeColor: 'bg-blue-500/10 text-blue-700 dark:text-blue-300 border-blue-500/30',
            icon: ShieldCheck,
            accentBg: 'from-blue-600/20 via-cyan-600/10 to-transparent',
        };
    }

    if (props.activeRole === 'Service Provider') {
        return {
            title: 'Service Provider Workspace',
            description: 'Manage services, gig schedule, customer bookings & payout withdrawals.',
            badgeColor: 'bg-emerald-500/10 text-emerald-700 dark:text-emerald-300 border-emerald-500/30',
            icon: Briefcase,
            accentBg: 'from-emerald-600/20 via-teal-600/10 to-transparent',
        };
    }

    return {
        title: 'Event Management Dashboard',
        description: 'Discover verified providers, schedule events, manage bookings & review invoices.',
        badgeColor: 'bg-amber-500/10 text-amber-700 dark:text-amber-300 border-amber-500/30',
        icon: User,
        accentBg: 'from-amber-600/20 via-orange-600/10 to-transparent',
    };
});
</script>

<template>
    <div
        class="relative overflow-hidden rounded-2xl border border-border/80 bg-gradient-to-r p-6 shadow-sm dark:border-sidebar-border"
        :class="roleMeta.accentBg"
    >
        <div class="relative z-10 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="space-y-1">
                <div class="flex flex-wrap items-center gap-2">
                    <span
                        class="inline-flex items-center gap-1.5 rounded-full border px-3 py-0.5 text-xs font-semibold uppercase tracking-wider"
                        :class="roleMeta.badgeColor"
                    >
                        <component :is="roleMeta.icon" class="size-3.5" />
                        {{ activeRole || 'Custom Role' }}
                    </span>
                    <span class="text-xs text-muted-foreground">
                        Logged in as <strong class="text-foreground">{{ user.name }}</strong>
                    </span>
                </div>
                <h1 class="text-2xl font-bold tracking-tight text-foreground sm:text-3xl">
                    {{ roleMeta.title }}
                </h1>
                <p class="max-w-2xl text-sm text-muted-foreground">
                    {{ roleMeta.description }}
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2 text-xs">
                <div class="rounded-lg border border-border/60 bg-background/80 px-3 py-2 backdrop-blur-sm">
                    <div class="flex items-center gap-1.5 font-medium text-foreground">
                        <Key class="size-3.5 text-primary" />
                        Permission-Driven
                    </div>
                    <p class="text-[11px] text-muted-foreground">
                        <span v-if="isSuperAdmin">All Permissions (Bypass)</span>
                        <span v-else>{{ permissionCount }} Permissions Assigned</span>
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>
