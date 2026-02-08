<template>
    <AppLayout>
        <div class="py-6">
            <div class="mx-auto max-w-7xl space-y-4 px-0 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="page-title">Approval Queue</h2>
                        <p class="page-subtitle">Review and approve pending requests</p>
                    </div>
                </div>
                <div class="bg-[#f6fbf6] overflow-hidden shadow-sm sm:rounded-lg ring-1 ring-[#cfe8d1] mb-6">
                    <div class="p-6">
                        <div class="flex items-center justify-between gap-4 mb-4">
                            <h3 class="text-lg font-medium">RIS Requests Pending Approval</h3>
                            <div class="relative w-full max-w-xs">
                                <svg class="pointer-events-none absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 104.5 4.5a7.5 7.5 0 0012.15 12.15z" />
                                </svg>
                                <input
                                    v-model="risSearch"
                                    type="text"
                                    placeholder="Search RIS approvals..."
                                    class="w-full rounded-lg border border-gray-300 pl-11 pr-4 py-2.5 text-sm shadow-sm transition focus:border-[#2e7d32] focus:outline-none focus:ring-2 focus:ring-[#2e7d32]"
                                    @input="handleRisSearch"
                                />
                            </div>
                        </div>
                        <DataTable
                            :columns="risColumns"
                            :data="risRequests"
                            :actions="risActions"
                        >
                            <template #cell-request_no="{ value }">
                                <span class="font-medium">{{ value }}</span>
                            </template>
                            <template #cell-status="{ value }">
                                <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-semibold"
                                    :class="value === 'Pending' ? 'bg-amber-100 text-amber-700' : 'bg-emerald-100 text-emerald-700'">
                                    {{ value }}
                                </span>
                            </template>
                            <template #actions="{ row }">
                                <button
                                    type="button"
                                    class="mr-1 inline-flex h-8 w-8 items-center justify-center rounded-md bg-gray-500 text-white shadow-sm transition hover:bg-gray-600"
                                    @click="openViewModal(row, 'RIS')"
                                >
                                    <span class="sr-only">View</span>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12s3.75-7.5 9.75-7.5S21.75 12 21.75 12s-3.75 7.5-9.75 7.5S2.25 12 2.25 12z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                </button>
                                <button
                                    type="button"
                                    class="mr-1 inline-flex h-8 w-8 items-center justify-center rounded-md bg-blue-600 text-white shadow-sm transition hover:bg-blue-700"
                                    @click="approveRis(row)"
                                >
                                    <span class="sr-only">Approve</span>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                    </svg>
                                </button>
                                <button
                                    type="button"
                                    class="inline-flex h-8 w-8 items-center justify-center rounded-md bg-gray-500 text-white shadow-sm transition hover:bg-gray-600"
                                    @click="releaseRis(row)"
                                >
                                    <span class="sr-only">Release</span>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m6-6H6" />
                                    </svg>
                                </button>
                            </template>
                        </DataTable>
                    </div>
                </div>

                <div class="bg-[#f6fbf6] overflow-hidden shadow-sm sm:rounded-lg ring-1 ring-[#cfe8d1]">
                    <div class="p-6">
                        <div class="flex items-center justify-between gap-4 mb-4">
                            <h3 class="text-lg font-medium">PTR Requests Pending Approval</h3>
                            <div class="relative w-full max-w-xs">
                                <svg class="pointer-events-none absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 104.5 4.5a7.5 7.5 0 0012.15 12.15z" />
                                </svg>
                                <input
                                    v-model="ptrSearch"
                                    type="text"
                                    placeholder="Search PTR approvals..."
                                    class="w-full rounded-lg border border-gray-300 pl-11 pr-4 py-2.5 text-sm shadow-sm transition focus:border-[#2e7d32] focus:outline-none focus:ring-2 focus:ring-[#2e7d32]"
                                    @input="handlePtrSearch"
                                />
                            </div>
                        </div>
                        <DataTable
                            :columns="ptrColumns"
                            :data="ptrRequests"
                            :actions="ptrActions"
                        >
                            <template #cell-request_no="{ value }">
                                <span class="font-medium">{{ value }}</span>
                            </template>
                            <template #cell-status="{ value }">
                                <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-semibold"
                                    :class="value === 'Pending' ? 'bg-amber-100 text-amber-700' : 'bg-emerald-100 text-emerald-700'">
                                    {{ value }}
                                </span>
                            </template>
                            <template #actions="{ row }">
                                <button
                                    type="button"
                                    class="mr-1 inline-flex h-8 w-8 items-center justify-center rounded-md bg-gray-500 text-white shadow-sm transition hover:bg-gray-600"
                                    @click="openViewModal(row, 'PTR')"
                                >
                                    <span class="sr-only">View</span>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12s3.75-7.5 9.75-7.5S21.75 12 21.75 12s-3.75 7.5-9.75 7.5S2.25 12 2.25 12z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                </button>
                                <button
                                    type="button"
                                    class="inline-flex h-8 w-8 items-center justify-center rounded-md bg-blue-600 text-white shadow-sm transition hover:bg-blue-700"
                                    @click="approvePtr(row)"
                                >
                                    <span class="sr-only">Approve</span>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                    </svg>
                                </button>
                            </template>
                        </DataTable>
                    </div>
                </div>
            </div>
        </div>

        <Modal :show="showViewModal" :showFooter="false" maxWidth="sm:max-w-2xl" @close="closeViewModal">
            <div class="-mx-6 -mt-6 mb-6 rounded-t-lg bg-[#2e7d32] px-6 py-4 text-white">
                <h3 class="text-lg font-semibold">{{ viewType }} Request Details</h3>
                <p class="text-sm text-white/80">Review request information below.</p>
            </div>
            <div v-if="viewRequest" class="grid grid-cols-1 gap-4 text-sm text-gray-700 sm:grid-cols-2">
                <div><span class="font-semibold text-[#1b5e20]">Request ID:</span> {{ viewRequest.request_no }}</div>
                <div><span class="font-semibold text-[#1b5e20]">Item:</span> {{ viewRequest.itemname }}</div>
                <div><span class="font-semibold text-[#1b5e20]">Quantity:</span> {{ viewRequest.req_qty }}</div>
                <div><span class="font-semibold text-[#1b5e20]">Requested By:</span> {{ viewRequest.requested_by }}</div>
                <div><span class="font-semibold text-[#1b5e20]">Department:</span> {{ viewRequest.department }}</div>
                <div><span class="font-semibold text-[#1b5e20]">Request Date:</span> {{ viewRequest.requestedat }}</div>
                <div><span class="font-semibold text-[#1b5e20]">Status:</span> {{ viewRequest.status }}</div>

                <template v-if="viewType === 'RIS'">
                    <div><span class="font-semibold text-[#1b5e20]">Division:</span> {{ viewRequest.division }}</div>
                    <div class="sm:col-span-2"><span class="font-semibold text-[#1b5e20]">Remarks:</span> {{ viewRequest.remarks }}</div>
                </template>

                <template v-else>
                    <div><span class="font-semibold text-[#1b5e20]">Division:</span> {{ viewRequest.division }}</div>
                    <div><span class="font-semibold text-[#1b5e20]">Target:</span> {{ viewRequest.target }}</div>
                    <div><span class="font-semibold text-[#1b5e20]">Transaction Type:</span> {{ viewRequest.trans_type }}</div>
                    <div><span class="font-semibold text-[#1b5e20]">Other Type:</span> {{ viewRequest.trans_type_other }}</div>
                    <div class="sm:col-span-2"><span class="font-semibold text-[#1b5e20]">Purpose:</span> {{ viewRequest.purpose }}</div>
                    <div class="sm:col-span-2"><span class="font-semibold text-[#1b5e20]">Remarks:</span> {{ viewRequest.remarks }}</div>
                </template>
            </div>
            <div class="-mx-6 -mb-6 mt-6 rounded-b-lg bg-[#2e7d32] px-6 py-4 flex items-center justify-end">
                <button
                    type="button"
                    class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-medium text-[#1b5e20] shadow-sm hover:bg-[#e8f5e9]"
                    @click="closeViewModal"
                >
                    Close
                </button>
            </div>
        </Modal>
    </AppLayout>
</template>

<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import { ref } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import DataTable from '@/Components/DataTable.vue';
import Modal from '@/Components/Modal.vue';

interface Props {
    risRequests: any[];
    ptrRequests: any[];
    filters?: {
        ris_search?: string;
        ptr_search?: string;
    };
}

const props = defineProps<Props>();
const risSearch = ref(props.filters?.ris_search || '');
const ptrSearch = ref(props.filters?.ptr_search || '');

const risColumns = [
    { key: 'request_no', label: 'Request ID', headerClass: 'w-32', cellClass: 'w-32' },
    { key: 'itemname', label: 'Item', headerClass: 'w-40', cellClass: 'w-40' },
    { key: 'req_qty', label: 'Quantity', headerClass: 'w-24', cellClass: 'w-24' },
    { key: 'requested_by', label: 'Requested By', headerClass: 'w-40', cellClass: 'w-40' },
    { key: 'department', label: 'Department', headerClass: 'w-40', cellClass: 'w-40' },
    { key: 'requestedat', label: 'Request Date', headerClass: 'w-40', cellClass: 'w-40' },
    { key: 'status', label: 'Status', headerClass: 'w-24', cellClass: 'w-24' },
];

const ptrColumns = [
    { key: 'request_no', label: 'Request ID', headerClass: 'w-32', cellClass: 'w-32' },
    { key: 'itemname', label: 'Item', headerClass: 'w-40', cellClass: 'w-40' },
    { key: 'req_qty', label: 'Quantity', headerClass: 'w-24', cellClass: 'w-24' },
    { key: 'requested_by', label: 'Requested By', headerClass: 'w-40', cellClass: 'w-40' },
    { key: 'department', label: 'Department', headerClass: 'w-40', cellClass: 'w-40' },
    { key: 'requestedat', label: 'Request Date', headerClass: 'w-40', cellClass: 'w-40' },
    { key: 'status', label: 'Status', headerClass: 'w-24', cellClass: 'w-24' },
];

const risActions = [{ label: 'Actions', onClick: () => undefined }];
const ptrActions = [{ label: 'Actions', onClick: () => undefined }];

const approveRis = (row: any) => {
    router.post(`/approvals/ris/${row.req_ris}/approve`);
};

const releaseRis = (row: any) => {
    router.visit(`/releasing/ris/${row.req_ris}`);
};

const approvePtr = (row: any) => {
    router.post(`/approvals/ptr/${row.req_ptr}/approve`);
};

const handleRisSearch = () => {
    const risValue = risSearch.value.trim();
    const ptrValue = ptrSearch.value.trim();
    router.get('/approvals', {
        ...(risValue ? { ris_search: risValue } : {}),
        ...(ptrValue ? { ptr_search: ptrValue } : {}),
    }, {
        preserveState: true,
        replace: true,
    });
};

const handlePtrSearch = () => {
    const risValue = risSearch.value.trim();
    const ptrValue = ptrSearch.value.trim();
    router.get('/approvals', {
        ...(risValue ? { ris_search: risValue } : {}),
        ...(ptrValue ? { ptr_search: ptrValue } : {}),
    }, {
        preserveState: true,
        replace: true,
    });
};

const showViewModal = ref(false);
const viewRequest = ref<any | null>(null);
const viewType = ref<'RIS' | 'PTR'>('RIS');

const openViewModal = (row: any, type: 'RIS' | 'PTR') => {
    viewRequest.value = row;
    viewType.value = type;
    showViewModal.value = true;
};

const closeViewModal = () => {
    showViewModal.value = false;
    viewRequest.value = null;
};
</script>



