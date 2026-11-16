import { ref, computed } from 'vue';
import { usePermission } from '@/composables/usePermission';

export type WorkspaceMode = 'provider' | 'client';

const STORAGE_KEY = 'heyevent_workspace_mode';

// Reactive singleton state
const activeMode = ref<WorkspaceMode>(
    (typeof window !== 'undefined' && (localStorage.getItem(STORAGE_KEY) as WorkspaceMode)) || 'provider'
);

export function useWorkspaceMode() {
    const { roles } = usePermission();

    const isServiceProvider = computed(() => roles.value.includes('Service Provider'));

    const setMode = (mode: WorkspaceMode) => {
        activeMode.value = mode;
        if (typeof window !== 'undefined') {
            try {
                localStorage.setItem(STORAGE_KEY, mode);
            } catch (e) {
                // Ignore storage errors in private browsing/sandboxes
            }
        }
    };

    const toggleMode = () => {
        setMode(activeMode.value === 'provider' ? 'client' : 'provider');
    };

    // If user is not a service provider, effective mode is always 'client' (unless admin)
    const effectiveMode = computed<WorkspaceMode>(() => {
        if (!isServiceProvider.value) {
            return 'client';
        }
        return activeMode.value;
    });

    const isClientMode = computed(() => effectiveMode.value === 'client');
    const isVendorMode = computed(() => effectiveMode.value === 'provider');

    return {
        activeMode,
        effectiveMode,
        isServiceProvider,
        isClientMode,
        isVendorMode,
        setMode,
        toggleMode,
    };
}

