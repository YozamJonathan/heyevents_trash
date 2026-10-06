<script setup lang="ts">
import { Head, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import ClientDashboard from '@/components/dashboards/ClientDashboard.vue';
import RoleSwitcherBanner from '@/components/dashboards/RoleSwitcherBanner.vue';
import type { Auth } from '@/types/auth';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Client Dashboard',
                href: '/dashboard/client',
            },
        ],
    },
});

const page = usePage<{ auth: Auth }>();
const user = computed(() => page.props.auth.user);
</script>

<template>
    <Head title="Client Event Dashboard" />

    <div class="flex h-full flex-1 flex-col gap-6 overflow-x-auto rounded-xl p-4 md:p-6">
        <RoleSwitcherBanner :user="user" active-role="Client" />
        <ClientDashboard />
    </div>
</template>
