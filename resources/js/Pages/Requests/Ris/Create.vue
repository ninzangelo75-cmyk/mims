<template>
    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Create RIS Request
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-[#f6fbf6] overflow-hidden shadow-sm sm:rounded-lg ring-1 ring-[#cfe8d1]">
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

                        <div class="flex items-center justify-end space-x-4">
                            <Link
                                href="/requests/ris"
                                class="text-gray-600 hover:text-gray-900"
                            >
                                Cancel
                            </Link>
                            <Button
                                type="submit"
                                :disabled="form.processing"
                            >
                                Submit Request
                            </Button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import { computed } from 'vue';
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

const submit = () => {
    form.post('/requests/ris');
};
</script>



