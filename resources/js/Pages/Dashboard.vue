<template>
    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Dashboard
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-[#f6fbf6] overflow-hidden shadow-sm sm:rounded-lg ring-1 ring-[#cfe8d1]">
                    <div class="p-6 text-gray-900">
                        <p>Welcome, {{ $page.props.auth.user?.fullname }}!</p>
                        <p class="mt-2">Role: {{ $page.props.auth.user?.role }}</p>
                        <p class="mt-2">Department: {{ $page.props.auth.user?.department || 'N/A' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import AppLayout from '@/Layouts/AppLayout.vue';
</script>



