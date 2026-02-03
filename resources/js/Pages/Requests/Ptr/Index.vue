<template>
    <AppLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    PTR Requests
                </h2>
                <Link
                    href="/requests/ptr/create"
                    class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700"
                >
                    New PTR Request
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <DataTable
                            :columns="columns"
                            :data="requests.data"
                        >
                            <template #cell-ptr_no="{ value }">
                                <span class="font-medium">{{ value }}</span>
                            </template>
                            <template #cell-trans_type="{ value }">
                                <Badge>{{ value }}</Badge>
                            </template>
                        </DataTable>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import DataTable from '@/Components/DataTable.vue';
import Badge from '@/Components/Badge.vue';

interface Props {
    requests: {
        data: any[];
        links?: any[];
    };
}

const props = defineProps<Props>();

const columns = [
    { key: 'ptr_no', label: 'PTR No' },
    { key: 'itemname', label: 'Medicine' },
    { key: 'req_qty', label: 'Quantity' },
    { key: 'trans_type', label: 'Type' },
    { key: 'requestedat', label: 'Requested At' },
];
</script>

