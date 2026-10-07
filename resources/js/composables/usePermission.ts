import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import type { Auth } from '@/types/auth';

export function usePermission() {
    const page = usePage<{ auth: Auth }>();

    const user = computed(() => page.props.auth?.user ?? null);
    const roles = computed(() => user.value?.roles ?? []);
    const permissions = computed(() => user.value?.permissions ?? []);

    const isSuperAdmin = computed(() => roles.value.includes('Super Admin'));

    const hasRole = (role: string | string[]): boolean => {
        if (!user.value) return false;
        if (isSuperAdmin.value) return true;
        if (Array.isArray(role)) {
            return role.some((r) => roles.value.includes(r));
        }
        return roles.value.includes(role);
    };

    const hasPermission = (permission: string | string[]): boolean => {
        if (!user.value) return false;
        // Super Admin has universal access
        if (isSuperAdmin.value) return true;

        if (Array.isArray(permission)) {
            return permission.some((p) => permissions.value.includes(p));
        }
        return permissions.value.includes(permission);
    };

    const can = (permission: string | string[]): boolean => hasPermission(permission);
    const canAny = (permissionsList: string[]): boolean => permissionsList.some((p) => hasPermission(p));
    const canAll = (permissionsList: string[]): boolean => permissionsList.every((p) => hasPermission(p));

    return {
        user,
        roles,
        permissions,
        isSuperAdmin,
        hasRole,
        hasPermission,
        can,
        canAny,
        canAll,
    };
}
