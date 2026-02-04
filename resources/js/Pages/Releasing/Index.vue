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
                        <h3 class="text-lg font-medium mb-4">RIS Requests Pending Approval</h3>
                        <DataTable
                            :columns="risColumns"
                            :data="risRequests"
                            :actions="risActions"
                        >
                            <template #cell-ris_no="{ value }">
                                <span class="font-medium">{{ value }}</span>
                            </template>
                            <template #actions="{ row }">
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
                        <h3 class="text-lg font-medium mb-4">PTR Requests Pending Approval</h3>
                        <DataTable
                            :columns="ptrColumns"
                            :data="ptrRequests"
                            :actions="ptrActions"
                        >
                            <template #cell-ptr_no="{ value }">
                                <span class="font-medium">{{ value }}</span>
                            </template>
                            <template #actions="{ row }">
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
    </AppLayout>
</template>

<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import DataTable from '@/Components/DataTable.vue';

interface Props {
    risRequests: any[];
    ptrRequests: any[];
}

const props = defineProps<Props>();

const risColumns = [
    { key: 'ris_no', label: 'RIS No' },
    { key: 'itemname', label: 'Medicine' },
    { key: 'req_qty', label: 'Quantity' },
    { key: 'requestedat', label: 'Requested At' },
];

const ptrColumns = [
    { key: 'ptr_no', label: 'PTR No' },
    { key: 'itemname', label: 'Medicine' },
    { key: 'req_qty', label: 'Quantity' },
    { key: 'trans_type', label: 'Type' },
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
</script>



