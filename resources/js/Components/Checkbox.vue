<script lang="ts">
import { computed } from "vue";

const emit = defineEmits(["update:checked"]);

const props = withDefaults(defineProps<{
  checked: boolean | any[];
  value: string | null;
  label: string | null;
}>(), {
  checked: false,
  value: null,
  label: null,
});

const proxyChecked = computed({
  get() {
    return props.checked;
  },
  set(val) {
    emit("update:checked", val);
  },
});
</script>

<template>
  <label class="flex items-center space-x-2">
    <input v-model="proxyChecked" type="checkbox" :value="value"
      class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800" />
    <span class="font-medium text-sm text-gray-700 dark:text-gray-300">{{ label }}</span>
  </label>
</template>
