<template>
    <div v-if="hasError" class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <div>
                <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                    Something went wrong
                </h2>
                <p class="mt-2 text-center text-sm text-gray-600">
                    {{ errorMessage }}
                </p>
            </div>
            <div class="mt-8">
                <button
                    @click="reset"
                    class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                >
                    Try again
                </button>
            </div>
        </div>
    </div>
    <slot v-else />
</template>

<script setup lang="ts">
import { ref, onErrorCaptured } from 'vue';

const hasError = ref(false);
const errorMessage = ref('An unexpected error occurred.');

onErrorCaptured((err: Error) => {
    hasError.value = true;
    errorMessage.value = err.message || 'An unexpected error occurred.';
    console.error('Error caught by ErrorBoundary:', err);
    return false;
});

const reset = () => {
    hasError.value = false;
    errorMessage.value = 'An unexpected error occurred.';
    window.location.reload();
};
</script>

