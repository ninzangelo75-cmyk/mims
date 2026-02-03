<template>
    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Receive Stock
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-[#f6fbf6] overflow-hidden shadow-sm sm:rounded-lg ring-1 ring-[#cfe8d1]">
                    <form @submit.prevent="submit" class="p-6 space-y-6">
                        <div class="grid grid-cols-2 gap-4">
                            <Input
                                id="supplier"
                                v-model="form.supplier"
                                label="Supplier"
                                :error="form.errors.supplier"
                            />

                            <Input
                                id="referenceno"
                                v-model="form.referenceno"
                                label="Reference Number"
                                :error="form.errors.referenceno"
                            />
                        </div>

                        <Input
                            id="department"
                            v-model="form.department"
                            label="Department"
                            :error="form.errors.department"
                        />

                        <div class="bg-[#e8f5e9] p-4 rounded space-y-4">
                            <p class="text-sm font-semibold text-gray-700">Add Item</p>

                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700">Medicine</label>
                                <div class="flex items-center gap-3">
                                    <div class="flex-1 text-sm text-gray-700">
                                        <span v-if="selectedItem">{{ selectedItem.itemname }}</span>
                                        <span v-else class="text-gray-400">No medicine selected</span>
                                    </div>
                                    <Button type="button" @click="openItemModal">
                                        Select Medicine
                                    </Button>
                                </div>
                                <p v-if="itemErrors.itemcode" class="text-sm text-red-600">{{ itemErrors.itemcode }}</p>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <Input
                                    id="batchno"
                                    v-model="currentItem.batchno"
                                    label="Batch Number"
                                    required
                                    :error="itemErrors.batchno"
                                />

                                <Input
                                    id="expirydate"
                                    v-model="currentItem.expirydate"
                                    label="Expiry Date"
                                    type="date"
                                    required
                                    :error="itemErrors.expirydate"
                                />
                            </div>

                            <div class="grid grid-cols-3 gap-4">
                                <Input
                                    id="qty"
                                    v-model="currentItem.qty"
                                    label="Quantity"
                                    type="number"
                                    step="0.01"
                                    required
                                    :error="itemErrors.qty"
                                />

                                <Input
                                    id="uom"
                                    v-model="currentItem.uom"
                                    label="Unit of Measure"
                                    required
                                    :error="itemErrors.uom"
                                />

                                <Input
                                    id="unitprice"
                                    v-model="currentItem.unitprice"
                                    label="Unit Price"
                                    type="number"
                                    step="0.01"
                                    required
                                    :error="itemErrors.unitprice"
                                />
                            </div>

                            <div class="flex items-center justify-between">
                                <p class="text-sm font-medium text-gray-700">
                                    Line Total:
                                    <span class="text-lg font-bold text-indigo-600">{{ currentTotal.toFixed(2) }}</span>
                                </p>
                                <Button type="button" @click="addItem">
                                    Add Item
                                </Button>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <p class="text-sm font-semibold text-gray-700">Items to Receive</p>
                            <p v-if="form.errors.items" class="text-sm text-red-600">{{ form.errors.items }}</p>

                            <div v-if="form.items.length === 0" class="text-sm text-gray-500">
                                No items added yet.
                            </div>

                            <div v-else class="overflow-x-auto">
                                <table class="min-w-full text-sm">
                                    <thead>
                                        <tr class="text-left text-gray-600">
                                            <th class="py-2 pr-4">Item</th>
                                            <th class="py-2 pr-4">Batch</th>
                                            <th class="py-2 pr-4">Expiry</th>
                                            <th class="py-2 pr-4">Qty</th>
                                            <th class="py-2 pr-4">UoM</th>
                                            <th class="py-2 pr-4">Unit Price</th>
                                            <th class="py-2 pr-4">Total</th>
                                            <th class="py-2 pr-4"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(item, index) in form.items" :key="index" class="border-t">
                                            <td class="py-2 pr-4">{{ itemLabel(item.itemcode) }}</td>
                                            <td class="py-2 pr-4">{{ item.batchno }}</td>
                                            <td class="py-2 pr-4">{{ item.expirydate }}</td>
                                            <td class="py-2 pr-4">{{ item.qty }}</td>
                                            <td class="py-2 pr-4">{{ item.uom }}</td>
                                            <td class="py-2 pr-4">{{ formatNumber(item.unitprice) }}</td>
                                            <td class="py-2 pr-4">{{ formatNumber(itemTotal(item)) }}</td>
                                            <td class="py-2 pr-4">
                                                <button
                                                    type="button"
                                                    class="text-red-600 hover:text-red-800"
                                                    @click="removeItem(index)"
                                                >
                                                    Remove
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="bg-[#e8f5e9] p-4 rounded">
                            <p class="text-sm font-medium text-gray-700">
                                Grand Total:
                                <span class="text-lg font-bold text-indigo-600">{{ grandTotal.toFixed(2) }}</span>
                            </p>
                        </div>

                        <div class="flex items-center justify-end space-x-4">
                            <Link
                                href="/receiving"
                                class="text-gray-600 hover:text-gray-900"
                            >
                                Cancel
                            </Link>
                            <Button
                                type="submit"
                                :disabled="form.processing"
                            >
                                Receive Stock
                            </Button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div
            v-if="showItemModal"
            class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/50 p-6"
        >
            <div class="w-full max-w-6xl rounded bg-white shadow-lg">
                <div class="flex items-center justify-between border-b px-6 py-4">
                    <h3 class="text-lg font-semibold text-gray-800">Select Medicine</h3>
                    <button type="button" class="text-gray-500 hover:text-gray-700" @click="closeItemModal">
                        Close
                    </button>
                </div>
                <div class="p-6 overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="text-left text-gray-600">
                                <th class="py-2 pr-4">Code</th>
                                <th class="py-2 pr-4">Name</th>
                                <th class="py-2 pr-4">Description</th>
                                <th class="py-2 pr-4">Brand</th>
                                <th class="py-2 pr-4">Dosage Form</th>
                                <th class="py-2 pr-4">Strength</th>
                                <th class="py-2 pr-4">Default UoM</th>
                                <th class="py-2 pr-4"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="item in props.items" :key="item.itemcode" class="border-t">
                                <td class="py-2 pr-4">{{ item.itemcode }}</td>
                                <td class="py-2 pr-4">{{ item.itemname }}</td>
                                <td class="py-2 pr-4">{{ item.description || '-' }}</td>
                                <td class="py-2 pr-4">{{ item.brand || '-' }}</td>
                                <td class="py-2 pr-4">{{ item.dosage_form || '-' }}</td>
                                <td class="py-2 pr-4">{{ item.strength || '-' }}</td>
                                <td class="py-2 pr-4">{{ item.default_uom || '-' }}</td>
                                <td class="py-2 pr-4">
                                    <Button type="button" @click="chooseItem(item)">
                                        Select
                                    </Button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Input from '@/Components/Input.vue';
import Button from '@/Components/Button.vue';
import type { Item } from '@/Types';

interface Props {
    items: Item[];
}

const props = defineProps<Props>();

const form = useForm({
    supplier: '',
    referenceno: '',
    department: '',
    items: [] as Array<{
        itemcode: number | '';
        batchno: string;
        expirydate: string;
        qty: string;
        uom: string;
        unitprice: string;
    }>,
});

const currentItem = ref({
    itemcode: '' as number | '',
    batchno: '',
    expirydate: '',
    qty: '',
    uom: '',
    unitprice: '',
});

const showItemModal = ref(false);

const itemErrors = ref({
    itemcode: '',
    batchno: '',
    expirydate: '',
    qty: '',
    uom: '',
    unitprice: '',
});

const currentTotal = computed(() => {
    const qty = parseFloat(currentItem.value.qty) || 0;
    const price = parseFloat(currentItem.value.unitprice) || 0;
    return qty * price;
});

const grandTotal = computed(() => {
    return form.items.reduce((sum, item) => sum + itemTotal(item), 0);
});

const itemLabel = (itemcode: number | '') => {
    if (!itemcode) {
        return 'N/A';
    }
    const match = props.items.find(item => item.itemcode === itemcode);
    return match ? `${match.itemname}${match.default_uom ? ` (${match.default_uom})` : ''}` : String(itemcode);
};

const selectedItem = computed(() => {
    if (!currentItem.value.itemcode) {
        return null;
    }
    return props.items.find(item => item.itemcode === currentItem.value.itemcode) || null;
});

const itemTotal = (item: { qty: string; unitprice: string }) => {
    const qty = parseFloat(item.qty) || 0;
    const price = parseFloat(item.unitprice) || 0;
    return qty * price;
};

const formatNumber = (value: number | string) => {
    const num = typeof value === 'number' ? value : parseFloat(value);
    return Number.isFinite(num) ? num.toFixed(2) : '0.00';
};

const openItemModal = () => {
    showItemModal.value = true;
};

const closeItemModal = () => {
    showItemModal.value = false;
};

const chooseItem = (item: Item) => {
    currentItem.value.itemcode = item.itemcode;
    if (!currentItem.value.uom && item.default_uom) {
        currentItem.value.uom = item.default_uom;
    }
    closeItemModal();
};

const resetItemErrors = () => {
    itemErrors.value = {
        itemcode: '',
        batchno: '',
        expirydate: '',
        qty: '',
        uom: '',
        unitprice: '',
    };
};

const addItem = () => {
    resetItemErrors();

    if (!currentItem.value.itemcode) {
        itemErrors.value.itemcode = 'Required';
    }
    if (!currentItem.value.batchno) {
        itemErrors.value.batchno = 'Required';
    }
    if (!currentItem.value.expirydate) {
        itemErrors.value.expirydate = 'Required';
    }
    if (!currentItem.value.qty) {
        itemErrors.value.qty = 'Required';
    }
    if (!currentItem.value.uom) {
        itemErrors.value.uom = 'Required';
    }
    if (!currentItem.value.unitprice) {
        itemErrors.value.unitprice = 'Required';
    }

    const hasErrors = Object.values(itemErrors.value).some(error => error);
    if (hasErrors) {
        return;
    }

    form.items.push({ ...currentItem.value });
    currentItem.value = {
        itemcode: '',
        batchno: '',
        expirydate: '',
        qty: '',
        uom: '',
        unitprice: '',
    };
};

const removeItem = (index: number) => {
    form.items.splice(index, 1);
};

const submit = () => {
    form.post('/receiving');
};
</script>




