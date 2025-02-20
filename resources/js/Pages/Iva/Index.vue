<script setup>
import { ref, computed } from "vue";
import axios from "axios";
import AdminLayout from "@/Layouts/AdminLayout.vue";

// Recibes 'paymethods' desde Inertia
const props = defineProps({
  ivas: {
    type: Array,
    default: () => [],
  },
});

// Inicializar selectedPayments con los métodos que tienen selected: true
const selectedIvas = ref(
  props.ivas.filter((method) => method.selected).map((method) => method.code)
);

// Computed para verificar si todos están seleccionados
const allSelected = computed(
  () => selectedIvas.value.length === props.ivas.length
);

// Función para seleccionar/deseleccionar todos
const toggleAll = () => {
  if (allSelected.value) {
    selectedIvas.value = [];
  } else {
    selectedIvas.value = props.ivas.map((pm) => pm.code);
  }
};

// Enviar la selección al backend
const saveSelection = () => {
  axios
    .post(route("busssines.setting.ivas.store"), {
      selected: selectedIvas.value,
    })
    .then((response) => {
      console.log(response.data.message);
      alert("Selección guardada correctamente");
    })
    .catch((error) => {
      console.error("Error al guardar la selección", error);
      alert("Ocurrió un error al guardar");
    });
};
</script>

<template>
  <AdminLayout title="Métodos de pago">
    <div class="p-4 bg-white rounded drop-shadow-md">
      <!-- Header -->
      <div class="flex flex-col sm:flex-row justify-between items-center">
        <h2 class="text-sm sm:text-lg font-bold w-full pb-2 sm:pb-0">
          Ivas
        </h2>
      </div>

      <div>
        <!-- Checkbox para seleccionar todos -->
        <div>
          <input type="checkbox" :checked="allSelected" @change="toggleAll" />
          <label class="ml-2">Seleccionar todos</label>
        </div>

        <!-- Lista de checkboxes -->
        <div v-for="iva in ivas" :key="iva.code" class="mt-2">
          <input
            type="checkbox"
            :value="iva.code"
            v-model="selectedIvas"
          />
          <label class="ml-2">{{ iva.name }}</label>
        </div>

        <!-- Botón para guardar -->
        <button
          @click="saveSelection"
          class="mt-4 px-4 py-2 bg-blue-500 text-white rounded"
        >
          Guardar selección
        </button>
      </div>
    </div>
  </AdminLayout>
</template>
