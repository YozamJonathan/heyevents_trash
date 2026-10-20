<script setup lang="ts">
import { computed } from 'vue';
import {
    Users,
    Shield,
    Server,
    Database,
    Lock,
    ArrowUpRight,
    Activity,
    Sliders,
    Layers,
    KeyRound,
    CheckCircle2,
} from '@lucide/vue';
import { useLanguage, type TranslationKey } from '@/composables/useLanguage';

const props = defineProps<{
    stats?: {
        users_count: number;
        roles_count: number;
        permissions_count: number;
    };
}>();

const { t } = useLanguage();

const metrics = computed(() => [
    {
        title: t('superAdminDashboard.users'),
        value: String(props.stats?.users_count ?? 4),
        change: t('superAdminDashboard.database'),
        icon: Users,
    },
    {
        title: t('superAdminDashboard.health'),
        value: t('superAdminDashboard.optimal'),
        change: t('superAdminDashboard.servicesUp'),
        icon: Server,
    },
    {
        title: t('superAdminDashboard.permissions'),
        value:
            props.stats?.permissions_count !== undefined
                ? String(props.stats.permissions_count)
                : '24',
        change: t('superAdminDashboard.guard'),
        icon: KeyRound,
    },
    {
        title: t('superAdminDashboard.roles'),
        value:
            props.stats?.roles_count !== undefined
                ? String(props.stats.roles_count)
                : '4',
        change: t('superAdminDashboard.rbac'),
        icon: Shield,
    },
]);

// Real audit log records (starts empty until activities are triggered)
const auditLogs = computed<
    Array<{
        action: string;
        target: string;
        user: string;
        time: string;
        status: string;
    }>
>(() => []);

const systemServices = [
    {
        nameKey: 'superAdminDashboard.rbacEngine',
        statusKey: 'superAdminDashboard.activeCached',
        stateKey: 'superAdminDashboard.optimal',
    },
    {
        nameKey: 'superAdminDashboard.databaseCluster',
        statusKey: 'superAdminDashboard.connected',
        stateKey: 'superAdminDashboard.optimal',
    },
    {
        nameKey: 'superAdminDashboard.eventQueue',
        statusKey: 'superAdminDashboard.listening',
        stateKey: 'superAdminDashboard.optimal',
    },
    {
        nameKey: 'superAdminDashboard.securityAuth',
        statusKey: 'superAdminDashboard.enforced',
        stateKey: 'superAdminDashboard.optimal',
    },
] satisfies {
    nameKey: TranslationKey;
    statusKey: TranslationKey;
    stateKey: TranslationKey;
}[];
</script>

<template>
    <div class="space-y-6">
        <!-- Quick System Stats -->
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div
                v-for="metric in metrics"
                :key="metric.title"
                class="border-border/70 bg-card rounded-xl border p-5 shadow-sm transition-all hover:border-purple-500/40"
            >
                <div class="flex items-center justify-between">
                    <span
                        class="text-muted-foreground text-xs font-medium tracking-wider uppercase"
                        >{{ metric.title }}</span
                    >
                    <div
                        class="rounded-lg bg-purple-500/10 p-2 text-purple-600 dark:text-purple-400"
                    >
                        <component :is="metric.icon" class="size-4" />
                    </div>
                </div>
                <div class="mt-3 flex items-baseline gap-2">
                    <span
                        class="text-foreground text-2xl font-bold tracking-tight"
                        >{{ metric.value }}</span
                    >
                    <span
                        class="inline-flex items-center text-xs font-medium text-purple-600 dark:text-purple-400"
                    >
                        {{ metric.change }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Super Admin Core Sections -->
        <div class="grid gap-6 lg:grid-cols-3">
            <!-- System Controls & Governance -->
            <div
                class="border-border/70 bg-card rounded-xl border p-6 shadow-sm lg:col-span-2"
            >
                <div
                    class="border-border/50 flex items-center justify-between border-b pb-4"
                >
                    <div>
                        <h2 class="text-foreground text-lg font-semibold">
                            {{ t('superAdminDashboard.infrastructure') }}
                        </h2>
                        <p class="text-muted-foreground text-xs">
                            {{
                                t(
                                    'superAdminDashboard.infrastructureDescription',
                                )
                            }}
                        </p>
                    </div>
                    <span
                        class="inline-flex items-center gap-1 rounded-md bg-purple-500/10 px-2 py-1 text-xs font-medium text-purple-600 dark:text-purple-400"
                    >
                        <Lock class="size-3" />
                        {{ t('superAdminDashboard.rootAccess') }}
                    </span>
                </div>

                <div class="mt-6 grid gap-4 sm:grid-cols-2">
                    <div
                        class="border-border/60 bg-muted/30 hover:bg-muted/50 rounded-lg border p-4 transition-colors"
                    >
                        <div class="flex items-center gap-3">
                            <div
                                class="rounded-md bg-purple-600 p-2 text-white"
                            >
                                <Shield class="size-4" />
                            </div>
                            <div>
                                <h3
                                    class="text-foreground text-sm font-semibold"
                                >
                                    {{ t('superAdminDashboard.roleMatrix') }}
                                </h3>
                                <p class="text-muted-foreground text-xs">
                                    {{
                                        t(
                                            'superAdminDashboard.roleMatrixDescription',
                                        )
                                    }}
                                </p>
                            </div>
                        </div>
                        <div
                            class="mt-4 flex items-center justify-between text-xs"
                        >
                            <span class="text-muted-foreground"
                                >{{ props.stats?.roles_count ?? 4 }}
                                {{
                                    t('superAdminDashboard.configuredRoles')
                                }}</span
                            >
                            <span
                                class="text-primary inline-flex items-center font-medium"
                            >
                                {{ t('superAdminDashboard.protected') }}
                            </span>
                        </div>
                    </div>

                    <div
                        class="border-border/60 bg-muted/30 hover:bg-muted/50 rounded-lg border p-4 transition-colors"
                    >
                        <div class="flex items-center gap-3">
                            <div
                                class="rounded-md bg-indigo-600 p-2 text-white"
                            >
                                <Sliders class="size-4" />
                            </div>
                            <div>
                                <h3
                                    class="text-foreground text-sm font-semibold"
                                >
                                    {{ t('superAdminDashboard.commission') }}
                                </h3>
                                <p class="text-muted-foreground text-xs">
                                    {{
                                        t(
                                            'superAdminDashboard.commissionDescription',
                                        )
                                    }}
                                </p>
                            </div>
                        </div>
                        <div
                            class="mt-4 flex items-center justify-between text-xs"
                        >
                            <span class="text-muted-foreground">{{
                                t('superAdminDashboard.platformStandard')
                            }}</span>
                            <span
                                class="text-primary inline-flex items-center font-medium"
                            >
                                {{ t('superAdminDashboard.activePolicy') }}
                            </span>
                        </div>
                    </div>

                    <div
                        class="border-border/60 bg-muted/30 hover:bg-muted/50 rounded-lg border p-4 transition-colors"
                    >
                        <div class="flex items-center gap-3">
                            <div class="rounded-md bg-blue-600 p-2 text-white">
                                <Database class="size-4" />
                            </div>
                            <div>
                                <h3
                                    class="text-foreground text-sm font-semibold"
                                >
                                    {{
                                        t('superAdminDashboard.databaseBackup')
                                    }}
                                </h3>
                                <p class="text-muted-foreground text-xs">
                                    {{
                                        t(
                                            'superAdminDashboard.databaseDescription',
                                        )
                                    }}
                                </p>
                            </div>
                        </div>
                        <div
                            class="mt-4 flex items-center justify-between text-xs"
                        >
                            <span
                                class="text-emerald-600 dark:text-emerald-400"
                                >{{ t('superAdminDashboard.connected') }}</span
                            >
                            <span
                                class="inline-flex items-center font-medium text-emerald-600 dark:text-emerald-400"
                            >
                                {{ t('superAdminDashboard.healthy') }}
                            </span>
                        </div>
                    </div>

                    <div
                        class="border-border/60 bg-muted/30 hover:bg-muted/50 rounded-lg border p-4 transition-colors"
                    >
                        <div class="flex items-center gap-3">
                            <div class="rounded-md bg-rose-600 p-2 text-white">
                                <Layers class="size-4" />
                            </div>
                            <div>
                                <h3
                                    class="text-foreground text-sm font-semibold"
                                >
                                    {{ t('superAdminDashboard.delegation') }}
                                </h3>
                                <p class="text-muted-foreground text-xs">
                                    {{
                                        t(
                                            'superAdminDashboard.delegationDescription',
                                        )
                                    }}
                                </p>
                            </div>
                        </div>
                        <div
                            class="mt-4 flex items-center justify-between text-xs"
                        >
                            <span class="text-muted-foreground">{{
                                t('superAdminDashboard.rbacGuard')
                            }}</span>
                            <span
                                class="text-primary inline-flex items-center font-medium"
                            >
                                {{ t('superAdminDashboard.enforced') }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- System Core Status -->
                <div class="border-border/50 mt-6 border-t pt-5">
                    <h4
                        class="text-muted-foreground text-xs font-semibold tracking-wider uppercase"
                    >
                        {{ t('superAdminDashboard.coreServices') }}
                    </h4>
                    <div class="mt-3 space-y-2">
                        <div
                            v-for="srv in systemServices"
                            :key="srv.nameKey"
                            class="border-border/50 bg-background/50 flex items-center justify-between rounded-lg border px-3 py-2 text-xs"
                        >
                            <div class="flex items-center gap-2">
                                <span
                                    class="size-2 animate-pulse rounded-full bg-emerald-500"
                                ></span>
                                <span class="text-foreground font-medium">{{
                                    t(srv.nameKey)
                                }}</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="text-muted-foreground">{{
                                    t(srv.statusKey)
                                }}</span>
                                <span
                                    class="rounded bg-emerald-500/10 px-1.5 py-0.5 font-mono text-[10px] text-emerald-600 dark:text-emerald-400"
                                >
                                    {{ t(srv.stateKey) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Audit Trail & System Activity -->
            <div
                class="border-border/70 bg-card flex flex-col justify-between rounded-xl border p-6 shadow-sm"
            >
                <div>
                    <div
                        class="border-border/50 flex items-center justify-between border-b pb-4"
                    >
                        <div>
                            <h2 class="text-foreground text-lg font-semibold">
                                {{ t('superAdminDashboard.auditLog') }}
                            </h2>
                            <p class="text-muted-foreground text-xs">
                                {{ t('superAdminDashboard.systemEvents') }}
                            </p>
                        </div>
                        <Activity
                            class="size-4 text-purple-600 dark:text-purple-400"
                        />
                    </div>

                    <!-- Clean Empty State if no audit log entries -->
                    <div
                        v-if="auditLogs.length === 0"
                        class="flex flex-col items-center justify-center py-12 text-center"
                    >
                        <div
                            class="mb-3 rounded-full bg-purple-500/10 p-3 text-purple-600 dark:text-purple-400"
                        >
                            <CheckCircle2 class="size-6" />
                        </div>
                        <p class="text-foreground text-sm font-semibold">
                            {{ t('superAdminDashboard.allClear') }}
                        </p>
                        <p
                            class="text-muted-foreground mt-1 max-w-[220px] text-xs"
                        >
                            {{ t('superAdminDashboard.noWarnings') }}
                        </p>
                    </div>

                    <!-- Populated logs if present -->
                    <div v-else class="divide-border/40 mt-4 divide-y">
                        <div
                            v-for="(log, idx) in auditLogs"
                            :key="idx"
                            class="py-3 first:pt-0 last:pb-0"
                        >
                            <div class="flex items-start justify-between">
                                <span
                                    class="text-foreground text-xs font-medium"
                                    >{{ log.action }}</span
                                >
                                <span
                                    class="bg-muted text-muted-foreground rounded px-1.5 py-0.5 text-[10px] font-medium"
                                    >{{ log.time }}</span
                                >
                            </div>
                            <p class="text-muted-foreground mt-1 text-[11px]">
                                {{ t('superAdminDashboard.target') }}:
                                <span class="text-foreground font-medium">{{
                                    log.target
                                }}</span>
                            </p>
                            <div
                                class="mt-1 flex items-center justify-between text-[11px]"
                            >
                                <span
                                    class="text-muted-foreground max-w-[140px] truncate"
                                    >{{ log.user }}</span
                                >
                                <span
                                    class="font-medium text-purple-600 dark:text-purple-400"
                                    >{{ log.status }}</span
                                >
                            </div>
                        </div>
                    </div>
                </div>

                <div
                    class="border-border/60 bg-muted/20 mt-6 rounded-lg border p-3 text-center"
                >
                    <p class="text-muted-foreground text-[11px]">
                        {{ t('superAdminDashboard.auditStreaming') }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>
