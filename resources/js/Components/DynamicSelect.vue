<script setup>

// Imports
import { onMounted, ref } from "vue";

// Definir las propiedades del componente
defineProps({
  modelValue: {
    type: String,
    default: "", // Valor por defecto para modelValue
  },
  options: {
    type: Array,
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
const select = ref(null);

// Manejar el foco en el componente si tiene el atributo "autofocus"
onMounted(() => {
  if (select.value && select.value.hasAttribute("autofocus")) {
    select.value.focus();
  }
});

// Exponer la función `focus` para que pueda ser llamada desde el padre
defineExpose({
  focus: () => select.value.focus(),
});
</script>

<template>
  <select ref="select"
    class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
    :value="modelValue" :required="required" @change="$emit('update:modelValue', $event.target.value)">
    <!-- Opción predeterminada -->
    <option v-if="seleccione" value="">Seleccione</option>

    <!-- Generar opciones dinámicas -->
    <option v-for="option in options" :key="option.value" :value="option.value">
      {{ option.label }}
    </option>
  </select>
</template>
