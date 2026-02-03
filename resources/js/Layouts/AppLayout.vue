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
                        <button
                            type="button"
                            class="relative p-2 text-[#1b5e20] hover:bg-white/60 rounded-lg transition-colors"
                            title="Notifications"
                        >
                            <Icon name="bell" className="w-5 h-5" />
                            <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
                        </button>

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
    </div>
</template>

<script setup lang="ts">
import { computed, ref } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import FlashMessage from '@/Components/FlashMessage.vue';
import Icon from '@/Components/Icon.vue';

const page = usePage();
const user = computed(() => page.props.auth.user as any);

type MenuIcon =
    | 'pill'
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
