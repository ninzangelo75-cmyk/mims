<template>
    <AppLayout>
        <div class="py-6">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="page-title">MIMS Dashboard</h2>
                        <p class="page-subtitle">Overview of inventory and requests</p>
                    </div>
                </div>
                <!-- Stat cards -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                    <div class="rounded-lg p-4 text-white shadow-sm bg-gradient-to-r from-orange-500 to-orange-400 text-center">
                        <p class="text-sm/5 opacity-90">Medicines in Stock</p>
                        <p class="text-2xl font-semibold mt-1">{{ formatNumber(stats.itemsInStock) }}</p>
                    </div>
                    <div class="rounded-lg p-4 text-white shadow-sm bg-gradient-to-r from-red-500 to-red-400 text-center">
                        <p class="text-sm/5 opacity-90">Low Stock Medicines</p>
                        <p class="text-2xl font-semibold mt-1">{{ formatNumber(stats.lowStock) }}</p>
                    </div>
                    <div class="rounded-lg p-4 text-white shadow-sm bg-gradient-to-r from-indigo-500 to-indigo-400 text-center">
                        <p class="text-sm/5 opacity-90">Pending Requests</p>
                        <p class="text-2xl font-semibold mt-1">
                            RIS {{ formatNumber(stats.pendingRis) }} | PTR {{ formatNumber(stats.pendingPtr) }}
                        </p>
                    </div>
                    <div class="rounded-lg p-4 text-white shadow-sm bg-gradient-to-r from-amber-500 to-amber-400 text-center">
                        <p class="text-sm/5 opacity-90">Expiring Soon</p>
                        <p class="text-2xl font-semibold mt-1">{{ formatNumber(stats.expiringSoon) }}</p>
                    </div>
                    <div class="rounded-lg p-4 text-white shadow-sm bg-gradient-to-r from-emerald-600 to-emerald-500 text-center">
                        <p class="text-sm/5 opacity-90">Total Items</p>
                        <p class="text-2xl font-semibold mt-1">{{ formatNumber(stats.totalItems) }}</p>
                    </div>
                </div>

                <!-- Main panels -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <div class="lg:col-span-2 space-y-6">
                        <div class="bg-white rounded-lg shadow-sm ring-1 ring-[#cfe8d1]">
                            <div class="px-5 py-4 border-b border-[#e6f2e7]">
                                <h3 class="font-semibold text-[#1b5e20]">Inventory Status</h3>
                            </div>
                            <div class="p-5">
                                <table class="w-full text-sm">
                                    <thead class="text-left text-gray-500">
                                        <tr>
                                            <th class="pb-3">Medicine</th>
                                            <th class="pb-3">Quantity</th>
                                            <th class="pb-3">Expiry Date</th>
                                            <th class="pb-3">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="text-gray-700">
                                        <tr v-if="!inventoryStatus.length" class="border-t border-gray-100">
                                            <td colspan="4" class="py-6 text-center text-sm text-gray-500">No inventory data found.</td>
                                        </tr>
                                        <tr v-for="item in inventoryStatus" :key="item.itemcode" class="border-t border-gray-100">
                                            <td class="py-3">{{ item.itemname }}</td>
                                            <td class="py-3">{{ formatNumber(item.remaining) }}</td>
                                            <td class="py-3">{{ formatDate(item.expiry_date) }}</td>
                                            <td class="py-3 align-middle">
                                                <span :class="statusClass(item.status)" class="px-2 py-1 rounded text-xs">
                                                    {{ item.status }}
                                                </span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="bg-white rounded-lg shadow-sm ring-1 ring-[#cfe8d1]">
                            <div class="px-5 py-4 border-b border-[#e6f2e7]">
                                <h3 class="font-semibold text-[#1b5e20]">Recent Requests</h3>
                            </div>
                            <div class="p-5">
                                <table class="w-full text-sm">
                                    <thead class="text-left text-gray-500">
                                        <tr>
                                            <th class="pb-3">Request No.</th>
                                            <th class="pb-3">Department</th>
                                            <th class="pb-3">Requested By</th>
                                            <th class="pb-3">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="text-gray-700">
                                        <tr v-if="!recentRequests.length" class="border-t border-gray-100">
                                            <td colspan="4" class="py-6 text-center text-sm text-gray-500">No recent requests found.</td>
                                        </tr>
                                        <tr v-for="request in recentRequests" :key="request.request_no" class="border-t border-gray-100">
                                            <td class="py-3">{{ request.request_no }}</td>
                                            <td class="py-3">{{ request.department || 'N/A' }}</td>
                                            <td class="py-3">{{ request.requested_by || 'N/A' }}</td>
                                            <td class="py-3 align-middle">
                                                <span :class="statusClass(request.status)" class="px-2 py-1 rounded text-xs">
                                                    {{ request.status }}
                                                </span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow-sm ring-1 ring-[#cfe8d1] h-fit">
                        <div class="px-5 py-4 border-b border-[#e6f2e7]">
                            <h3 class="font-semibold text-[#1b5e20]">Notifications</h3>
                        </div>
                        <div class="p-5 space-y-3 text-sm text-gray-700">
                            <div v-if="!notifications.length" class="text-sm text-gray-500">
                                No notifications yet.
                            </div>
                            <div v-for="(note, index) in notifications" :key="index" class="flex items-start gap-3">
                                <span :class="notificationDot(note.type)" class="mt-1 w-2 h-2 rounded-full"></span>
                                <p>{{ note.message }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import AppLayout from '@/Layouts/AppLayout.vue';

type InventoryStatusItem = {
    itemcode: number;
    itemname: string;
    remaining: number;
    expiry_date: string | null;
    status: 'In Stock' | 'Low Stock' | 'Expiring';
};

type RecentRequest = {
    type: 'RIS' | 'PTR';
    request_no: string;
    department: string | null;
    requested_by: string | null;
    status: 'Pending' | 'Approved';
};

type NotificationItem = {
    type: string;
    message: string;
};

type Stats = {
    totalItems: number;
    itemsInStock: number;
    lowStock: number;
    pendingRis: number;
    pendingPtr: number;
    expiringSoon: number;
};

type Props = {
    stats: Stats;
    inventoryStatus: InventoryStatusItem[];
    recentRequests: RecentRequest[];
    notifications: NotificationItem[];
};

const props = defineProps<Props>();
const { stats, inventoryStatus, recentRequests, notifications } = props;

const formatNumber = (value: number) => {
    return value?.toLocaleString() ?? '0';
};

const formatDate = (value: string | null) => {
    if (!value) return 'N/A';
    return new Intl.DateTimeFormat('en-US', { year: 'numeric', month: 'short', day: 'numeric' }).format(new Date(value));
};

const statusClass = (status: string) => {
    if (status === 'Low Stock') return 'bg-red-100 text-red-700';
    if (status === 'Expiring') return 'bg-amber-100 text-amber-700';
    if (status === 'Approved') return 'bg-emerald-100 text-emerald-700';
    if (status === 'Pending') return 'bg-amber-100 text-amber-700';
    return 'bg-emerald-100 text-emerald-700';
};

const notificationDot = (type: string) => {
    if (type.includes('low_stock')) return 'bg-red-500';
    if (type.includes('expiring')) return 'bg-amber-500';
    if (type.includes('pending')) return 'bg-indigo-500';
    return 'bg-emerald-500';
};
</script>



