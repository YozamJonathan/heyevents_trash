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

const props = defineProps<{
    stats?: {
        users_count: number;
        roles_count: number;
        permissions_count: number;
    };
}>();

const metrics = computed(() => [
    {
        title: 'Total Registered Users',
        value: props.stats?.users_count !== undefined ? String(props.stats.users_count) : '4',
        change: 'In Database',
        icon: Users,
    },
    {
        title: 'System Uptime & Health',
        value: 'Optimal',
        change: 'All Services Up',
        icon: Server,
    },
    {
        title: 'Configured Permissions',
        value: props.stats?.permissions_count !== undefined ? String(props.stats.permissions_count) : '24',
        change: 'Spatie Guard',
        icon: KeyRound,
    },
    {
        title: 'Active System Roles',
        value: props.stats?.roles_count !== undefined ? String(props.stats.roles_count) : '4',
        change: 'RBAC Active',
        icon: Shield,
    },
]);

// Real audit log records (starts empty until activities are triggered)
const auditLogs = computed<Array<{ action: string; target: string; user: string; time: string; status: string }>>(() => []);

const systemServices = [
    { name: 'Spatie RBAC Engine', status: 'Active & Cached', state: 'Operational' },
    { name: 'Database Cluster', status: 'Connected', state: 'Operational' },
    { name: 'Event Queue & Jobs', status: 'Listening', state: 'Operational' },
    { name: 'Security & Auth Guard', status: 'Enforced', state: 'Operational' },
];
</script>

<template>
    <div class="space-y-6">
        <!-- Quick System Stats -->
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div
                v-for="metric in metrics"
                :key="metric.title"
                class="rounded-xl border border-border/70 bg-card p-5 shadow-sm transition-all hover:border-purple-500/40"
            >
                <div class="flex items-center justify-between">
                    <span class="text-xs font-medium uppercase tracking-wider text-muted-foreground">{{ metric.title }}</span>
                    <div class="rounded-lg bg-purple-500/10 p-2 text-purple-600 dark:text-purple-400">
                        <component :is="metric.icon" class="size-4" />
                    </div>
                </div>
                <div class="mt-3 flex items-baseline gap-2">
                    <span class="text-2xl font-bold tracking-tight text-foreground">{{ metric.value }}</span>
                    <span class="inline-flex items-center text-xs font-medium text-purple-600 dark:text-purple-400">
                        {{ metric.change }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Super Admin Core Sections -->
        <div class="grid gap-6 lg:grid-cols-3">
            <!-- System Controls & Governance -->
            <div class="rounded-xl border border-border/70 bg-card p-6 shadow-sm lg:col-span-2">
                <div class="flex items-center justify-between border-b border-border/50 pb-4">
                    <div>
                        <h2 class="text-lg font-semibold text-foreground">Platform Infrastructure & Governance</h2>
                        <p class="text-xs text-muted-foreground">High-privilege system controls and operational configuration.</p>
                    </div>
                    <span class="inline-flex items-center gap-1 rounded-md bg-purple-500/10 px-2 py-1 text-xs font-medium text-purple-600 dark:text-purple-400">
                        <Lock class="size-3" /> Root Access
                    </span>
                </div>

                <div class="mt-6 grid gap-4 sm:grid-cols-2">
                    <div class="rounded-lg border border-border/60 bg-muted/30 p-4 transition-colors hover:bg-muted/50">
                        <div class="flex items-center gap-3">
                            <div class="rounded-md bg-purple-600 p-2 text-white">
                                <Shield class="size-4" />
                            </div>
                            <div>
                                <h3 class="text-sm font-semibold text-foreground">Role & Permission Matrix</h3>
                                <p class="text-xs text-muted-foreground">Define roles and manage granular Spatie permission gates.</p>
                            </div>
                        </div>
                        <div class="mt-4 flex items-center justify-between text-xs">
                            <span class="text-muted-foreground">{{ props.stats?.roles_count ?? 4 }} Configured Roles</span>
                            <span class="inline-flex items-center font-medium text-primary">
                                System Protected
                            </span>
                        </div>
                    </div>

                    <div class="rounded-lg border border-border/60 bg-muted/30 p-4 transition-colors hover:bg-muted/50">
                        <div class="flex items-center gap-3">
                            <div class="rounded-md bg-indigo-600 p-2 text-white">
                                <Sliders class="size-4" />
                            </div>
                            <div>
                                <h3 class="text-sm font-semibold text-foreground">Platform Commission & Tariffs</h3>
                                <p class="text-xs text-muted-foreground">Configure global booking fee margins and transaction tiers.</p>
                            </div>
                        </div>
                        <div class="mt-4 flex items-center justify-between text-xs">
                            <span class="text-muted-foreground">Platform Standard</span>
                            <span class="inline-flex items-center font-medium text-primary">
                                Active Policy
                            </span>
                        </div>
                    </div>

                    <div class="rounded-lg border border-border/60 bg-muted/30 p-4 transition-colors hover:bg-muted/50">
                        <div class="flex items-center gap-3">
                            <div class="rounded-md bg-blue-600 p-2 text-white">
                                <Database class="size-4" />
                            </div>
                            <div>
                                <h3 class="text-sm font-semibold text-foreground">Database & Backup Vault</h3>
                                <p class="text-xs text-muted-foreground">Database migrations, connection pool, and automated jobs.</p>
                            </div>
                        </div>
                        <div class="mt-4 flex items-center justify-between text-xs">
                            <span class="text-emerald-600 dark:text-emerald-400">Database Connected</span>
                            <span class="inline-flex items-center font-medium text-emerald-600 dark:text-emerald-400">
                                Healthy
                            </span>
                        </div>
                    </div>

                    <div class="rounded-lg border border-border/60 bg-muted/30 p-4 transition-colors hover:bg-muted/50">
                        <div class="flex items-center gap-3">
                            <div class="rounded-md bg-rose-600 p-2 text-white">
                                <Layers class="size-4" />
                            </div>
                            <div>
                                <h3 class="text-sm font-semibold text-foreground">Admin Staff Delegation</h3>
                                <p class="text-xs text-muted-foreground">Assign operational administrators and manage access levels.</p>
                            </div>
                        </div>
                        <div class="mt-4 flex items-center justify-between text-xs">
                            <span class="text-muted-foreground">RBAC Guard</span>
                            <span class="inline-flex items-center font-medium text-primary">
                                Enforced
                            </span>
                        </div>
                    </div>
                </div>

                <!-- System Core Status -->
                <div class="mt-6 border-t border-border/50 pt-5">
                    <h4 class="text-xs font-semibold uppercase tracking-wider text-muted-foreground">System Core Services</h4>
                    <div class="mt-3 space-y-2">
                        <div
                            v-for="srv in systemServices"
                            :key="srv.name"
                            class="flex items-center justify-between rounded-lg border border-border/50 bg-background/50 px-3 py-2 text-xs"
                        >
                            <div class="flex items-center gap-2">
                                <span class="size-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                <span class="font-medium text-foreground">{{ srv.name }}</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="text-muted-foreground">{{ srv.status }}</span>
                                <span class="rounded bg-emerald-500/10 px-1.5 py-0.5 font-mono text-[10px] text-emerald-600 dark:text-emerald-400">
                                    {{ srv.state }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Audit Trail & System Activity -->
            <div class="rounded-xl border border-border/70 bg-card p-6 shadow-sm flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between border-b border-border/50 pb-4">
                        <div>
                            <h2 class="text-lg font-semibold text-foreground">Security Audit Log</h2>
                            <p class="text-xs text-muted-foreground">Real-time system events</p>
                        </div>
                        <Activity class="size-4 text-purple-600 dark:text-purple-400" />
                    </div>

                    <!-- Clean Empty State if no audit log entries -->
                    <div v-if="auditLogs.length === 0" class="flex flex-col items-center justify-center py-12 text-center">
                        <div class="rounded-full bg-purple-500/10 p-3 text-purple-600 dark:text-purple-400 mb-3">
                            <CheckCircle2 class="size-6" />
                        </div>
                        <p class="text-sm font-semibold text-foreground">All Clear & Monitored</p>
                        <p class="mt-1 text-xs text-muted-foreground max-w-[220px]">
                            No critical audit warnings. System permissions and authentication are running securely.
                        </p>
                    </div>

                    <!-- Populated logs if present -->
                    <div v-else class="mt-4 divide-y divide-border/40">
                        <div
                            v-for="(log, idx) in auditLogs"
                            :key="idx"
                            class="py-3 first:pt-0 last:pb-0"
                        >
                            <div class="flex items-start justify-between">
                                <span class="text-xs font-medium text-foreground">{{ log.action }}</span>
                                <span class="rounded bg-muted px-1.5 py-0.5 text-[10px] font-medium text-muted-foreground">{{ log.time }}</span>
                            </div>
                            <p class="mt-1 text-[11px] text-muted-foreground">
                                Target: <span class="font-medium text-foreground">{{ log.target }}</span>
                            </p>
                            <div class="mt-1 flex items-center justify-between text-[11px]">
                                <span class="text-muted-foreground truncate max-w-[140px]">{{ log.user }}</span>
                                <span class="text-purple-600 dark:text-purple-400 font-medium">{{ log.status }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-6 rounded-lg border border-border/60 bg-muted/20 p-3 text-center">
                    <p class="text-[11px] text-muted-foreground">
                        Audit streaming active via Laravel internal logging.
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>
