<template>
    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Receive Stock
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <form @submit.prevent="submit" class="p-6 space-y-6">
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
                                id="batchno"
                                v-model="form.batchno"
                                label="Batch Number"
                                required
                                :error="form.errors.batchno"
                            />

                            <Input
                                id="expirydate"
                                v-model="form.expirydate"
                                label="Expiry Date"
                                type="date"
                                required
                                :error="form.errors.expirydate"
                            />
                        </div>

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

                        <div class="grid grid-cols-3 gap-4">
                            <Input
                                id="qty"
                                v-model="form.qty"
                                label="Quantity"
                                type="number"
                                step="0.01"
                                required
                                :error="form.errors.qty"
                            />

                            <Input
                                id="uom"
                                v-model="form.uom"
                                label="Unit of Measure"
                                required
                                :error="form.errors.uom"
                            />

                            <Input
                                id="unitprice"
                                v-model="form.unitprice"
                                label="Unit Price"
                                type="number"
                                step="0.01"
                                required
                                :error="form.errors.unitprice"
                                @input="calculateTotal"
                            />
                        </div>

                        <div class="bg-gray-50 p-4 rounded">
                            <p class="text-sm font-medium text-gray-700">
                                Total Amount: <span class="text-lg font-bold text-indigo-600">{{ totalAmount.toFixed(2) }}</span>
                            </p>
                        </div>

                        <Input
                            id="department"
                            v-model="form.department"
                            label="Department"
                            :error="form.errors.department"
                        />

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
    </AppLayout>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Input from '@/Components/Input.vue';
import Select from '@/Components/Select.vue';
import Button from '@/Components/Button.vue';
import type { Item } from '@/Types';

interface Props {
    items: Item[];
}

const props = defineProps<Props>();

const itemOptions = computed(() => {
    return props.items.map(item => ({
        value: item.itemcode,
        label: `${item.itemname}${item.default_uom ? ` (${item.default_uom})` : ''}`,
    }));
});

const form = useForm({
    itemcode: '',
    supplier: '',
    referenceno: '',
    qty: '',
    uom: '',
    unitprice: '',
    batchno: '',
    expirydate: '',
    department: '',
});

const totalAmount = computed(() => {
    const qty = parseFloat(form.qty) || 0;
    const price = parseFloat(form.unitprice) || 0;
    return qty * price;
});

const calculateTotal = () => {
    // Reactive calculation handled by computed
};

const submit = () => {
    form.post('/receiving');
};
</script>

