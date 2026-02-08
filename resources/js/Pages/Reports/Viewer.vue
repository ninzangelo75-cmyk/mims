<template>
    <AppLayout>
        <div class="py-6">
            <div class="mx-auto max-w-7xl space-y-6 px-0 sm:px-6 lg:px-8">
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div>
                        <h2 class="page-title">{{ title }}</h2>
                        <p class="page-subtitle">Filtered view of report results</p>
                        <div class="mt-3 flex flex-wrap gap-2">
                            <span v-for="chip in filterChips" :key="chip" class="rounded-full bg-[#e8f5e9] px-3 py-1 text-xs font-medium text-[#1b5e20]">
                                {{ chip }}
                            </span>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="button" class="btn-secondary">Export PDF</button>
                        <button type="button" class="btn-secondary">Export Excel</button>
                        <button type="button" class="btn-primary">Print</button>
                    </div>
                </div>

                <ReportFilters />

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    <div v-for="kpi in kpis" :key="kpi.label" class="rounded-xl border border-[#cfe8d1] bg-white p-4 shadow-sm">
                        <p class="text-xs font-semibold text-gray-500">{{ kpi.label }}</p>
                        <p class="mt-2 text-2xl font-semibold text-gray-900">{{ kpi.value }}</p>
                        <p v-if="kpi.note" class="mt-1 text-xs text-gray-500">{{ kpi.note }}</p>
                    </div>
                </div>

                <div class="rounded-2xl border border-[#cfe8d1] bg-white shadow-sm">
                    <div class="flex items-center justify-between border-b border-gray-100 px-6 py-4">
                        <div class="flex gap-3">
                            <button
                                v-for="tab in tabs"
                                :key="tab"
                                type="button"
                                :class="tab === activeTab ? 'bg-[#2e7d32] text-white' : 'bg-[#e8f5e9] text-[#1b5e20]'"
                                class="rounded-lg px-3 py-2 text-xs font-semibold transition"
                                @click="activeTab = tab"
                            >
                                {{ tab }}
                            </button>
                        </div>
                    </div>
                    <div class="p-6">
                        <div v-if="activeTab === 'Table View'" class="text-sm text-gray-600">
                            Table view goes here.
                        </div>
                        <div v-else class="text-sm text-gray-600">
                            Chart view goes here.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import ReportFilters from '@/Components/ReportFilters.vue';

const title = 'Report Viewer';
const filterChips = [
    'Date: Jan 1, 2026 - Feb 8, 2026',
    'Status: All',
    'Warehouse: Main',
];

const kpis = [
    { label: 'Total Received Qty', value: '1,240' },
    { label: 'Total Issued Qty', value: '980' },
    { label: 'Pending RIS/PTR', value: '14' },
    { label: 'Avg Approval Time', value: '2.4 hrs' },
    { label: 'Expiring (30 days)', value: '6' },
    { label: 'Current Stock Value', value: '₱1.2M', note: 'Optional' },
];

const tabs = ['Table View', 'Chart View'];
const activeTab = ref('Table View');
</script>
