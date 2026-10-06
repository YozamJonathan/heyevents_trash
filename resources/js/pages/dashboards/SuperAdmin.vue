<script setup lang="ts">
import { Head, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import RoleSwitcherBanner from '@/components/dashboards/RoleSwitcherBanner.vue';
import SuperAdminDashboard from '@/components/dashboards/SuperAdminDashboard.vue';
import type { Auth } from '@/types/auth';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Super Admin Dashboard',
                href: '/dashboard/super-admin',
            },
        ],
    },
});

const page = usePage<{ auth: Auth }>();
const user = computed(() => page.props.auth.user);
</script>

<template>
    <Head title="Super Admin Dashboard" />

    <div class="flex h-full flex-1 flex-col gap-6 overflow-x-auto rounded-xl p-4 md:p-6">
        <RoleSwitcherBanner :user="user" active-role="Super Admin" />
        <SuperAdminDashboard />
    </div>
</template>
