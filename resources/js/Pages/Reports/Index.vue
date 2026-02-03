<template>
    <AppLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Reports</h2>
                    <p class="text-sm text-gray-600">Generate and export inventory and movement reports</p>
                </div>
            </div>
        </template>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
            <p class="text-gray-700">
                Reports module is ready for the next iteration. We can add:
            </p>
            <ul class="mt-3 list-disc list-inside text-gray-600 space-y-1">
                <li>Stock-in / Stock-out reports</li>
                <li>Expiry monitoring report</li>
                <li>Department usage report</li>
                <li>Printable RIS / PTR documents</li>
            </ul>
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import AppLayout from '@/Layouts/AppLayout.vue';
</script>


