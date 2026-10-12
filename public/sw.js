const CACHE_NAME = 'hey-events-cache-v1';
const OFFLINE_FALLBACK_URL = '/offline.html';

const PRECACHE_ASSETS = [
    OFFLINE_FALLBACK_URL,
    '/manifest.json',
    '/manifest.webmanifest',
    '/favicon.svg',
    '/favicon-dark.png',
    '/apple-touch-icon.png',
    '/icons/icon-192.png',
    '/icons/icon-512.png',
    '/icons/icon-maskable-192.png',
    '/icons/icon-maskable-512.png',
];

// Install event - Precache offline shell & static basics
self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => {
            return cache.addAll(PRECACHE_ASSETS);
        })
    );
    self.skipWaiting();
});

// Activate event - Clean up old cache iterations & claim clients
self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((cacheNames) => {
            return Promise.all(
                cacheNames.map((cacheName) => {
                    if (cacheName !== CACHE_NAME) {
                        return caches.delete(cacheName);
                    }
                })
            );
        }).then(() => self.clients.claim())
    );
});

// Fetch event - handle network requests & caching
self.addEventListener('fetch', (event) => {
    const { request } = event;

    // Only handle GET requests
    if (request.method !== 'GET') {
        return;
    }

    const url = new URL(request.url);

    // Skip internal browser extensions, dev servers hot-reloading (Vite HMR/ws), etc.
    if (!url.protocol.startsWith('http')) {
        return;
    }

    // 1. Static Assets (Vite build assets, images, icons, fonts)
    const isStaticAsset =
        url.pathname.startsWith('/build/') ||
        url.pathname.startsWith('/images/') ||
        url.pathname.startsWith('/icons/') ||
        url.hostname.includes('fonts.bunny.net') ||
        url.pathname.match(/\.(png|jpg|jpeg|svg|webp|gif|css|js|woff2?|ttf|eot)$/i);

    if (isStaticAsset) {
        event.respondWith(
            caches.match(request).then((cachedResponse) => {
                if (cachedResponse) {
                    // Stale-while-revalidate in background
                    fetch(request)
                        .then((networkResponse) => {
                            if (networkResponse && networkResponse.status === 200) {
                                caches.open(CACHE_NAME).then((cache) => {
                                    cache.put(request, networkResponse);
                                });
                            }
                        })
                        .catch(() => {
                            // Silently fail revalidation if offline
                        });
                    return cachedResponse;
                }

                return fetch(request).then((networkResponse) => {
                    if (networkResponse && networkResponse.status === 200) {
                        const responseClone = networkResponse.clone();
                        caches.open(CACHE_NAME).then((cache) => {
                            cache.put(request, responseClone);
                        });
                    }
                    return networkResponse;
                });
            })
        );
        return;
    }

    // 2. Navigation requests (Page loading and Inertia visits)
    const isNavigation =
        request.mode === 'navigate' ||
        request.headers.get('X-Inertia') === 'true' ||
        (request.headers.get('accept') && request.headers.get('accept').includes('text/html'));

    if (isNavigation) {
        event.respondWith(
            fetch(request)
                .then((networkResponse) => {
                    // If network succeeds and is OK, clone and cache for offline reading
                    if (networkResponse && networkResponse.status === 200) {
                        const responseClone = networkResponse.clone();
                        caches.open(CACHE_NAME).then((cache) => {
                            cache.put(request, responseClone);
                        });
                    }
                    return networkResponse;
                })
                .catch(async () => {
                    // Try to retrieve from cache first
                    const cachedResponse = await caches.match(request);
                    if (cachedResponse) {
                        return cachedResponse;
                    }

                    // For full document navigations, return our branded offline page
                    if (request.mode === 'navigate') {
                        const fallbackResponse = await caches.match(OFFLINE_FALLBACK_URL);
                        if (fallbackResponse) {
                            return fallbackResponse;
                        }
                    }

                    // Otherwise return a standard error response
                    return new Response(
                        JSON.stringify({
                            error: 'You are currently offline. Please reconnect to continue.',
                        }),
                        {
                            status: 503,
                            statusText: 'Service Unavailable',
                            headers: { 'Content-Type': 'application/json' },
                        }
                    );
                })
        );
        return;
    }

    // 3. Default network-first for any other GET requests
    event.respondWith(
        fetch(request).catch(() => {
            return caches.match(request);
        })
    );
});

// Support manual skip-waiting trigger from client
self.addEventListener('message', (event) => {
    if (event.data && event.data.type === 'SKIP_WAITING') {
        self.skipWaiting();
    }
});
