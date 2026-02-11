<template>
    <div class="flex h-screen bg-[#f6fbf6]">
        <!-- Sidebar -->
        <aside
            class="bg-[#1b5e20] text-white transition-all duration-300 flex flex-col"
            :class="isCollapsed ? 'w-20' : 'w-64'"
        >
            <div class="p-4 flex items-center justify-between border-b border-[#2e7d32]">
                <div v-if="!isCollapsed" class="flex items-center space-x-2">
                    <Icon name="pill" className="w-8 h-8" />
                    <span class="font-bold text-lg">SKPH - MIMS</span>
                </div>
                <div v-else class="w-full flex justify-center">
                    <Icon name="pill" className="w-8 h-8" />
                </div>
            </div>

            <nav class="flex-1 py-4">
                <Link
                    v-for="item in menuItems"
                    :key="item.id"
                    :href="item.href"
                    class="w-full flex items-center space-x-3 px-4 py-3 transition-colors"
                    :class="isActive(item) ? 'bg-[#2e7d32] border-r-4 border-[#e8f5e9]' : 'hover:bg-[#2e7d32]'"
                >
                    <Icon :name="item.icon" :className="`${isCollapsed ? 'mx-auto ' : ''}w-5 h-5`" />
                    <span v-if="!isCollapsed" class="text-sm">{{ item.label }}</span>
                </Link>
            </nav>

            <button
                type="button"
                @click="toggleSidebar"
                class="p-4 flex items-center justify-center border-t border-[#2e7d32] hover:bg-[#2e7d32] transition-colors"
            >
                <Icon v-if="isCollapsed" name="chevron-right" className="w-5 h-5" />
                <div v-else class="flex items-center space-x-2">
                    <Icon name="chevron-left" className="w-5 h-5" />
                    <span class="text-sm">Collapse</span>
                </div>
            </button>
        </aside>

        <!-- Main -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Top header -->
            <header class="bg-[#e8f5e9] border-b border-[#cfe8d1] px-6 py-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-[#1b5e20]">Medicine Inventory System</h1>
                        <p class="text-sm text-[#1b5e20]/80">Manage your pharmaceutical inventory efficiently</p>
                    </div>

                    <div class="flex items-center space-x-4">
                        <div class="relative">
                            <button
                                type="button"
                                class="relative p-2 text-[#1b5e20] hover:bg-white/60 rounded-lg transition-colors"
                                title="Notifications"
                                @click="toggleNotifications"
                            >
                                <Icon name="bell" className="w-5 h-5" />
                                <span
                                    v-if="notificationCount > 0"
                                    class="absolute -top-1 -right-1 min-w-[18px] h-[18px] px-1 bg-red-500 text-white text-[10px] leading-[18px] rounded-full text-center"
                                >
                                    {{ notificationCount }}
                                </span>
                            </button>

                            <div
                                v-if="showNotifications"
                                class="absolute right-0 mt-2 w-80 rounded-xl border border-[#cfe8d1] bg-white shadow-lg z-50"
                            >
                                <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100">
                                    <p class="text-sm font-semibold text-gray-900">Notifications</p>
                                    <button
                                        type="button"
                                        class="text-gray-500 hover:text-gray-700"
                                        @click="showNotifications = false"
                                        aria-label="Close notifications"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                                <div class="max-h-56 overflow-y-auto">
                                    <div v-if="notifications.length === 0" class="px-4 py-6 text-sm text-gray-500">
                                        No notifications yet.
                                    </div>
                                    <button
                                        v-for="(note, index) in notifications"
                                        :key="index"
                                        type="button"
                                        class="w-full text-left px-4 py-3 border-b border-gray-100 text-sm text-gray-700 hover:bg-[#f6fbf6]"
                                        @click="handleNotificationClick(note)"
                                    >
                                        <div class="flex items-start gap-3">
                                            <span :class="notificationDot(note.type)" class="mt-1 w-2 h-2 rounded-full"></span>
                                            <div class="flex-1">
                                                <p class="font-medium text-gray-900">{{ note.message }}</p>
                                                <p v-if="note.time" class="text-xs text-gray-500 mt-1">{{ formatTime(note.time) }}</p>
                                            </div>
                                        </div>
                                    </button>
                                </div>
                                <div class="px-4 py-3 border-t border-gray-100">
                                    <button
                                        type="button"
                                        class="w-full text-center text-sm font-semibold text-[#1b5e20] hover:text-[#2e7d32]"
                                        @click="openAllNotifications"
                                    >
                                        View All Notifications
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center space-x-3 pl-4 border-l border-[#cfe8d1]">
                            <div class="text-right">
                                <p class="text-sm font-medium text-[#1b5e20]">
                                    {{ user?.fullname || 'User' }}
                                </p>
                                <p class="text-xs text-[#1b5e20]/80">
                                    {{ roleLabel }}
                                </p>
                            </div>
                            <div class="w-10 h-10 bg-[#2e7d32] rounded-full flex items-center justify-center text-white font-semibold">
                                {{ initials }}
                            </div>
                        </div>

                        <button
                            type="button"
                            @click="logout"
                            class="p-2 text-[#1b5e20] hover:bg-white/60 rounded-lg transition-colors"
                            title="Sign Out"
                        >
                            <Icon name="log-out" className="w-5 h-5" />
                        </button>
                    </div>
                </div>
            </header>

            <FlashMessage />

            <main class="flex-1 overflow-y-auto">
                <div v-if="$slots.header" class="bg-[#e8f5e9] border-b border-[#cfe8d1]">
                    <div class="px-6 py-6">
                        <slot name="header" />
                    </div>
                </div>

                <div class="p-6">
                    <slot />
                </div>
            </main>
        </div>

        <Modal :show="showAllNotifications" :showFooter="false" maxWidth="sm:max-w-3xl" @close="showAllNotifications = false">
            <div class="-mx-6 -mt-6 mb-6 rounded-t-lg bg-[#2e7d32] px-6 py-4 text-white">
                <h3 class="text-lg font-semibold">All Notifications</h3>
                <p class="text-sm text-white/80">Complete notification list</p>
            </div>
            <div class="max-h-[70vh] overflow-y-auto">
                <div v-if="notifications.length === 0" class="px-6 py-8 text-sm text-gray-500">
                    No notifications yet.
                </div>
                <button
                    v-for="(note, index) in notifications"
                    :key="index"
                    type="button"
                    class="w-full text-left px-6 py-4 border-b border-gray-100 text-sm text-gray-700 hover:bg-[#f6fbf6]"
                    @click="handleNotificationClick(note)"
                >
                    <div class="flex items-start gap-3">
                        <span :class="notificationDot(note.type)" class="mt-1 w-2 h-2 rounded-full"></span>
                        <div class="flex-1">
                            <p class="font-medium text-gray-900">{{ note.message }}</p>
                            <p v-if="note.time" class="text-xs text-gray-500 mt-1">{{ formatTime(note.time) }}</p>
                        </div>
                    </div>
                </button>
            </div>
            <div class="-mx-6 -mb-6 mt-6 rounded-b-lg bg-[#2e7d32] px-6 py-4 flex items-center justify-end">
                <button
                    type="button"
                    class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-medium text-[#1b5e20] shadow-sm hover:bg-[#e8f5e9]"
                    @click="showAllNotifications = false"
                >
                    Close
                </button>
            </div>
        </Modal>
    </div>
</template>

<script setup lang="ts">
import { computed, ref } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import FlashMessage from '@/Components/FlashMessage.vue';
import Icon from '@/Components/Icon.vue';
import Modal from '@/Components/Modal.vue';

const page = usePage();
const user = computed(() => page.props.auth.user as any);

type MenuIcon =
    | 'pill'
    | 'dashboard'
    | 'package-check'
    | 'package'
    | 'clipboard-list'
    | 'check-circle'
    | 'file-text'
    | 'scroll-text'
    | 'user';

type MenuItem = {
    id:
        | 'medicine-entry'
        | 'receiving'
        | 'inventory'
        | 'request'
        | 'approval'
        | 'reports'
        | 'audit-logs'
        | 'user-account';
    label: string;
    href: string;
    icon: MenuIcon;
    roles: Array<'ADMIN' | 'STAFF' | 'USER'>;
};

const isCollapsed = ref<boolean>(localStorage.getItem('sidebar_collapsed') === '1');

const toggleSidebar = () => {
    isCollapsed.value = !isCollapsed.value;
    localStorage.setItem('sidebar_collapsed', isCollapsed.value ? '1' : '0');
};

const menuItems = computed(() => {
    const role = user.value?.role as 'ADMIN' | 'STAFF' | 'USER' | undefined;

    const all: MenuItem[] = [
        { id: 'dashboard', label: 'Dashboard', icon: 'dashboard', href: '/dashboard', roles: ['ADMIN', 'STAFF', 'USER'] },
        { id: 'medicine-entry', label: 'Medicine Entry', icon: 'pill', href: '/items', roles: ['ADMIN'] },
        { id: 'receiving', label: 'Receiving', icon: 'package-check', href: '/receiving', roles: ['ADMIN', 'STAFF'] },
        { id: 'inventory', label: 'Inventory', icon: 'package', href: '/inventory', roles: ['ADMIN', 'STAFF', 'USER'] },
        { id: 'request', label: 'Request', icon: 'clipboard-list', href: '/requests/ris', roles: ['ADMIN', 'STAFF', 'USER'] },
        { id: 'approval', label: 'Approval', icon: 'check-circle', href: '/approvals', roles: ['ADMIN', 'STAFF'] },
        { id: 'reports', label: 'Reports', icon: 'file-text', href: '/reports', roles: ['ADMIN', 'STAFF'] },
        { id: 'audit-logs', label: 'Audit Logs', icon: 'scroll-text', href: '/audit-logs', roles: ['ADMIN'] },
        { id: 'user-account', label: 'User Management', icon: 'user', href: '/account', roles: ['ADMIN'] },
    ];

    if (!role) return [];
    return all.filter((i) => i.roles.includes(role));
});

const logout = () => {
    router.post('/logout');
};

const showNotifications = ref(false);
const showAllNotifications = ref(false);
const notifications = computed(() => {
    const list = (page.props.notifications as Array<{ type: string; message: string; time?: string | null; href?: string }> | undefined) || [];
    return [...list].sort((a, b) => {
        const at = a.time ? new Date(a.time).getTime() : 0;
        const bt = b.time ? new Date(b.time).getTime() : 0;
        return bt - at;
    });
});
const notificationCount = computed(() => notifications.value.length);

const toggleNotifications = () => {
    showNotifications.value = !showNotifications.value;
};

const openAllNotifications = () => {
    showNotifications.value = false;
    showAllNotifications.value = true;
};

const resolveNotificationHref = (note: { href?: string; type?: string }) => {
    if (note.href) return note.href;
    const type = note.type || '';
    if (type.includes('low_stock') || type.includes('expiring')) return '/inventory';
    if (type.includes('new_ris')) return '/requests/ris';
    if (type.includes('new_ptr')) return '/requests/ptr';
    if (type.includes('pending')) return '/approvals';
    return undefined;
};

const handleNotificationClick = (note: { href?: string; type?: string }) => {
    const href = resolveNotificationHref(note);
    if (href) {
        showNotifications.value = false;
        showAllNotifications.value = false;
        router.visit(href);
    }
};

const notificationDot = (type: string) => {
    if (type.includes('low_stock')) return 'bg-red-500';
    if (type.includes('expiring')) return 'bg-amber-500';
    if (type.includes('pending')) return 'bg-indigo-500';
    return 'bg-emerald-500';
};

const formatTime = (value: string) => {
    const date = new Date(value);
    return Number.isNaN(date.getTime()) ? value : date.toLocaleString();
};

const isActive = (item: MenuItem) => {
    const url = (page.url || '/') as string;
    if (item.href === '/requests/ris') return url.startsWith('/requests');
    return url.startsWith(item.href);
};

const initials = computed(() => {
    const name = (user.value?.fullname || user.value?.username || 'User') as string;
    const parts = name.trim().split(/\s+/).filter(Boolean);
    const first = parts[0]?.[0] ?? 'U';
    const last = parts.length > 1 ? parts[parts.length - 1]?.[0] : '';
    return (first + last).toUpperCase();
});

const roleLabel = computed(() => {
    const role = user.value?.role as string | undefined;
    if (role === 'ADMIN') return 'Administrator';
    if (role === 'STAFF') return 'Staff';
    if (role === 'USER') return 'User';
    return 'User';
});
</script>
