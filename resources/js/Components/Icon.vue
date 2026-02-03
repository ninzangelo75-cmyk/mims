<template>
    <svg
        :class="className"
        viewBox="0 0 24 24"
        fill="none"
        stroke="currentColor"
        stroke-width="2"
        aria-hidden="true"
    >
        <!-- pill -->
        <template v-if="name === 'pill'">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 13.5 13.5 10.5" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M7 17a5 5 0 0 1 0-7l3-3a5 5 0 0 1 7 7l-3 3a5 5 0 0 1-7 0Z" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M8.5 8.5 15.5 15.5" />
        </template>

        <!-- package-check -->
        <template v-else-if="name === 'package-check'">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 2 2 7l10 5 10-5-10-5Z" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M2 7v10l10 5 10-5V7" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 13l2 2 4-4" />
        </template>

        <!-- package -->
        <template v-else-if="name === 'package'">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 2 2 7l10 5 10-5-10-5Z" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M2 7v10l10 5 10-5V7" />
        </template>

        <!-- clipboard-list -->
        <template v-else-if="name === 'clipboard-list'">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 4h6a2 2 0 0 1 2 2v14H7V6a2 2 0 0 1 2-2Z" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 4V3h6v1" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 10h6" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 14h6" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 18h6" />
        </template>

        <!-- check-circle -->
        <template v-else-if="name === 'check-circle'">
            <path stroke-linecap="round" stroke-linejoin="round" d="M22 12a10 10 0 1 1-20 0 10 10 0 0 1 20 0Z" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M8 12l2 2 6-6" />
        </template>

        <!-- file-text -->
        <template v-else-if="name === 'file-text'">
            <path stroke-linecap="round" stroke-linejoin="round" d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M14 2v6h6" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M16 13H8" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M16 17H8" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M10 9H8" />
        </template>

        <!-- scroll-text -->
        <template v-else-if="name === 'scroll-text'">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 4h10a2 2 0 0 1 2 2v14" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h10" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M8 8h8" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h8" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M8 16h6" />
        </template>

        <!-- user -->
        <template v-else-if="name === 'user'">
            <path stroke-linecap="round" stroke-linejoin="round" d="M20 21a8 8 0 1 0-16 0" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 13a4 4 0 1 0-4-4 4 4 0 0 0 4 4Z" />
        </template>

        <!-- chevron-left -->
        <template v-else-if="name === 'chevron-left'">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 18 9 12l6-6" />
        </template>

        <!-- chevron-right -->
        <template v-else-if="name === 'chevron-right'">
            <path stroke-linecap="round" stroke-linejoin="round" d="m9 18 6-6-6-6" />
        </template>

        <!-- bell -->
        <template v-else-if="name === 'bell'">
            <path stroke-linecap="round" stroke-linejoin="round" d="M18 8a6 6 0 1 0-12 0c0 7-3 7-3 7h18s-3 0-3-7" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M13.73 21a2 2 0 0 1-3.46 0" />
        </template>

        <!-- log-out -->
        <template v-else-if="name === 'log-out'">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M16 17l5-5-5-5" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 12H9" />
        </template>
    </svg>
</template>

<script setup lang="ts">
type IconName =
    | 'pill'
    | 'package-check'
    | 'package'
    | 'clipboard-list'
    | 'check-circle'
    | 'file-text'
    | 'scroll-text'
    | 'user'
    | 'chevron-left'
    | 'chevron-right'
    | 'bell'
    | 'log-out';

withDefaults(
    defineProps<{
        name: IconName;
        className?: string;
    }>(),
    {
        className: 'w-5 h-5',
    }
);
</script>


