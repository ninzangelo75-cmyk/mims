<template>
    <AppLayout>
        <div class="py-6">
            <div class="mx-auto max-w-7xl space-y-4 px-0 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="page-title">PTR Request</h2>
                        <p class="page-subtitle">Submit and track property transfer requests</p>
                    </div>
                    <Link href="/requests/ptr/create" class="btn-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        <span>New PTR Request</span>
                    </Link>
                </div>

                <div class="overflow-hidden rounded-2xl bg-[#f6fbf6] shadow ring-1 ring-[#cfe8d1]">
                    <div class="border-b border-gray-200 px-6 py-4">
                        <div class="flex items-center justify-between gap-4">
                            <h3 class="text-base font-semibold text-gray-900">PTR Request History</h3>
                            <div class="relative w-full max-w-xs">
                                <svg class="pointer-events-none absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 104.5 4.5a7.5 7.5 0 0012.15 12.15z" />
                                </svg>
                                <input
                                    v-model="search"
                                    type="text"
                                    placeholder="Search PTR requests..."
                                    class="w-full rounded-lg border border-gray-300 pl-11 pr-4 py-2.5 text-sm shadow-sm transition focus:border-[#2e7d32] focus:outline-none focus:ring-2 focus:ring-[#2e7d32]"
                                    @input="handleSearch"
                                />
                            </div>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-[#e8f5e9]">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                        PTR No
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                        Medicine
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                        Quantity
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                        Type
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                        Request Date
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-[#f6fbf6]">
                                <tr v-if="!requests.data.length">
                                    <td colspan="5" class="px-6 py-6 text-center text-sm text-gray-500">
                                        No PTR requests found.
                                    </td>
                                </tr>
                                <tr v-for="request in requests.data" :key="request.req_ptr" class="hover:bg-[#e8f5e9]">
                                    <td class="whitespace-nowrap px-6 py-4 text-sm font-semibold text-gray-900">
                                        {{ request.ptr_no }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900 uppercase">
                                        {{ request.itemname }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900">
                                        {{ request.req_qty }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4">
                                        <span class="inline-flex items-center rounded-full bg-[#e8f5e9] px-2.5 py-1 text-xs font-semibold text-[#1b5e20]">
                                            {{ request.trans_type }}
                                        </span>
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900">
                                        {{ formatDate(request.requestedat) }}
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
    </AppLayout>
</template>

<script setup lang="ts">
import { Link, router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';

interface Props {
    requests: {
        data: any[];
        links?: any[];
        from?: number;
        to?: number;
        total?: number;
    };
    filters?: {
        search?: string;
    };
}

const props = defineProps<Props>();
const requests = computed(() => props.requests);
const search = ref(props.filters?.search || '');

const handleSearch = () => {
    const value = search.value.trim();
    router.get('/requests/ptr', value ? { search: value } : {}, {
        preserveState: true,
        replace: true,
    });
};

const formatDate = (value: string | null | undefined) => {
    if (!value) return 'â€”';
    return new Date(value).toLocaleDateString();
};
</script>




