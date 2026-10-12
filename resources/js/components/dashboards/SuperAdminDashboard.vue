<script setup lang="ts">
import {
    Users,
    Shield,
    Server,
    DollarSign,
    Database,
    Lock,
    ArrowUpRight,
    Activity,
    CheckCircle2,
    Sliders,
    Layers,
    FileText,
} from '@lucide/vue';

const metrics = [
    { title: 'Total Registered Users', value: '1,428', change: '+12.4%', icon: Users, isPositive: true },
    { title: 'System Uptime & Health', value: '99.98%', change: 'Optimal', icon: Server, isPositive: true },
    { title: 'Total Platform Volume', value: '$84,920', change: '+18.2%', icon: DollarSign, isPositive: true },
    { title: 'Active System Roles', value: '4 Defined', change: '24 Perms', icon: Shield, isPositive: true },
];

const auditLogs = [
    { action: 'Role Permissions Updated', target: 'Organizer Role', user: 'superadmin@heyevents.com', time: '12 mins ago', status: 'Success' },
    { action: 'Database Backup Completed', target: 'Automated Snapshot #492', user: 'System Worker', time: '1 hour ago', status: 'Success' },
    { action: 'Admin Privileges Granted', target: 'admin@heyevents.com', user: 'superadmin@heyevents.com', time: '3 hours ago', status: 'Security' },
    { action: 'API Gateway Rate Limit Adjusted', target: 'Production Cluster', user: 'superadmin@heyevents.com', time: 'Yesterday', status: 'Config' },
];

const systemServices = [
    { name: 'Spatie RBAC Engine', status: 'Active & Cached', version: 'v8.3', state: 'Operational' },
    { name: 'Database Cluster (Postgres/SQLite)', status: 'Connected', latency: '24ms', state: 'Operational' },
    { name: 'Event Queue & Notifications', status: 'Processing', queueSize: '0 pending', state: 'Operational' },
    { name: 'Payment Webhook Dispatcher', status: 'Listening', health: '100%', state: 'Operational' },
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
                    <span class="inline-flex items-center text-xs font-medium text-emerald-600 dark:text-emerald-400">
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
                        <p class="text-xs text-muted-foreground">High-privilege system controls exclusive to Super Admins.</p>
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
                                <p class="text-xs text-muted-foreground">Define roles, adjust granular Spatie permission gates.</p>
                            </div>
                        </div>
                        <div class="mt-4 flex items-center justify-between text-xs">
                            <span class="text-muted-foreground">4 Active Roles</span>
                            <span class="inline-flex items-center font-medium text-primary hover:underline cursor-pointer">
                                Manage Matrix <ArrowUpRight class="ml-1 size-3" />
                            </span>
                        </div>
                    </div>

                    <div class="rounded-lg border border-border/60 bg-muted/30 p-4 transition-colors hover:bg-muted/50">
                        <div class="flex items-center gap-3">
                            <div class="rounded-md bg-indigo-600 p-2 text-white">
                                <Sliders class="size-4" />
                            </div>
                            <div>
                                <h3 class="text-sm font-semibold text-foreground">Platform Commission & Fees</h3>
                                <p class="text-xs text-muted-foreground">Configure global payout margins and transaction tariffs.</p>
                            </div>
                        </div>
                        <div class="mt-4 flex items-center justify-between text-xs">
                            <span class="text-muted-foreground">Standard 10% Fee</span>
                            <span class="inline-flex items-center font-medium text-primary hover:underline cursor-pointer">
                                Edit Settings <ArrowUpRight class="ml-1 size-3" />
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
                                <p class="text-xs text-muted-foreground">Trigger manual snapshots and review data integrity.</p>
                            </div>
                        </div>
                        <div class="mt-4 flex items-center justify-between text-xs">
                            <span class="text-emerald-600 dark:text-emerald-400">Snapshot healthy</span>
                            <span class="inline-flex items-center font-medium text-primary hover:underline cursor-pointer">
                                Backup Vault <ArrowUpRight class="ml-1 size-3" />
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
                                <p class="text-xs text-muted-foreground">Assign operational managers and revoke access.</p>
                            </div>
                        </div>
                        <div class="mt-4 flex items-center justify-between text-xs">
                            <span class="text-muted-foreground">2 Active Admins</span>
                            <span class="inline-flex items-center font-medium text-primary hover:underline cursor-pointer">
                                Staff Directory <ArrowUpRight class="ml-1 size-3" />
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
            <div class="rounded-xl border border-border/70 bg-card p-6 shadow-sm">
                <div class="flex items-center justify-between border-b border-border/50 pb-4">
                    <div>
                        <h2 class="text-lg font-semibold text-foreground">Security Audit Log</h2>
                        <p class="text-xs text-muted-foreground">Real-time system events</p>
                    </div>
                    <Activity class="size-4 text-purple-600 dark:text-purple-400" />
                </div>

                <div class="mt-4 divide-y divide-border/40">
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

                <button class="mt-5 w-full rounded-lg border border-border bg-background py-2 text-center text-xs font-medium text-foreground transition-colors hover:bg-muted">
                    View Complete Audit Trail
                </button>
            </div>
        </div>
    </div>
</template>
