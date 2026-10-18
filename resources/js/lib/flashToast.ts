import { router } from '@inertiajs/vue3';
import { toast } from 'vue-sonner';
import type { FlashToast } from '@/types/ui';

let lastShownMessage = '';

export function initializeFlashToast(): void {
    const handleFlash = (pageProps: any) => {
        const flash = pageProps?.flash;
        const data = flash?.toast as FlashToast | undefined;
        const success = flash?.success as string | undefined;
        const error = flash?.error as string | undefined;

        if (data?.message && data.message !== lastShownMessage) {
            lastShownMessage = data.message;
            toast[data.type || 'success'](data.message);
            return;
        }

        if (success && success !== lastShownMessage) {
            lastShownMessage = success;
            toast.success(success);
            return;
        }

        if (error && error !== lastShownMessage) {
            lastShownMessage = error;
            toast.error(error);
            return;
        }
    };

    router.on('navigate', (event) => {
        handleFlash(event.detail?.page?.props);
    });

    router.on('success', (event) => {
        handleFlash(event.detail?.page?.props);
    });
}
