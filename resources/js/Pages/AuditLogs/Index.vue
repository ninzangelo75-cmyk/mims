<template>
    <AppLayout>
        <template #header>
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Audit Logs</h2>
                <p class="text-sm text-gray-600">Track all system activities and changes</p>
            </div>
        </template>

        <div class="py-10">
            <div class="mx-auto max-w-7xl space-y-6 px-0 sm:px-6 lg:px-8">
                <!-- Summary cards -->
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                    <SummaryCard
                        title="Total Activities Today"
                        :value="summary.totalToday"
                        icon-color="text-indigo-500"
                        icon-bg="bg-indigo-50"
                    />
                    <SummaryCard
                        title="Successful Actions"
                        :value="summary.successCount"
                        icon-color="text-green-500"
                        icon-bg="bg-green-50"
                    />
                    <SummaryCard
                        title="Failed Actions"
                        :value="summary.failedCount"
                        icon-color="text-red-500"
                        icon-bg="bg-red-50"
                    />
                </div>

                <!-- Activity table -->
                <div class="overflow-hidden rounded-2xl bg-white shadow">
                    <div class="flex items-center justify-between border-b border-gray-200 px-6 py-4">
                        <div>
                            <h3 class="text-base font-semibold text-gray-900">Activity Log</h3>
                        </div>
                        <button
                            type="button"
                            class="inline-flex items-center space-x-2 rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-700 transition hover:bg-gray-50"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 4.5h18M4.5 9.75h15M9 15h6" />
                            </svg>
                            <span>Filter</span>
                        </button>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                        Action
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                        User
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                        Item/Reference
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                        Timestamp
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                        Status
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                <tr v-if="!logs.data.length">
                                    <td colspan="5" class="px-6 py-6 text-center text-sm text-gray-500">
                                        No audit logs found.
                                    </td>
                                </tr>
                                <tr v-for="log in logs.data" :key="log.id" class="hover:bg-gray-50">
                                    <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-900">
                                        {{ formatActionLabel(log.action, log.model_type) }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900">
                                        {{ log.user?.fullname || 'System' }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900">
                                        {{ formatItemReference(log) }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                                        {{ log.created_at }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4">
                                        <span
                                            :class="statusClass(log.action)"
                                            class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold"
                                        >
                                            {{ statusLabel(log.action) }}
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div
                        v-if="logs.links"
                        class="flex flex-col gap-3 border-t border-gray-100 px-6 py-4 sm:flex-row sm:items-center sm:justify-between"
                    >
                        <div class="text-sm text-gray-700">
                            Showing {{ logs.from }} to {{ logs.to }} of {{ logs.total }} results
                        </div>
                        <div class="flex flex-wrap items-center gap-2">
                            <Link
                                v-for="link in logs.links"
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
import { computed, defineComponent } from 'vue';
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

interface LogUser {
    useid: number;
    fullname: string;
}

interface AuditLog {
    id: number;
    created_at: string;
    user: LogUser | null;
    action: string;
    model_type: string;
    model_id: number | string | null;
    old_values?: Record<string, unknown> | null;
    new_values?: Record<string, unknown> | null;
}

interface PaginatedLogs {
    data: AuditLog[];
    links?: any[];
    from?: number;
    to?: number;
    total?: number;
}

interface Summary {
    totalToday: number;
    successCount: number;
    failedCount: number;
}

interface Props {
    logs: PaginatedLogs;
}

const props = defineProps<Props>();
const logs = props.logs;

const summary = computed<Summary>(() => {
    const today = new Date().toISOString().slice(0, 10);
    let totalToday = 0;
    let successCount = 0;
    let failedCount = 0;

    logs.data.forEach((log) => {
        if (log.created_at.startsWith(today)) {
            totalToday += 1;
        }
        if (log.action === 'ERROR' || log.action === 'FAILED') {
            failedCount += 1;
        } else {
            successCount += 1;
        }
    });

    return { totalToday, successCount, failedCount };
});

const formatActionLabel = (action: string, modelType: string) => {
    return `${action} ${modelType}`;
};

const formatItemReference = (log: AuditLog) => {
    if (log.model_id) {
        return `${log.model_type} #${log.model_id}`;
    }
    return log.model_type;
};

const statusLabel = (action: string) => {
    return action === 'ERROR' || action === 'FAILED' ? 'Failed' : 'Success';
};

const statusClass = (action: string) => {
    return action === 'ERROR' || action === 'FAILED'
        ? 'bg-red-100 text-red-800'
        : 'bg-green-100 text-green-800';
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
        <div class="flex items-center justify-between rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-100">
            <div>
                <p class="text-sm text-gray-600">{{ title }}</p>
                <p class="mt-2 text-3xl font-bold text-gray-900">{{ value }}</p>
            </div>
            <div :class="['flex h-12 w-12 items-center justify-center rounded-xl', iconBg]">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" :class="iconColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 4.5h18M4.5 9.75h15M9 15h6" />
                </svg>
            </div>
        </div>
    `,
});
</script>
