<template>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th
                        v-for="column in columns"
                        :key="column.key"
                        scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                    >
                        {{ column.label }}
                    </th>
                    <th v-if="actions.length > 0" scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Actions
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="(row, index) in data" :key="index" class="hover:bg-gray-50">
                    <td
                        v-for="column in columns"
                        :key="column.key"
                        class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"
                    >
                        <slot :name="`cell-${column.key}`" :row="row" :value="row[column.key]">
                            {{ row[column.key] }}
                        </slot>
                    </td>
                    <td v-if="actions.length > 0" class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <slot name="actions" :row="row">
                            <button
                                v-for="action in actions"
                                :key="action.label"
                                :class="action.class || 'text-indigo-600 hover:text-indigo-900'"
                                @click="action.onClick(row)"
                            >
                                {{ action.label }}
                            </button>
                        </slot>
                    </td>
                </tr>
                <tr v-if="data.length === 0">
                    <td :colspan="columns.length + (actions.length > 0 ? 1 : 0)" class="px-6 py-4 text-center text-sm text-gray-500">
                        No data available
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<script setup lang="ts">
interface Column {
    key: string;
    label: string;
}

interface Action {
    label: string;
    onClick: (row: any) => void;
    class?: string;
}

interface Props {
    columns: Column[];
    data: any[];
    actions?: Action[];
}

withDefaults(defineProps<Props>(), {
    actions: () => [],
});
</script>

