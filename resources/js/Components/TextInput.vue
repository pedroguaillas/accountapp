<script setup lang="ts">
import { PropType, ref, onMounted } from "vue";

defineProps({
    modelValue: {
        type: [String, Number] as PropType<string | number>,
        default: "", // Valor por defecto para modelValue
    },
});

defineEmits(['update:modelValue']);

const input = ref<HTMLInputElement | null>(null);

onMounted(() => {
    if (input.value && input.value.hasAttribute('autofocus')) {
        input.value.focus();
    }
});

defineExpose({ focus: () => input.value && input.value.focus() });
</script>

<template>
    <input ref="input"
        class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
        :value="modelValue" @change="(event) => {
            if (event.target) {
                $emit('update:modelValue', (event.target as HTMLInputElement).value);
            }
        }" />
</template>
