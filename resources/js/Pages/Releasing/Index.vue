<template>
    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Approval Queue
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
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
                        </DataTable>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
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
import Button from '@/Components/Button.vue';

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

const risActions = [
    {
        label: 'Approve',
        onClick: (row: any) => {
            router.post(`/approvals/ris/${row.req_ris}/approve`);
        },
    },
    {
        label: 'Release',
        onClick: (row: any) => {
            router.visit(`/releasing/ris/${row.req_ris}`);
        },
    },
];

const ptrActions = [
    {
        label: 'Approve',
        onClick: (row: any) => {
            router.post(`/approvals/ptr/${row.req_ptr}/approve`);
        },
    },
];
</script>

