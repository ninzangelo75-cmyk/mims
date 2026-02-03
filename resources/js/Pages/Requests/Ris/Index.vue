<template>
    <AppLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="page-title">Request</h2>
                    <p class="page-subtitle">Submit and track medicine requests</p>
                </div>
                <Link
                    href="/requests/ris/create"
                    class="btn-primary"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    <span>New Request</span>
                </Link>
            </div>
        </template>

        <div class="py-10">
            <div class="mx-auto max-w-7xl space-y-6 px-0 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                    <SummaryCard
                        title="Pending Requests"
                        :value="summary.pending"
                        icon-color="text-orange-500"
                        icon-bg="bg-orange-50"
                    />
                    <SummaryCard
                        title="Approved Today"
                        :value="summary.approvedToday"
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
                        <h3 class="text-base font-semibold text-gray-900">Request History</h3>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                        Request ID
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                        Medicine
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                        Quantity
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                        Requested By
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
                                <tr v-if="!requests.data.length">
                                    <td colspan="6" class="px-6 py-6 text-center text-sm text-gray-500">
                                        No requests found.
                                    </td>
                                </tr>
                                <tr v-for="request in requests.data" :key="request.req_ris" class="hover:bg-gray-50">
                                    <td class="whitespace-nowrap px-6 py-4 text-sm font-semibold text-gray-900">
                                        {{ formatReqId(request.req_ris) }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900">
                                        {{ request.itemname }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900">
                                        {{ request.req_qty }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900">
                                        {{ request.requested_by }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900">
                                        {{ formatDate(request.requestedat) }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4">
                                        <span :class="statusClass(request.isavailable)" class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold">
                                            {{ statusLabel(request.isavailable) }}
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div v-if="requests.links" class="flex flex-col gap-3 border-t border-gray-100 px-6 py-4 sm:flex-row sm:items-center sm:justify-between">
                        <div class="text-sm text-gray-700">
                            Showing {{ requests.from }} to {{ requests.to }} of {{ requests.total }} results
                        </div>
                        <div class="flex flex-wrap items-center gap-2">
                            <Link
                                v-for="link in requests.links"
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
import { defineComponent } from 'vue';
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

interface RequestItem {
    req_ris: number;
    ris_no: string;
    itemname: string;
    req_qty: number;
    requested_by: string;
    requestedat: string | null;
    isavailable: boolean;
}

interface Summary {
    pending: number;
    approvedToday: number;
    totalThisMonth: number;
}

interface Props {
    requests: {
        data: RequestItem[];
        links?: any[];
        from?: number;
        to?: number;
        total?: number;
    };
    summary: Summary;
}

const props = defineProps<Props>();
const { requests, summary } = props;

const formatReqId = (id: number | string) => {
    const padded = String(id).padStart(4, '0');
    return `#REQ${padded}`;
};

const formatDate = (value: string | null) => {
    if (!value) return '—';
    return new Date(value).toLocaleDateString();
};

const statusLabel = (isAvailable: boolean) => (isAvailable ? 'Approved' : 'Pending');

const statusClass = (isAvailable: boolean) => {
    return isAvailable ? 'bg-green-100 text-green-800' : 'bg-orange-100 text-orange-800';
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
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
            </div>
        </div>
    `,
});
</script>




