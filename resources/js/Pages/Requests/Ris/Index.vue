<template>
    <AppLayout>
        <div class="py-6">
            <div class="mx-auto max-w-7xl space-y-4 px-0 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="page-title">Request</h2>
                        <p class="page-subtitle">Submit and track medicine requests</p>
                    </div>
                    <button
                        type="button"
                        class="btn-primary"
                        @click="openRequestModal"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        <span>New Request</span>
                    </button>
                </div>
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
                            <thead class="bg-[#e8f5e9]">
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
                                <tr v-for="request in requests.data" :key="request.req_ris" class="hover:bg-[#e8f5e9]">
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
                                        ? 'bg-[#2e7d32] text-white border-[#2e7d32]'
                                        : 'bg-[#e8f5e9] text-[#1b5e20] border-[#cfe8d1] hover:bg-[#dff1e1]',
                                    !link.url ? 'opacity-50 cursor-not-allowed' : ''
                                ]"
                                v-html="link.label"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <Modal :show="showRequestModal" :showFooter="false" @close="closeRequestModal">
            <div class="-mx-6 -mt-6 mb-6 rounded-t-lg bg-blue-600 px-6 py-4 text-white">
                <h3 class="text-lg font-semibold">New Request</h3>
                <p class="text-sm text-white/80">Submit a medicine request.</p>
            </div>
            <form @submit.prevent="submitRequest">
                <div class="space-y-6">
                    <Select
                        id="itemcode"
                        v-model="form.itemcode"
                        label="Medicine"
                        :options="itemOptions"
                        required
                        :error="form.errors.itemcode"
                    />

                    <div class="grid grid-cols-2 gap-4">
                        <Input
                            id="req_qty"
                            v-model="form.req_qty"
                            label="Quantity"
                            type="number"
                            step="0.01"
                            required
                            :error="form.errors.req_qty"
                        />

                        <Input
                            id="division"
                            v-model="form.division"
                            label="Division"
                            :error="form.errors.division"
                        />
                    </div>

                    <Input
                        id="department"
                        v-model="form.department"
                        label="Department"
                        :error="form.errors.department"
                    />

                    <Input
                        id="remarks"
                        v-model="form.remarks"
                        label="Remarks"
                        type="textarea"
                        :error="form.errors.remarks"
                    />
                </div>

                <div class="-mx-6 -mb-6 mt-5 rounded-b-lg bg-blue-600 px-6 py-4 flex items-center justify-end space-x-2 min-h-[56px]">
                    <Button
                        variant="danger"
                        type="button"
                        class="bg-white text-blue-600 hover:bg-blue-50 focus:ring-white h-9"
                        @click="closeRequestModal"
                    >
                        Cancel
                    </Button>
                    <Button
                        variant="secondary"
                        type="submit"
                        class="bg-white text-blue-600 hover:bg-blue-50 focus:ring-white h-9"
                        :disabled="form.processing"
                    >
                        Submit Request
                    </Button>
                </div>
            </form>
        </Modal>
    </AppLayout>
</template>

<script setup lang="ts">
import { defineComponent, computed, ref } from 'vue';
import { Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Modal from '@/Components/Modal.vue';
import Input from '@/Components/Input.vue';
import Select from '@/Components/Select.vue';
import Button from '@/Components/Button.vue';
import type { Item } from '@/Types';

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
    items: Item[];
}

const props = defineProps<Props>();
const { requests, summary } = props;

const showRequestModal = ref(false);

const itemOptions = computed(() => {
    return props.items.map(item => ({
        value: item.itemcode,
        label: item.itemname,
    }));
});

const form = useForm({
    itemcode: '',
    req_qty: '',
    division: '',
    department: '',
    remarks: '',
});

const openRequestModal = () => {
    showRequestModal.value = true;
};

const closeRequestModal = () => {
    showRequestModal.value = false;
};

const submitRequest = () => {
    form.post('/requests/ris', {
        onSuccess: () => {
            showRequestModal.value = false;
            form.reset();
        },
    });
};

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






