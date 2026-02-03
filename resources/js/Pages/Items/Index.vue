<template>
    <AppLayout>
        <div class="py-6">
            <div class="mx-auto max-w-7xl space-y-5 px-0 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="page-title">Medicine Entry</h2>
                        <p class="page-subtitle">Add and manage medicine information</p>
                    </div>
                    <button type="button" class="btn-primary" @click="openCreateModal">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        <span>Add Medicine</span>
                    </button>
                </div>
                <div class="overflow-hidden rounded-2xl bg-[#f6fbf6] shadow ring-1 ring-[#cfe8d1]">
                    <div class="border-b border-gray-200 p-5">
                        <div class="relative">
                            <svg class="pointer-events-none absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 104.5 4.5a7.5 7.5 0 0012.15 12.15z" />
                            </svg>
                            <input
                                v-model="search"
                                type="text"
                                placeholder="Search medicines..."
                                class="w-full rounded-lg border border-gray-300 pl-11 pr-4 py-2.5 text-sm shadow-sm transition focus:border-[#2e7d32] focus:outline-none focus:ring-2 focus:ring-[#2e7d32]"
                                @input="handleSearch"
                            />
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-[#e8f5e9]">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 whitespace-nowrap">
                                        Code
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 whitespace-nowrap">
                                        Medicine Name
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 whitespace-nowrap">
                                        Brand
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 whitespace-nowrap">
                                        Dosage Form
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 whitespace-nowrap">
                                        Strength
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 whitespace-nowrap">
                                        UOM
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 whitespace-nowrap">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-[#f6fbf6]">
                                <tr v-if="!items.data.length">
                                    <td colspan="8" class="px-6 py-6 text-center text-sm text-gray-500">
                                        No medicines found.
                                    </td>
                                </tr>
                                <tr v-for="medicine in items.data" :key="medicine.itemcode" class="hover:bg-gray-50">
                                    <td class="whitespace-nowrap px-6 py-2 text-sm text-gray-900">
                                        {{ formatItemCode(medicine.itemcode) }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-2">
                                        <div class="text-sm font-semibold text-gray-900 truncate max-w-[180px] uppercase">{{ medicine.itemname }}</div>
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-2">
                                        <span
                                            class="inline-flex items-center rounded-full bg-[#e8f5e9] px-3 py-1 text-xs font-medium text-[#1b5e20] ring-1 ring-[#e8f5e9]"
                                        >
                                            {{ medicine.brand || 'Unspecified' }}
                                        </span>
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-2 text-sm text-gray-900">
                                        {{ medicine.dosage_form || '-' }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-2 text-sm text-gray-900">
                                        {{ medicine.strength || '-' }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-2 text-sm text-gray-900">
                                        {{ medicine.default_uom || '-' }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-2 text-sm font-medium">
                                        <button
                                            type="button"
                                            class="mr-1 inline-flex h-8 w-8 items-center justify-center rounded-md bg-gray-500 text-white shadow-sm transition hover:bg-gray-600"
                                            @click="openViewModal(medicine)"
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
                                            @click="openEditModal(medicine)"
                                        >
                                            <span class="sr-only">Edit</span>
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 3.487a1.875 1.875 0 012.651 2.651L7.5 18.15 3 19.5l1.35-4.5L16.862 3.487z" />
                                            </svg>
                                        </button>
                                        <button
                                            class="inline-flex h-8 w-8 items-center justify-center rounded-md bg-red-600 text-white shadow-sm transition hover:bg-red-700"
                                            @click="confirmDelete(medicine)"
                                        >
                                            <span class="sr-only">Delete</span>
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 7h12m-9 0V5a1 1 0 011-1h4a1 1 0 011 1v2m-8 0l.8 12.1A2 2 0 008.8 21h6.4a2 2 0 001.99-1.9L18 7" />
                                            </svg>
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

        <Modal :show="showDeleteModal" :showFooter="false" @close="showDeleteModal = false">
            <div class="-mx-6 -mt-6 mb-6 rounded-t-lg bg-red-600 px-6 py-4 text-white">
                <h3 class="text-lg font-semibold">Confirm Delete</h3>
            </div>
            <p class="text-sm text-gray-700">
                Are you sure you want to delete "{{ itemToDelete?.itemname }}"? This action cannot be undone.
            </p>
            <div class="-mx-6 -mb-6 mt-5 rounded-b-lg bg-red-600 px-6 py-4 flex items-center justify-end space-x-2 min-h-[56px]">
                <Button
                    variant="secondary"
                    class="bg-white text-red-600 hover:bg-red-50 focus:ring-white h-9"
                    @click="showDeleteModal = false"
                >
                    Cancel
                </Button>
                <Button
                    variant="danger"
                    class="bg-white text-red-600 hover:bg-red-50 focus:ring-white h-9"
                    @click="deleteItem"
                >
                    Delete
                </Button>
            </div>
        </Modal>

        <Modal :show="showViewModal" :showFooter="false" @close="closeViewModal">
            <div class="-mx-6 -mt-6 mb-6 rounded-t-lg bg-gray-600 px-6 py-4 text-white">
                <h3 class="text-lg font-semibold">Medicine Information</h3>
            </div>
            <div class="space-y-3 text-sm text-gray-700">
                <div><span class="font-semibold text-[#1b5e20]">Code:</span> {{ viewItem ? formatItemCode(viewItem.itemcode) : '-' }}</div>
                <div><span class="font-semibold text-[#1b5e20]">Name:</span> {{ viewItem ? viewItem.itemname.toUpperCase() : '-' }}</div>
                <div><span class="font-semibold text-[#1b5e20]">Description:</span> {{ viewItem?.description || '-' }}</div>
                <div><span class="font-semibold text-[#1b5e20]">Brand:</span> {{ viewItem?.brand || '-' }}</div>
                <div><span class="font-semibold text-[#1b5e20]">Dosage Form:</span> {{ viewItem?.dosage_form || '-' }}</div>
                <div><span class="font-semibold text-[#1b5e20]">Strength:</span> {{ viewItem?.strength || '-' }}</div>
                <div><span class="font-semibold text-[#1b5e20]">UOM:</span> {{ viewItem?.default_uom || '-' }}</div>
            </div>
            <div class="-mx-6 -mb-6 mt-5 rounded-b-lg bg-gray-600 px-6 py-4 flex items-center justify-end space-x-2 min-h-[56px]">
                <Button
                    variant="secondary"
                    class="bg-white text-gray-600 hover:bg-gray-50 focus:ring-white h-9"
                    @click="closeViewModal"
                >
                    Close
                </Button>
                <Button
                    variant="secondary"
                    class="bg-white text-gray-600 hover:bg-gray-50 focus:ring-white h-9"
                    @click="closeViewModal"
                >
                    Done
                </Button>
            </div>
        </Modal>

        <Modal
            :show="showItemModal"
            :showFooter="false"
            @close="closeItemModal"
        >
            <div
                class="-mx-6 -mt-6 mb-6 rounded-t-lg px-6 py-4 text-white"
                :class="isEditMode ? 'bg-blue-600' : 'bg-[#2e7d32]'"
            >
                <h3 class="text-lg font-semibold">
                    {{ isEditMode ? 'Edit Medicine' : 'Add Medicine' }}
                </h3>
                <p class="text-sm text-white/80">
                    {{ isEditMode ? 'Update the medicine details below.' : 'Fill in the medicine details to add a new item.' }}
                </p>
            </div>
            <form @submit.prevent="submitItem">
                <div class="space-y-5">
                    <Input
                        id="itemname"
                        v-model="itemForm.itemname"
                        label="Medicine Name"
                        required
                        :error="itemForm.errors.itemname"
                    />

                    <Input
                        id="description"
                        v-model="itemForm.description"
                        label="Description"
                        type="textarea"
                        :error="itemForm.errors.description"
                    />

                    <div class="grid grid-cols-2 gap-4">
                        <Input
                            id="brand"
                            v-model="itemForm.brand"
                            label="Brand"
                            :error="itemForm.errors.brand"
                        />

                        <Input
                            id="dosage_form"
                            v-model="itemForm.dosage_form"
                            label="Dosage Form"
                            :error="itemForm.errors.dosage_form"
                        />
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <Input
                            id="strength"
                            v-model="itemForm.strength"
                            label="Strength"
                            :error="itemForm.errors.strength"
                        />

                        <Input
                            id="default_uom"
                            v-model="itemForm.default_uom"
                            label="Default UOM"
                            :error="itemForm.errors.default_uom"
                        />
                    </div>
                </div>

                <div class="-mx-6 -mb-6 mt-4 rounded-b-lg bg-blue-600 px-6 py-4 flex items-center justify-end space-x-2 min-h-[56px]">
                    <Button
                        variant="danger"
                        type="button"
                        class="bg-white text-blue-600 hover:bg-blue-50 focus:ring-white h-9"
                        @click="closeItemModal"
                    >
                        Cancel
                    </Button>
                    <Button
                        variant="secondary"
                        type="submit"
                        class="bg-white text-blue-600 hover:bg-blue-50 focus:ring-white h-9"
                        :disabled="itemForm.processing"
                    >
                        {{ isEditMode ? 'Update Medicine' : 'Create Medicine' }}
                    </Button>
                </div>
            </form>
        </Modal>
    </AppLayout>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { Link, router, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Modal from '@/Components/Modal.vue';
import Button from '@/Components/Button.vue';
import Input from '@/Components/Input.vue';
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
const showViewModal = ref(false);
const viewItem = ref<Item | null>(null);
const showItemModal = ref(false);
const isEditMode = ref(false);
const editingItem = ref<Item | null>(null);

const itemForm = useForm({
    itemname: '',
    description: '',
    brand: '',
    dosage_form: '',
    strength: '',
    default_uom: '',
});

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

const openViewModal = (item: Item) => {
    viewItem.value = item;
    showViewModal.value = true;
};

const closeViewModal = () => {
    showViewModal.value = false;
    viewItem.value = null;
};

const openCreateModal = () => {
    isEditMode.value = false;
    editingItem.value = null;
    itemForm.reset();
    itemForm.clearErrors();
    showItemModal.value = true;
};

const openEditModal = (item: Item) => {
    isEditMode.value = true;
    editingItem.value = item;
    itemForm.itemname = item.itemname;
    itemForm.description = item.description || '';
    itemForm.brand = item.brand || '';
    itemForm.dosage_form = item.dosage_form || '';
    itemForm.strength = item.strength || '';
    itemForm.default_uom = item.default_uom || '';
    itemForm.clearErrors();
    showItemModal.value = true;
};

const closeItemModal = () => {
    showItemModal.value = false;
};

const submitItem = () => {
    if (isEditMode.value && editingItem.value) {
        itemForm.put(`/items/${editingItem.value.itemcode}`, {
            onSuccess: () => {
                showItemModal.value = false;
            },
        });
        return;
    }

    itemForm.post('/items', {
        onSuccess: () => {
            showItemModal.value = false;
        },
    });
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

const formatItemCode = (code: number) => {
    return `MED-${String(code).padStart(6, '0')}`;
};
</script>
