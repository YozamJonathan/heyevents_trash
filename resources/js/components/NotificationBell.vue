<script setup lang="ts">
import { computed, ref, onMounted, onUnmounted } from 'vue';
import { usePage, router } from '@inertiajs/vue3';
import {
    Bell,
    CheckCheck,
    BadgeCheck,
    AlertCircle,
    MessageSquare,
    Clock,
    X,
} from '@lucide/vue';
import { useLanguage } from '@/composables/useLanguage';

const page = usePage();
const { t } = useLanguage();

const isOpen = ref(false);
const containerRef = ref<HTMLElement | null>(null);

const notifications = computed<any[]>(() => {
    return (page.props.auth as any)?.notifications ?? [];
});

const unreadCount = computed<number>(() => {
    return (page.props.auth as any)?.unread_notifications_count ?? 0;
});

function toggleDropdown() {
    isOpen.value = !isOpen.value;
}

function handleClickOutside(event: MouseEvent) {
    if (containerRef.value && !containerRef.value.contains(event.target as Node)) {
        isOpen.value = false;
    }
}

onMounted(() => {
    document.addEventListener('click', handleClickOutside);
});

onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside);
});

function markAsRead(notif: any) {
    if (!notif.read_at) {
        router.post(
            `/notifications/${notif.id}/read`,
            {},
            {
                preserveScroll: true,
                preserveState: true,
            },
        );
    }
    if (notif.data?.action_url) {
        isOpen.value = false;
        router.visit(notif.data.action_url);
    }
}

function markAllAsRead() {
    router.post(
        '/notifications/mark-all-read',
        {},
        {
            preserveScroll: true,
            preserveState: true,
        },
    );
}

function formatRelativeTime(dateString?: string) {
    if (!dateString) return '';
    try {
        const date = new Date(dateString);
        const now = new Date();
        const diffMs = now.getTime() - date.getTime();
        const diffMins = Math.floor(diffMs / 60000);
        const diffHours = Math.floor(diffMins / 60);
        const diffDays = Math.floor(diffHours / 24);

        if (diffMins < 1) return 'Just now';
        if (diffMins < 60) return `${diffMins}m ago`;
        if (diffHours < 24) return `${diffHours}h ago`;
        if (diffDays === 1) return 'Yesterday';
        if (diffDays < 7) return `${diffDays}d ago`;

        return date.toLocaleDateString(undefined, {
            month: 'short',
            day: 'numeric',
        });
    } catch {
        return '';
    }
}
</script>

<template>
    <div ref="containerRef" class="relative inline-block">
        <!-- Bell Trigger Button -->
        <button
            type="button"
            @click="toggleDropdown"
            class="relative inline-flex size-8 shrink-0 items-center justify-center rounded-lg border border-sidebar-border bg-sidebar-accent/40 text-foreground shadow-2xs transition-colors hover:border-[#ff545a]/50 hover:bg-sidebar-accent hover:text-[#ff545a] focus:outline-hidden"
            :title="t('nav.notifications') || 'Notifications'"
            :aria-label="t('nav.notifications') || 'Notifications'"
        >
            <Bell class="size-4" />

            <!-- Unread Count Badge -->
            <span
                v-if="unreadCount > 0"
                class="absolute -top-1 -right-1 flex min-w-4 h-4 items-center justify-center rounded-full bg-[#ff545a] px-1 text-[10px] font-black text-white shadow-xs ring-2 ring-background animate-pulse"
            >
                {{ unreadCount > 9 ? '9+' : unreadCount }}
            </span>
        </button>

        <!-- Notification Popover / Dropdown -->
        <transition
            enter-active-class="transition duration-150 ease-out"
            enter-from-class="translate-y-1 opacity-0 scale-95"
            enter-to-class="translate-y-0 opacity-100 scale-100"
            leave-active-class="transition duration-100 ease-in"
            leave-from-class="translate-y-0 opacity-100 scale-100"
            leave-to-class="translate-y-1 opacity-0 scale-95"
        >
            <div
                v-if="isOpen"
                class="absolute right-0 z-50 mt-2 w-80 sm:w-96 rounded-2xl border border-border/80 bg-background/95 p-0 shadow-2xl backdrop-blur-xl ring-1 ring-black/5 dark:bg-zinc-900/95 dark:ring-white/10"
            >
                <!-- Popover Header -->
                <div
                    class="flex items-center justify-between border-b border-border/60 px-4 py-3"
                >
                    <div class="flex items-center gap-2">
                        <h4 class="text-foreground text-sm font-bold">
                            {{ t('nav.notifications') || 'Notifications' }}
                        </h4>
                        <span
                            v-if="unreadCount > 0"
                            class="rounded-full bg-[#ff545a]/10 px-2 py-0.5 text-[10px] font-bold text-[#ff545a]"
                        >
                            {{ unreadCount }} new
                        </span>
                    </div>

                    <button
                        v-if="unreadCount > 0"
                        type="button"
                        @click="markAllAsRead"
                        class="inline-flex items-center gap-1 text-[11px] font-semibold text-[#ff545a] hover:underline"
                    >
                        <CheckCheck class="size-3" />
                        <span>Mark all as read</span>
                    </button>
                </div>

                <!-- Notification Items List -->
                <div class="max-h-80 overflow-y-auto divide-y divide-border/40">
                    <!-- Empty State -->
                    <div
                        v-if="notifications.length === 0"
                        class="flex flex-col items-center justify-center py-10 px-4 text-center"
                    >
                        <div
                            class="mb-2 rounded-full bg-muted/60 p-2.5 text-muted-foreground"
                        >
                            <Bell class="size-5" />
                        </div>
                        <p class="text-foreground text-xs font-semibold">
                            No notifications yet
                        </p>
                        <p class="text-muted-foreground mt-0.5 text-[11px]">
                            Updates and admin messages will appear here.
                        </p>
                    </div>

                    <!-- Items -->
                    <div
                        v-for="notif in notifications"
                        :key="notif.id"
                        @click="markAsRead(notif)"
                        class="group flex items-start gap-3 p-3.5 transition-colors cursor-pointer hover:bg-muted/40"
                        :class="!notif.read_at ? 'bg-primary/5 dark:bg-primary/5' : ''"
                    >
                        <!-- Icon -->
                        <div class="mt-0.5 shrink-0">
                            <div
                                v-if="notif.data?.type === 'approved'"
                                class="rounded-xl bg-emerald-500/15 p-2 text-emerald-600 dark:text-emerald-400"
                            >
                                <BadgeCheck class="size-4" />
                            </div>
                            <div
                                v-else-if="notif.data?.type === 'rejected'"
                                class="rounded-xl bg-rose-500/15 p-2 text-rose-600 dark:text-rose-400"
                            >
                                <AlertCircle class="size-4" />
                            </div>
                            <div
                                v-else-if="notif.data?.type === 'feedback'"
                                class="rounded-xl bg-blue-500/15 p-2 text-blue-600 dark:text-blue-400"
                            >
                                <MessageSquare class="size-4" />
                            </div>
                            <div
                                v-else
                                class="rounded-xl bg-amber-500/15 p-2 text-amber-600 dark:text-amber-400"
                            >
                                <Bell class="size-4" />
                            </div>
                        </div>

                        <!-- Content -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between gap-1">
                                <h5
                                    class="text-xs font-bold truncate"
                                    :class="!notif.read_at ? 'text-foreground' : 'text-muted-foreground'"
                                >
                                    {{ notif.data?.title || 'Notification' }}
                                </h5>
                                <span class="text-[10px] text-muted-foreground shrink-0 font-medium">
                                    {{ formatRelativeTime(notif.created_at) }}
                                </span>
                            </div>

                            <p
                                class="mt-1 text-[11px] leading-relaxed line-clamp-3"
                                :class="!notif.read_at ? 'text-zinc-800 dark:text-zinc-200 font-medium' : 'text-muted-foreground'"
                            >
                                {{ notif.data?.message }}
                            </p>

                            <div class="mt-1.5 flex items-center justify-between text-[10px] text-muted-foreground">
                                <span v-if="notif.data?.sender_name">
                                    From: {{ notif.data.sender_name }}
                                </span>
                                <span v-if="!notif.read_at" class="inline-flex items-center gap-1 font-bold text-[#ff545a]">
                                    <span class="size-1.5 rounded-full bg-[#ff545a]"></span>
                                    <span>Unread</span>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </transition>
    </div>
</template>

