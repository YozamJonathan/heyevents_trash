<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';
import { dashboard } from '@/routes';
import {
    Users,
    Shield,
    Briefcase,
    Ticket,
    ShieldAlert,
    Search,
    UserCheck,
    Calendar,
    MoreVertical,
    Trash2,
    Crown,
    Filter,
    CheckCircle2,
    AlertCircle,
    Clock,
    X,
    ChevronLeft,
    ChevronRight,
} from '@lucide/vue';
import { useLanguage } from '@/composables/useLanguage';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Dashboard',
                href: dashboard(),
            },
            {
                title: 'User Management',
                href: '/admin/users',
            },
        ],
    },
});

interface UserItem {
    id: number;
    name: string;
    email: string;
    google_id?: string | null;
    email_verified_at?: string | null;
    created_at: string;
    roles: Array<{ id: number; name: string }>;
    service_provider_profile?: {
        id: number;
        brand_name: string;
        status: 'pending' | 'approved' | 'rejected';
    } | null;
}

const props = defineProps<{
    users: {
        data: UserItem[];
        current_page: number;
        last_page: number;
        total: number;
        links: Array<{ url: string | null; label: string; active: boolean }>;
    };
    stats: {
        total: number;
        admins: number;
        providers: number;
        clients: number;
    };
    filters: {
        search: string;
        role: string;
    };
    availableRoles: string[];
    canEditUsers: boolean;
    canDeleteUsers: boolean;
}>();

const { t } = useLanguage();

const search = ref(props.filters.search || '');
const selectedRole = ref(props.filters.role || 'all');

// Role Change Modal state
const roleModalOpen = ref(false);
const selectedUser = ref<UserItem | null>(null);
const newRoleToAssign = ref<string>('');
const isSubmittingRole = ref(false);

function openRoleModal(user: UserItem) {
    selectedUser.value = user;
    newRoleToAssign.value = user.roles[0]?.name || 'Client';
    roleModalOpen.value = true;
}

function closeRoleModal() {
    roleModalOpen.value = false;
    selectedUser.value = null;
    newRoleToAssign.value = '';
}

function submitRoleChange() {
    if (!selectedUser.value || !newRoleToAssign.value) return;

    isSubmittingRole.value = true;
    router.put(
        `/admin/users/${selectedUser.value.id}/role`,
        { role: newRoleToAssign.value },
        {
            preserveScroll: true,
            onFinish: () => {
                isSubmittingRole.value = false;
                closeRoleModal();
            },
        },
    );
}

function deleteUser(user: UserItem) {
    if (
        !confirm(
            `Are you sure you want to permanently delete user "${user.name}" (${user.email})? This action cannot be undone.`,
        )
    ) {
        return;
    }

    router.delete(`/admin/users/${user.id}`, {
        preserveScroll: true,
    });
}

// Debounced filter trigger
let timeout: any = null;
function applyFilter() {
    clearTimeout(timeout);
    timeout = setTimeout(() => {
        router.get(
            '/admin/users',
            {
                search: search.value || undefined,
                role: selectedRole.value !== 'all' ? selectedRole.value : undefined,
            },
            {
                preserveState: true,
                replace: true,
            },
        );
    }, 300);
}

watch(selectedRole, () => {
    applyFilter();
});

function getInitials(name: string): string {
    return name
        .split(' ')
        .map((n) => n[0])
        .slice(0, 2)
        .join('')
        .toUpperCase();
}

function formatDate(dateStr: string) {
    try {
        return new Date(dateStr).toLocaleDateString(undefined, {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
        });
    } catch {
        return dateStr;
    }
}
</script>

<template>
    <Head title="User Management" />

    <div class="space-y-6">
        <!-- Page Header -->
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-foreground font-['Poppins',sans-serif]">
                    User Management
                </h1>
                <p class="text-xs sm:text-sm text-muted-foreground mt-1">
                    Manage all registered accounts, roles, permission tiers, and service provider credentials.
                </p>
            </div>
            <Link
                href="/dashboard"
                class="inline-flex self-start items-center gap-1.5 rounded-xl border border-border/60 bg-background px-3.5 py-2 text-xs font-semibold text-foreground transition hover:bg-muted"
            >
                ← Back to Dashboard
            </Link>
        </div>

        <!-- Metrics Row -->
        <div class="grid gap-3 grid-cols-2 sm:grid-cols-4">
            <div class="rounded-xl border border-border/70 bg-card p-4 shadow-xs">
                <span class="text-xs font-medium text-muted-foreground">Total Users</span>
                <div class="flex items-baseline justify-between mt-1">
                    <span class="text-2xl font-bold text-foreground">{{ stats.total }}</span>
                    <Users class="size-4 text-muted-foreground/60" />
                </div>
            </div>

            <div class="rounded-xl border border-blue-500/30 bg-blue-500/5 p-4 shadow-xs">
                <span class="text-xs font-medium text-blue-700 dark:text-blue-300">Administrators</span>
                <div class="flex items-baseline justify-between mt-1">
                    <span class="text-2xl font-bold text-blue-900 dark:text-blue-100">{{ stats.admins }}</span>
                    <Shield class="size-4 text-blue-600 dark:text-blue-400" />
                </div>
            </div>

            <div class="rounded-xl border border-amber-500/30 bg-amber-500/5 p-4 shadow-xs">
                <span class="text-xs font-medium text-amber-700 dark:text-amber-300">Service Providers</span>
                <div class="flex items-baseline justify-between mt-1">
                    <span class="text-2xl font-bold text-amber-900 dark:text-amber-100">{{ stats.providers }}</span>
                    <Briefcase class="size-4 text-amber-600 dark:text-amber-400" />
                </div>
            </div>

            <div class="rounded-xl border border-emerald-500/30 bg-emerald-500/5 p-4 shadow-xs">
                <span class="text-xs font-medium text-emerald-700 dark:text-emerald-300">Event Clients</span>
                <div class="flex items-baseline justify-between mt-1">
                    <span class="text-2xl font-bold text-emerald-900 dark:text-emerald-100">{{ stats.clients }}</span>
                    <Ticket class="size-4 text-emerald-600 dark:text-emerald-400" />
                </div>
            </div>
        </div>

        <!-- Filters Bar -->
        <div class="flex flex-col sm:flex-row gap-3">
            <!-- Search Input -->
            <div class="relative flex-1">
                <Search class="absolute left-3.5 top-1/2 -translate-y-1/2 size-4 text-muted-foreground" />
                <input
                    v-model="search"
                    @input="applyFilter"
                    type="text"
                    placeholder="Search by name or email address..."
                    class="w-full h-10.5 pl-10 pr-4 rounded-xl border border-border/80 bg-card text-xs text-foreground placeholder:text-muted-foreground focus:border-[#ff545a] focus:ring-1 focus:ring-[#ff545a] outline-none transition"
                />
            </div>

            <!-- Role Filter Select -->
            <div class="flex items-center gap-2">
                <Filter class="size-4 text-muted-foreground shrink-0 hidden sm:block" />
                <select
                    v-model="selectedRole"
                    class="h-10.5 rounded-xl border border-border/80 bg-card px-3.5 text-xs font-medium text-foreground outline-none focus:border-[#ff545a] transition"
                >
                    <option value="all">All Roles</option>
                    <option v-for="role in availableRoles" :key="role" :value="role">
                        {{ role }}
                    </option>
                </select>
            </div>
        </div>

        <!-- Users Table Card -->
        <div class="rounded-2xl border border-border/80 bg-card shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-muted/50 border-b border-border/60 text-[11px] font-bold uppercase tracking-wider text-muted-foreground">
                        <tr>
                            <th class="py-3 px-4">User</th>
                            <th class="py-3 px-4">Role</th>
                            <th class="py-3 px-4">KYC / Verification</th>
                            <th class="py-3 px-4">Registered</th>
                            <th class="py-3 px-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border/40">
                        <tr
                            v-if="users.data.length === 0"
                            class="text-center py-8"
                        >
                            <td colspan="5" class="py-12 text-muted-foreground">
                                No users matched your search criteria.
                            </td>
                        </tr>

                        <tr
                            v-for="user in users.data"
                            :key="user.id"
                            class="hover:bg-muted/30 transition-colors"
                        >
                            <!-- Name & Email -->
                            <td class="py-3.5 px-4 min-w-[200px]">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="size-8.5 rounded-full bg-[#ff545a]/10 text-[#ff545a] flex items-center justify-center font-bold text-xs shrink-0"
                                    >
                                        {{ getInitials(user.name) }}
                                    </div>
                                    <div class="min-w-0">
                                        <div class="flex items-center gap-1.5 font-semibold text-foreground truncate">
                                            <span class="truncate">{{ user.name }}</span>
                                            <span
                                                v-if="user.google_id"
                                                title="Registered via Google"
                                                class="rounded bg-zinc-200 dark:bg-zinc-800 px-1 py-0.2 text-[9px] font-bold text-zinc-600 dark:text-zinc-400"
                                            >
                                                G
                                            </span>
                                        </div>
                                        <div class="text-muted-foreground text-[11px] truncate">
                                            {{ user.email }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- Role Badges -->
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <template v-if="user.roles.length > 0">
                                    <span
                                        v-for="r in user.roles"
                                        :key="r.id"
                                        class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-[11px] font-bold"
                                        :class="{
                                            'bg-purple-500/15 text-purple-700 dark:text-purple-300': r.name === 'Super Admin',
                                            'bg-blue-500/15 text-blue-700 dark:text-blue-300': r.name === 'Admin',
                                            'bg-amber-500/15 text-amber-700 dark:text-amber-300': r.name === 'Service Provider',
                                            'bg-emerald-500/15 text-emerald-700 dark:text-emerald-300': r.name === 'Client',
                                        }"
                                    >
                                        <Crown v-if="r.name === 'Super Admin'" class="size-3" />
                                        <Shield v-else-if="r.name === 'Admin'" class="size-3" />
                                        <Briefcase v-else-if="r.name === 'Service Provider'" class="size-3" />
                                        <Ticket v-else class="size-3" />
                                        {{ r.name }}
                                    </span>
                                </template>
                                <span
                                    v-else
                                    class="inline-flex items-center rounded-full bg-zinc-200/60 dark:bg-zinc-800 px-2 py-0.5 text-[11px] font-medium text-zinc-500"
                                >
                                    Unassigned
                                </span>
                            </td>

                            <!-- KYC Status -->
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <template v-if="user.service_provider_profile">
                                    <Link
                                        href="/admin/vendor-approvals"
                                        class="inline-flex items-center gap-1.5 hover:underline"
                                    >
                                        <span
                                            v-if="user.service_provider_profile.status === 'pending'"
                                            class="inline-flex items-center gap-1 text-amber-600 dark:text-amber-400 font-semibold text-[11px]"
                                        >
                                            <Clock class="size-3" />
                                            KYC Pending
                                        </span>
                                        <span
                                            v-else-if="user.service_provider_profile.status === 'approved'"
                                            class="inline-flex items-center gap-1 text-emerald-600 dark:text-emerald-400 font-semibold text-[11px]"
                                        >
                                            <CheckCircle2 class="size-3" />
                                            Verified
                                        </span>
                                        <span
                                            v-else
                                            class="inline-flex items-center gap-1 text-rose-600 dark:text-rose-400 font-semibold text-[11px]"
                                        >
                                            <AlertCircle class="size-3" />
                                            Rejected
                                        </span>
                                    </Link>
                                </template>
                                <span v-else class="text-muted-foreground text-[11px]">
                                    N/A
                                </span>
                            </td>

                            <!-- Joined Date -->
                            <td class="py-3.5 px-4 whitespace-nowrap text-muted-foreground">
                                {{ formatDate(user.created_at) }}
                            </td>

                            <!-- Action Buttons -->
                            <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                <div class="inline-flex items-center gap-2">
                                    <button
                                        v-if="canEditUsers"
                                        type="button"
                                        @click="openRoleModal(user)"
                                        class="rounded-lg border border-border/70 bg-background px-2.5 py-1 text-[11px] font-semibold text-foreground hover:bg-muted transition"
                                    >
                                        Change Role
                                    </button>

                                    <button
                                        v-if="canDeleteUsers && user.email !== 'superadmin@heyevents.com'"
                                        type="button"
                                        @click="deleteUser(user)"
                                        title="Delete user"
                                        class="rounded-lg p-1 text-muted-foreground hover:text-rose-600 hover:bg-rose-500/10 transition"
                                    >
                                        <Trash2 class="size-4" />
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination Bar -->
            <div
                v-if="users.last_page > 1"
                class="flex items-center justify-between border-t border-border/50 px-4 py-3 text-xs"
            >
                <span class="text-muted-foreground">
                    Showing Page {{ users.current_page }} of {{ users.last_page }} ({{ users.total }} users)
                </span>
                <div class="flex items-center gap-1">
                    <template v-for="(link, i) in users.links" :key="i">
                        <Link
                            v-if="link.url"
                            :href="link.url"
                            class="px-2.5 py-1 rounded-lg border text-[11px] font-medium transition"
                            :class="
                                link.active
                                    ? 'border-[#ff545a] bg-[#ff545a] text-white'
                                    : 'border-border/60 bg-background text-foreground hover:bg-muted'
                            "
                            v-html="link.label"
                        />
                    </template>
                </div>
            </div>
        </div>

        <!-- Role Change Modal -->
        <div
            v-if="roleModalOpen && selectedUser"
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs"
        >
            <div
                class="w-full max-w-md rounded-2xl border border-border bg-card p-6 shadow-2xl space-y-4"
            >
                <div class="flex items-center justify-between pb-2 border-b border-border/50">
                    <h3 class="text-base font-bold text-foreground">
                        Change User Role
                    </h3>
                    <button
                        type="button"
                        @click="closeRoleModal"
                        class="rounded-lg p-1 text-muted-foreground hover:text-foreground"
                    >
                        <X class="size-4" />
                    </button>
                </div>

                <div class="space-y-1 text-xs">
                    <p class="text-muted-foreground">
                        Updating role assignment for:
                    </p>
                    <p class="font-bold text-sm text-foreground">
                        {{ selectedUser.name }}
                        <span class="font-normal text-muted-foreground">({{ selectedUser.email }})</span>
                    </p>
                </div>

                <div class="space-y-2">
                    <label class="text-xs font-semibold text-foreground block">Select New Role</label>
                    <div class="space-y-2">
                        <label
                            v-for="role in availableRoles"
                            :key="role"
                            class="flex items-center gap-3 rounded-xl border p-3 cursor-pointer transition text-xs"
                            :class="
                                newRoleToAssign === role
                                    ? 'border-[#ff545a] bg-[#ff545a]/5 font-semibold text-foreground'
                                    : 'border-border/60 hover:bg-muted/40 text-muted-foreground'
                            "
                        >
                            <input
                                type="radio"
                                name="target_role"
                                :value="role"
                                v-model="newRoleToAssign"
                                class="text-[#ff545a] focus:ring-[#ff545a]"
                            />
                            <span>{{ role }}</span>
                        </label>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2 pt-2 border-t border-border/50">
                    <button
                        type="button"
                        @click="closeRoleModal"
                        class="rounded-xl border border-border px-3.5 py-2 text-xs font-semibold text-foreground hover:bg-muted"
                    >
                        Cancel
                    </button>
                    <button
                        type="button"
                        @click="submitRoleChange"
                        :disabled="isSubmittingRole"
                        class="rounded-xl bg-[#ff545a] px-4 py-2 text-xs font-semibold text-white shadow-xs hover:bg-[#e0454b] disabled:opacity-50"
                    >
                        {{ isSubmittingRole ? 'Saving...' : 'Confirm Role Change' }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
