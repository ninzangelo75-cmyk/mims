<template>
    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Release Stock - RIS {{ request.ris_no }}
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-[#f6fbf6] overflow-hidden shadow-sm sm:rounded-lg ring-1 ring-[#cfe8d1]">
                    <div class="p-6">
                        <div class="mb-6">
                            <p class="page-subtitle">Medicine: <span class="font-medium">{{ request.item?.itemname }}</span></p>
                            <p class="page-subtitle">Requested Quantity: <span class="font-medium">{{ request.req_qty }}</span></p>
                        </div>

                        <form @submit.prevent="submit" class="space-y-4">
                            <div v-for="batch in batches" :key="batch.recid" class="border rounded p-4">
                                <div class="flex items-center justify-between mb-2">
                                    <div>
                                        <span class="font-medium">Batch: {{ batch.batchno }}</span>
                                        <span class="page-subtitle ml-4">Expiry: {{ batch.expirydate }}</span>
                                    </div>
                                    <span class="text-sm">Available: {{ batch.available_qty }}</span>
                                </div>
                                <Input
                                    :id="`qty_${batch.recid}`"
                                    v-model="batchForm[`batch_${batch.recid}`]"
                                    :label="`Quantity to release from batch ${batch.batchno}`"
                                    type="number"
                                    step="0.01"
                                    :max="batch.available_qty"
                                />
                            </div>

                            <div class="flex items-center justify-end space-x-4 mt-6">
                                <Link
                                    href="/approvals"
                                    class="text-gray-600 hover:text-gray-900"
                                >
                                    Cancel
                                </Link>
                                <Button
                                    type="submit"
                                    :disabled="form.processing"
                                >
                                    Release Stock
                                </Button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Input from '@/Components/Input.vue';
import Button from '@/Components/Button.vue';

interface Props {
    request: any;
    batches: any[];
}

const props = defineProps<Props>();

const batchForm = ref<Record<string, string>>({});

const form = useForm({
    batches: [] as any[],
});

const submit = () => {
    const batches = props.batches.map(batch => ({
        recid: batch.recid,
        qty: parseFloat(batchForm.value[`batch_${batch.recid}`] || '0'),
    })).filter(b => b.qty > 0);

    if (batches.length === 0) {
        alert('Please select at least one batch with quantity greater than 0.');
        return;
    }

    form.batches = batches;
    form.post(`/releasing/ris/${props.request.req_ris}`);
};
</script>



