<template>
    <div>
        <label v-if="label" :for="id" class="block text-sm font-medium text-gray-700 mb-1">
            {{ label }}
            <span v-if="required" class="text-red-500">*</span>
        </label>
        <input
            v-if="type !== 'textarea'"
            :id="id"
            :type="type"
            :value="modelValue"
            :placeholder="placeholder"
            :required="required"
            :disabled="disabled"
            :class="[
                'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm',
                error ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : '',
                disabled ? 'bg-gray-100 cursor-not-allowed' : ''
            ]"
            @input="$emit('update:modelValue', ($event.target as HTMLInputElement).value)"
        />
        <textarea
            v-else
            :id="id"
            :value="modelValue"
            :placeholder="placeholder"
            :required="required"
            :disabled="disabled"
            rows="3"
            :class="[
                'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm',
                error ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : '',
                disabled ? 'bg-gray-100 cursor-not-allowed' : ''
            ]"
            @input="$emit('update:modelValue', ($event.target as HTMLTextAreaElement).value)"
        />
        <p v-if="error" class="mt-1 text-sm text-red-600">{{ error }}</p>
        <p v-if="hint && !error" class="mt-1 text-sm text-gray-500">{{ hint }}</p>
    </div>
</template>

<script setup lang="ts">
interface Props {
    id?: string;
    label?: string;
    type?: string;
    modelValue: string | number;
    placeholder?: string;
    required?: boolean;
    disabled?: boolean;
    error?: string;
    hint?: string;
}

withDefaults(defineProps<Props>(), {
    type: 'text',
    required: false,
    disabled: false,
});

defineEmits<{
    'update:modelValue': [value: string | number];
}>();
</script>

