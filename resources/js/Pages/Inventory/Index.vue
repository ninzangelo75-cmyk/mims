<template>
    <AppLayout>
        <div class="py-6">
            <div class="mx-auto max-w-7xl space-y-4 px-0 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="page-title">Inventory</h2>
                        <p class="page-subtitle">Monitor medicine stock levels</p>
                    </div>
                </div>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                    <SummaryCard
                        title="Total Items"
                        :value="summary.totalItems"
                        icon-color="text-indigo-500"
                        icon-bg="bg-indigo-50"
                        icon-path="M3 7.5l9-4.5 9 4.5m-18 0l9 4.5m-9-4.5V16.5l9 4.5m0-9V21m0-9l9-4.5m0 0v9"
                    />
                    <SummaryCard
                        title="Low Stock Items"
                        :value="summary.lowStockItems"
                        icon-color="text-orange-500"
                        icon-bg="bg-orange-50"
                        icon-path="M12 9v3.75m-7.5 0L10.29 3.24c.66-1.14 2.28-1.14 2.94 0L19.5 12.75M12 15.75h.007v.008H12Z"
                    />
                    <SummaryCard
                        title="Total Value"
                        :value="formatCurrency(summary.totalValue)"
                        icon-color="text-green-500"
                        icon-bg="bg-green-50"
                        icon-path="M3 12c0 4.97 4.03 9 9 9s9-4.03 9-9-4.03-9-9-9-9 4.03-9 9Zm9-5v10m4-6H8"
                    />
                </div>

                <div class="overflow-hidden rounded-2xl bg-[#f6fbf6] shadow ring-1 ring-[#cfe8d1]">
                    <div class="border-b border-gray-200 px-6 py-4">
                        <div class="flex items-center justify-between gap-4">
                            <h3 class="text-base font-semibold text-gray-900">Current Inventory Status</h3>
                            <div class="relative w-full max-w-xs">
                                <svg class="pointer-events-none absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 104.5 4.5a7.5 7.5 0 0012.15 12.15z" />
                                </svg>
                                <input
                                    v-model="search"
                                    type="text"
                                    placeholder="Search medicine or status..."
                                    class="w-full rounded-lg border border-gray-300 pl-11 pr-4 py-2.5 text-sm shadow-sm transition focus:border-[#2e7d32] focus:outline-none focus:ring-2 focus:ring-[#2e7d32]"
                                    @input="handleSearch"
                                />
                            </div>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-[#e8f5e9]">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                        Medicine Name
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                        Current Stock
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                        Reorder Level
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                        Status
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-[#f6fbf6]">
                                <tr v-if="!inventory.length">
                                    <td colspan="4" class="px-6 py-6 text-center text-sm text-gray-500">
                                        No inventory data found.
                                    </td>
                                </tr>
                                <tr v-for="item in inventory" :key="item.itemcode" class="hover:bg-[#e8f5e9]">
                                    <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-900">
                                        {{ item.itemname }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900">
                                        {{ formatNumber(item.remaining) }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900">
                                        {{ formatNumber(item.reorder_level) }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4">
                                        <span :class="statusClass(item.status)" class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold">
                                            {{ item.status }}
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import { computed, defineComponent, ref } from 'vue';
import { router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

interface InventoryItem {
    itemcode: number;
    itemname: string;
    remaining: number;
    reorder_level: number;
    status: 'In Stock' | 'Low Stock' | 'Critical';
}

interface Summary {
    totalItems: number;
    lowStockItems: number;
    totalValue: number;
}

interface Props {
    inventory: InventoryItem[];
    summary: Summary;
    filters?: {
        search?: string;
    };
}

const props = defineProps<Props>();
const inventory = computed(() => props.inventory);
const summary = computed(() => props.summary);
const search = ref(props.filters?.search || '');

const handleSearch = () => {
    const value = search.value.trim();
    router.get('/inventory', value ? { search: value } : {}, {
        preserveState: true,
        replace: true,
    });
};

const formatNumber = (value: number) => {
    return value.toLocaleString();
};

const formatCurrency = (value: number) => {
    if (!value || Number.isNaN(value)) return '—';
    return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD', maximumFractionDigits: 0 }).format(value);
};

const statusClass = (status: InventoryItem['status']) => {
    if (status === 'Critical') {
        return 'bg-red-100 text-red-800';
    }
    if (status === 'Low Stock') {
        return 'bg-orange-100 text-orange-800';
    }
    return 'bg-green-100 text-green-800';
};

const SummaryCard = defineComponent({
    name: 'SummaryCard',
    props: {
        title: { type: String, required: true },
        value: { type: [Number, String], required: true },
        iconColor: { type: String, required: true },
        iconBg: { type: String, required: true },
        iconPath: { type: String, required: true },
    },
    template: `
        <div class="flex items-center justify-between rounded-xl bg-[#f6fbf6] p-5 shadow-sm ring-1 ring-[#cfe8d1]">
            <div>
                <p class="page-subtitle">{{ title }}</p>
                <p class="mt-2 text-3xl font-bold text-gray-900">{{ value }}</p>
            </div>
            <div :class="['flex h-12 w-12 items-center justify-center rounded-xl', iconBg]">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" :class="iconColor">
                    <path stroke-linecap="round" stroke-linejoin="round" :d="iconPath" />
                </svg>
            </div>
        </div>
    `,
});
</script>




