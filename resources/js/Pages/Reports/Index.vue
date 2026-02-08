<template>
    <AppLayout>
        <div class="py-6">
            <div class="mx-auto max-w-7xl space-y-4 px-0 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="page-title">Reports</h2>
                        <p class="page-subtitle">Report catalog for your modules</p>
                    </div>
                </div>

                <div class="space-y-6">
                    <div v-for="section in reportSections" :key="section.title" class="rounded-2xl shadow-sm border border-[#cfe8d1] bg-[#f6fbf6] p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">{{ section.title }}</h3>
                                <p class="text-sm text-gray-600">{{ section.subtitle }}</p>
                            </div>
                        </div>
                        <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                            <button
                                v-for="card in section.cards"
                                :key="card.title"
                                type="button"
                                class="group rounded-xl border border-[#cfe8d1] bg-white p-4 text-left shadow-sm transition hover:-translate-y-0.5 hover:shadow-md"
                            >
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <p class="text-sm font-semibold text-gray-900">{{ card.title }}</p>
                                        <p class="mt-1 text-xs text-gray-600">{{ card.description }}</p>
                                    </div>
                                    <span class="inline-flex h-7 w-7 items-center justify-center rounded-lg bg-[#e8f5e9] text-[#1b5e20] text-xs font-semibold">
                                        {{ card.code }}
                                    </span>
                                </div>
                                <p class="mt-3 text-xs text-gray-500">Open report</p>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import AppLayout from '@/Layouts/AppLayout.vue';

type ReportCard = {
    code: string;
    title: string;
    description: string;
};

type ReportSection = {
    title: string;
    subtitle: string;
    cards: ReportCard[];
};

const reportSections: ReportSection[] = [
    {
        title: 'Medicines',
        subtitle: 'Master data and change tracking',
        cards: [
            { code: 'A1', title: 'Medicine Masterlist', description: 'All medicines with status (active/inactive).' },
            { code: 'A2', title: 'Medicine Price/Cost List', description: 'Pricing and cost list (if stored).' },
            { code: 'A3', title: 'Medicine Entry Changes', description: 'Audit of edits with user and timestamp.' },
        ],
    },
    {
        title: 'Receiving',
        subtitle: 'Inbound stock and variance checks',
        cards: [
            { code: 'B1', title: 'Receiving Summary', description: 'By date range, supplier, receipt no.' },
            { code: 'B2', title: 'Receiving Details', description: 'Per item, batch/lot, expiry, qty received.' },
            { code: 'B3', title: 'Receiving Variance', description: 'Ordered vs received (if PO is tracked).' },
        ],
    },
    {
        title: 'Inventory',
        subtitle: 'Stock status, movement, and health',
        cards: [
            { code: 'C1', title: 'Stock on Hand', description: 'Current available quantities.' },
            { code: 'C2', title: 'Stock Card / Item Ledger', description: 'Movement history per item.' },
            { code: 'C3', title: 'Expiring Items', description: '30/60/90 day buckets.' },
            { code: 'C4', title: 'Expired Items', description: 'All expired items with dates.' },
            { code: 'C5', title: 'Low Stock / Reorder List', description: 'Below reorder level.' },
            { code: 'C6', title: 'Fast / Slow Moving', description: 'Based on issues/usage.' },
        ],
    },
    {
        title: 'RIS & PTR Workflow',
        subtitle: 'Request performance and issuance',
        cards: [
            { code: 'D1', title: 'RIS Requests Summary', description: 'By status: Pending/Approved/Issued/Rejected/Cancelled.' },
            { code: 'D2', title: 'RIS Requests Aging', description: 'How long requests stay pending.' },
            { code: 'D3', title: 'PTR Requests Summary', description: 'By status: Pending/Approved/Issued/Rejected/Cancelled.' },
            { code: 'D4', title: 'Approval Queue Performance', description: 'Approvals by approver and average time.' },
            { code: 'D5', title: 'Issuance Summary', description: 'Items issued tied to RIS/PTR.' },
        ],
    },
    {
        title: 'User Management / Audit',
        subtitle: 'Accounts, activity, and exports',
        cards: [
            { code: 'E1', title: 'User List & Roles', description: 'All users with roles and status.' },
            { code: 'E2', title: 'User Activity Log', description: 'Login and CRUD/approval activity.' },
            { code: 'E3', title: 'Export Log', description: 'Who exported which report.' },
        ],
    },
];
</script>




