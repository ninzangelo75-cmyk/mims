<template>
    <AppLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Medicine Entry</h2>
                    <p class="text-sm text-gray-600">Add and manage medicine information</p>
                </div>
                <Link
                    href="/items/create"
                    class="inline-flex items-center space-x-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    <span>Add Medicine</span>
                </Link>
            </div>
        </template>

        <div class="py-10">
            <div class="mx-auto max-w-7xl space-y-5 px-0 sm:px-6 lg:px-8">
                <div class="overflow-hidden rounded-2xl bg-white shadow">
                    <div class="border-b border-gray-200 p-5">
                        <div class="relative">
                            <svg class="pointer-events-none absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 104.5 4.5a7.5 7.5 0 0012.15 12.15z" />
                            </svg>
                            <input
                                v-model="search"
                                type="text"
                                placeholder="Search medicines..."
                                class="w-full rounded-lg border border-gray-300 pl-11 pr-4 py-2.5 text-sm shadow-sm transition focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                @input="handleSearch"
                            />
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                        Medicine Name
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                        Brand
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                        Strength
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                        UOM
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                <tr v-if="!items.data.length">
                                    <td colspan="5" class="px-6 py-6 text-center text-sm text-gray-500">
                                        No medicines found.
                                    </td>
                                </tr>
                                <tr v-for="medicine in items.data" :key="medicine.itemcode" class="hover:bg-gray-50">
                                    <td class="whitespace-nowrap px-6 py-4">
                                        <div class="text-sm font-semibold text-gray-900">{{ medicine.itemname }}</div>
                                        <div class="text-xs text-gray-500">{{ medicine.description || '—' }}</div>
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4">
                                        <span
                                            class="inline-flex items-center rounded-full bg-indigo-50 px-3 py-1 text-xs font-medium text-indigo-700 ring-1 ring-indigo-100"
                                        >
                                            {{ medicine.brand || 'Unspecified' }}
                                        </span>
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900">
                                        {{ medicine.strength || '—' }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900">
                                        {{ medicine.default_uom || '—' }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm font-medium">
                                        <Link
                                            :href="`/items/${medicine.itemcode}/edit`"
                                            class="mr-4 text-indigo-600 hover:text-indigo-900"
                                        >
                                            Edit
                                        </Link>
                                        <button
                                            class="text-red-600 hover:text-red-900"
                                            @click="confirmDelete(medicine)"
                                        >
                                            Delete
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div v-if="items.links" class="flex flex-col gap-3 border-t border-gray-100 px-6 py-4 sm:flex-row sm:items-center sm:justify-between">
                        <div class="text-sm text-gray-700">
                            Showing {{ items.from }} to {{ items.to }} of {{ items.total }} results
                        </div>
                        <div class="flex flex-wrap items-center gap-2">
                            <Link
                                v-for="link in items.links"
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

        <Modal :show="showDeleteModal" title="Confirm Delete" @close="showDeleteModal = false">
            <p class="text-sm text-gray-600">
                Are you sure you want to delete "{{ itemToDelete?.itemname }}"? This action cannot be undone.
            </p>
            <template #footer>
                <Button variant="secondary" @click="showDeleteModal = false">Cancel</Button>
                <Button variant="danger" class="ml-3" @click="deleteItem">Delete</Button>
            </template>
        </Modal>
    </AppLayout>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Modal from '@/Components/Modal.vue';
import Button from '@/Components/Button.vue';
import type { Item } from '@/Types';

interface Props {
    items: {
        data: Item[];
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

const search = ref(props.filters?.search || '');
const showDeleteModal = ref(false);
const itemToDelete = ref<Item | null>(null);

const handleSearch = () => {
    router.get('/items', { search: search.value }, {
        preserveState: true,
        replace: true,
    });
};

const confirmDelete = (item: Item) => {
    itemToDelete.value = item;
    showDeleteModal.value = true;
};

const deleteItem = () => {
    if (!itemToDelete.value) return;

    router.delete(`/items/${itemToDelete.value.itemcode}`, {
        onSuccess: () => {
            showDeleteModal.value = false;
            itemToDelete.value = null;
        },
    });
};
</script>

