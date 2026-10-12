import { ref } from 'vue';

interface BeforeInstallPromptEvent extends Event {
    readonly platforms: string[];
    readonly userChoice: Promise<{
        outcome: 'accepted' | 'dismissed';
        platform: string;
    }>;
    prompt(): Promise<void>;
}

const isInstallable = ref(false);
const isInstalled = ref(false);
const isOffline = ref(typeof navigator !== 'undefined' ? !navigator.onLine : false);
const needRefresh = ref(false);
const isIos = ref(false);
const showIosPrompt = ref(false);

let deferredPrompt: BeforeInstallPromptEvent | null = null;
let registration: ServiceWorkerRegistration | null = null;

export function usePwa() {
    const installApp = async (): Promise<boolean> => {
        if (!deferredPrompt) {
            return false;
        }

        await deferredPrompt.prompt();
        const choice = await deferredPrompt.userChoice;
        if (choice.outcome === 'accepted') {
            isInstallable.value = false;
            deferredPrompt = null;
            return true;
        }

        return false;
    };

    const dismissInstall = () => {
        isInstallable.value = false;
        sessionStorage.setItem('hey_pwa_dismissed', 'true');
    };

    const dismissIosPrompt = () => {
        showIosPrompt.value = false;
        sessionStorage.setItem('hey_pwa_ios_dismissed', 'true');
    };

    const updateApp = () => {
        if (registration && registration.waiting) {
            registration.waiting.postMessage({ type: 'SKIP_WAITING' });
            window.location.reload();
        }
    };

    return {
        isInstallable,
        isInstalled,
        isOffline,
        needRefresh,
        isIos,
        showIosPrompt,
        installApp,
        dismissInstall,
        dismissIosPrompt,
        updateApp,
    };
}

export function initializePwa() {
    if (typeof window === 'undefined') {
        return;
    }

    // Check if launched as installed PWA / standalone
    const isStandalone =
        window.matchMedia('(display-mode: standalone)').matches ||
        (window.navigator as unknown as { standalone?: boolean }).standalone === true;

    if (isStandalone) {
        isInstalled.value = true;
    }

    // iOS Detection
    const isIosDevice =
        /iphone|ipad|ipod/i.test(window.navigator.userAgent) &&
        !(window as unknown as { MSStream?: unknown }).MSStream;

    isIos.value = isIosDevice;

    const isIosDismissed = sessionStorage.getItem('hey_pwa_ios_dismissed') === 'true';
    if (isIosDevice && !isStandalone && !isIosDismissed) {
        showIosPrompt.value = true;
    }

    // Network connectivity tracking
    window.addEventListener('online', () => {
        isOffline.value = false;
    });

    window.addEventListener('offline', () => {
        isOffline.value = true;
    });

    // Listen for install prompt (Chrome, Edge, Android browsers)
    window.addEventListener('beforeinstallprompt', (e: Event) => {
        console.log('[PWA] beforeinstallprompt event fired by browser');
        e.preventDefault();
        deferredPrompt = e as BeforeInstallPromptEvent;

        const isDismissed = sessionStorage.getItem('hey_pwa_dismissed') === 'true';
        if (!isDismissed && !isStandalone) {
            isInstallable.value = true;
        }
    });

    window.addEventListener('appinstalled', () => {
        console.log('[PWA] App successfully installed');
        isInstalled.value = true;
        isInstallable.value = false;
        deferredPrompt = null;
    });

    // Service Worker registration
    if ('serviceWorker' in navigator && (window.isSecureContext || window.location.protocol === 'https:' || window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1')) {
        navigator.serviceWorker
            .register('/sw.js', { scope: '/' })
            .then((reg) => {
                registration = reg;
                console.log('[PWA] Service Worker registered with scope:', reg.scope);

                // Check for updates
                reg.addEventListener('updatefound', () => {
                    const newWorker = reg.installing;
                    if (newWorker) {
                        newWorker.addEventListener('statechange', () => {
                            if (newWorker.state === 'installed' && navigator.serviceWorker.controller) {
                                needRefresh.value = true;
                            }
                        });
                    }
                });
            })
            .catch((err) => {
                console.warn('[PWA] ServiceWorker registration failed:', err);
            });

        // Controller change reload trigger
        let refreshing = false;
        navigator.serviceWorker.addEventListener('controllerchange', () => {
            if (!refreshing) {
                refreshing = true;
                window.location.reload();
            }
        });
    }
}
