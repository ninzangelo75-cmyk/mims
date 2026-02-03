<template>
    <AppLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="page-title">Receiving</h2>
                    <p class="page-subtitle">Track incoming medicine shipments</p>
                </div>
                <Link
                    href="/receiving/create"
                    class="btn-primary"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    <span>Receive Stock</span>
                </Link>
            </div>
        </template>

        <div class="py-10">
            <div class="mx-auto max-w-7xl space-y-6 px-0 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                    <SummaryCard
                        title="Pending Receipts"
                        :value="summary.pendingReceipts"
                        icon-color="text-orange-500"
                        icon-bg="bg-orange-50"
                    />
                    <SummaryCard
                        title="Completed Today"
                        :value="summary.completedToday"
                        icon-color="text-green-500"
                        icon-bg="bg-green-50"
                    />
                    <SummaryCard
                        title="Total This Month"
                        :value="summary.totalThisMonth"
                        icon-color="text-indigo-500"
                        icon-bg="bg-indigo-50"
                    />
                </div>

                <div class="overflow-hidden rounded-2xl bg-[#f6fbf6] shadow ring-1 ring-[#cfe8d1]">
                    <div class="border-b border-gray-200 px-6 py-4">
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <h3 class="text-base font-semibold text-gray-900">Recent Receiving Records</h3>
                            <div class="relative w-full sm:w-72">
                                <svg class="pointer-events-none absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 104.5 4.5a7.5 7.5 0 0012.15 12.15z" />
                                </svg>
                                <input
                                    v-model="search"
                                    type="text"
                                    placeholder="Search supplier or batch..."
                                    class="w-full rounded-lg border border-gray-300 pl-11 pr-4 py-2 text-sm shadow-sm transition focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                    @input="handleSearch"
                                />
                            </div>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                        Receipt ID
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                        Supplier
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                        Items
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                        Date
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                        Status
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-[#f6fbf6]">
                                <tr v-if="!receivings.data.length">
                                    <td colspan="5" class="px-6 py-6 text-center text-sm text-gray-500">
                                        No receiving records found.
                                    </td>
                                </tr>
                                <tr v-for="receiving in receivings.data" :key="receiving.recid" class="hover:bg-gray-50">
                                    <td class="whitespace-nowrap px-6 py-4 text-sm font-semibold text-gray-900">
                                        {{ formatReceiptId(receiving.recid) }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900">
                                        {{ receiving.supplier || '—' }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900">
                                        {{ receiving.qty }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900">
                                        {{ formatDate(receiving.datereceived) }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4">
                                        <span
                                            :class="[
                                                'inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold',
                                                getStatus(receiving) === 'Completed'
                                                    ? 'bg-green-100 text-green-800'
                                                    : 'bg-orange-100 text-orange-800'
                                            ]"
                                        >
                                            {{ getStatus(receiving) }}
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div v-if="receivings.links" class="flex flex-col gap-3 border-t border-gray-100 px-6 py-4 sm:flex-row sm:items-center sm:justify-between">
                        <div class="text-sm text-gray-700">
                            Showing {{ receivings.from }} to {{ receivings.to }} of {{ receivings.total }} results
                        </div>
                        <div class="flex flex-wrap items-center gap-2">
                            <Link
                                v-for="link in receivings.links"
                                :key="link.label"
                                :href="link.url || '#'"
                                :class="[
                                    'px-3 py-2 text-sm border rounded transition',
                                    link.active
                                        ? 'bg-indigo-600 text-white border-indigo-600'
                                        : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50',
                                    !link.url ? 'opacity-50 cursor-not-allowed' : ''
                                ]"
                                v-html="link.label"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import { ref, defineComponent } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import type { Receiving } from '@/Types';

interface Summary {
    pendingReceipts: number;
    completedToday: number;
    totalThisMonth: number;
}

interface Props {
    receivings: {
        data: Receiving[];
        links?: any[];
        from?: number;
        to?: number;
        total?: number;
    };
    summary: Summary;
    filters?: {
        search?: string;
    };
}

const props = defineProps<Props>();

const search = ref(props.filters?.search || '');
const summary = props.summary;

const handleSearch = () => {
    router.get('/receiving', { search: search.value }, {
        preserveState: true,
        replace: true,
    });
};

const formatDate = (value: string | null) => {
    if (!value) return '—';
    return new Date(value).toLocaleDateString();
};

const formatReceiptId = (id: number | string) => {
    const padded = String(id).padStart(4, '0');
    return `#RCV${padded}`;
};

const getStatus = (receiving: Receiving & { datereceived?: string }) => {
    if (!receiving.datereceived) return 'Pending';
    const receivedDate = new Date(receiving.datereceived);
    const today = new Date();
    return receivedDate > today ? 'Pending' : 'Completed';
};

const SummaryCard = defineComponent({
    name: 'SummaryCard',
    props: {
        title: { type: String, required: true },
        value: { type: [Number, String], required: true },
        iconColor: { type: String, required: true },
        iconBg: { type: String, required: true },
    },
    template: `
        <div class="flex items-center justify-between rounded-xl bg-[#f6fbf6] p-5 shadow-sm ring-1 ring-[#cfe8d1]">
            <div>
                <p class="page-subtitle">{{ title }}</p>
                <p class="mt-2 text-3xl font-bold text-gray-900">{{ value }}</p>
            </div>
            <div :class="['flex h-12 w-12 items-center justify-center rounded-xl', iconBg]">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" :class="iconColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 7.5l9-4.5 9 4.5m-18 0l9 4.5m-9-4.5V16.5l9 4.5m0-9V21m0-9l9-4.5m0 0v9" />
                </svg>
            </div>
        </div>
    `,
});
</script>




