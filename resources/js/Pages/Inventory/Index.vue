<template>
    <AppLayout>
        <template #header>
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Inventory</h2>
                <p class="text-sm text-gray-600">Monitor medicine stock levels</p>
            </div>
        </template>

        <div class="py-10">
            <div class="mx-auto max-w-7xl space-y-6 px-0 sm:px-6 lg:px-8">
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

                <div class="overflow-hidden rounded-2xl bg-white shadow">
                    <div class="border-b border-gray-200 px-6 py-4">
                        <h3 class="text-base font-semibold text-gray-900">Current Inventory Status</h3>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
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
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                <tr v-if="!inventory.length">
                                    <td colspan="5" class="px-6 py-6 text-center text-sm text-gray-500">
                                        No inventory data found.
                                    </td>
                                </tr>
                                <tr v-for="item in inventory" :key="item.itemcode" class="hover:bg-gray-50">
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
                                    <td class="whitespace-nowrap px-6 py-4 text-sm font-medium">
                                        <Link :href="`/requests/ris/create?item=${item.itemcode}`" class="text-indigo-600 hover:text-indigo-900">
                                            Reorder
                                        </Link>
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
import { defineComponent } from 'vue';
import { Link } from '@inertiajs/vue3';
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
}

const props = defineProps<Props>();
const { inventory, summary } = props;

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
        <div class="flex items-center justify-between rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-100">
            <div>
                <p class="text-sm text-gray-600">{{ title }}</p>
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
