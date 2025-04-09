<script setup lang="ts">

// Imports
import { PropType, ref, onMounted } from "vue";

interface Option {
  value: string | number;
  label: string;
}

// Definir las propiedades del componente
const props = defineProps({
  modelValue: {
    type: [String, Number] as PropType<string | number>,
    default: "", // Valor por defecto para modelValue
  },
  options: {
    type: Array as PropType<Option[]>,
    required: true, // Asegura que siempre se pase un array
  },
  seleccione: {
    type: Boolean,
    default: true,
  },
  required: {
    type: Boolean,
    default: false,
  }
});

// Emitir eventos
defineEmits(["update:modelValue"]);

// Referencia al elemento <select>
const select = ref<HTMLSelectElement | null>(null);

// Manejar el foco en el componente si tiene el atributo "autofocus"
onMounted(() => {
  if (select.value && select.value.hasAttribute("autofocus")) {
    select.value.focus();
  }
});

// Exponer la función `focus` para que pueda ser llamada desde el padre
defineExpose({
  focus: () => select.value && select.value.focus(),
});
</script>

<template>
  <select ref="select"
    class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
    :value="modelValue" :required="required" @change="(event) => {
      if (event.target) {
        $emit('update:modelValue', (event.target as HTMLSelectElement).value);
      }
    }">
    <!-- Opción predeterminada -->
    <option v-if="seleccione" value="">Seleccione</option>

    <!-- Generar opciones dinámicas -->
    <option v-for="option in props.options" :key="option.value" :value="option.value">
      {{ option.label }}
    </option>
  </select>
</template>
