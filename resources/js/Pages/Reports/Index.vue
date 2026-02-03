<template>
    <AppLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="page-title">Reports</h2>
                    <p class="page-subtitle">Generate and export inventory and movement reports</p>
                </div>
            </div>
        </template>

        <div class="bg-white rounded-2xl shadow-sm border border-[#cfe8d1] bg-[#f6fbf6] p-6">
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




