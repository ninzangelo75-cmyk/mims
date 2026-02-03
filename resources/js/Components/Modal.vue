<template>
    <Teleport to="body">
        <Transition
            enter-active-class="ease-out duration-300"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="ease-in duration-200"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div
                v-if="show"
                class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/40 backdrop-blur-sm"
                @click.self="$emit('close')"
            >
                <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                    <Transition
                        enter-active-class="ease-out duration-300"
                        enter-from-class="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                        enter-to-class="opacity-100 translate-y-0 sm:scale-100"
                        leave-active-class="ease-in duration-200"
                        leave-from-class="opacity-100 translate-y-0 sm:scale-100"
                        leave-to-class="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    >
                        <div
                            v-if="show"
                            :class="['relative transform overflow-hidden rounded-lg bg-[#F8FAFC] text-left shadow-xl transition-all sm:my-8 sm:w-full', maxWidth]"
                            @click.stop
                        >
                            <div class="bg-[#F8FAFC] px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                                <div v-if="title" class="mb-4">
                                    <h3 class="text-lg font-medium leading-6 text-gray-900">
                                        {{ title }}
                                    </h3>
                                </div>
                                <slot />
                            </div>
                            <div v-if="showFooter" class="bg-[#dff1e1] px-4 py-3 sm:flex sm:justify-end sm:items-center sm:gap-2 sm:px-6">
                                <slot name="footer">
                                    <Button @click="$emit('close')">Close</Button>
                                </slot>
                            </div>
                        </div>
                    </Transition>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>

<script setup lang="ts">
import Button from './Button.vue';

interface Props {
    show: boolean;
    title?: string;
    showFooter?: boolean;
    maxWidth?: string;
}

withDefaults(defineProps<Props>(), {
    showFooter: true,
    maxWidth: 'sm:max-w-lg',
});

defineEmits<{
    close: [];
}>();
</script>

