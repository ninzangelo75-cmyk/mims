<template>
    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Add Medicine
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <form @submit.prevent="submit" class="p-6 space-y-6">
                        <Input
                            id="itemname"
                            v-model="form.itemname"
                            label="Medicine Name"
                            required
                            :error="form.errors.itemname"
                        />

                        <Input
                            id="description"
                            v-model="form.description"
                            label="Description"
                            type="textarea"
                            :error="form.errors.description"
                        />

                        <div class="grid grid-cols-2 gap-4">
                            <Input
                                id="brand"
                                v-model="form.brand"
                                label="Brand"
                                :error="form.errors.brand"
                            />

                            <Input
                                id="dosage_form"
                                v-model="form.dosage_form"
                                label="Dosage Form"
                                :error="form.errors.dosage_form"
                            />
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <Input
                                id="strength"
                                v-model="form.strength"
                                label="Strength"
                                :error="form.errors.strength"
                            />

                            <Input
                                id="default_uom"
                                v-model="form.default_uom"
                                label="Default UOM"
                                :error="form.errors.default_uom"
                            />
                        </div>

                        <div class="flex items-center justify-end space-x-4">
                            <Link
                                href="/items"
                                class="text-gray-600 hover:text-gray-900"
                            >
                                Cancel
                            </Link>
                            <Button
                                type="submit"
                                :disabled="form.processing"
                            >
                                Create Medicine
                            </Button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Input from '@/Components/Input.vue';
import Button from '@/Components/Button.vue';

const form = useForm({
    itemname: '',
    description: '',
    brand: '',
    dosage_form: '',
    strength: '',
    default_uom: '',
});

const submit = () => {
    form.post('/items');
};
</script>

