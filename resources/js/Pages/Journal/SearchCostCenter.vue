<script setup>

// Imports
import CostCenterSelectModal from "./CostCenterSelectModal.vue";
import { ref } from "vue";

// Props
defineProps({
  costCenters: { type: Array, default: () => [] },
});

// Refs
const modal = ref(false); // Estado del modal
const code = ref("");
const name = ref("");

// Método para alternar la visibilidad del modal
const toggleModal = () => {
  modal.value = !modal.value;
};

// Método para recibir el centro de costo seleccionado y enviarlo al componente create
const handleCostCenterSelect = (costCenter) => {
  name.value = costCenter.name;
  // Establecemos el código de la cuenta en el input de código
  code.value = costCenter.code;

  // Emitir el centro de costo seleccionado
  emit("selectCostCenter", costCenter);
};

const emit = defineEmits(["selectCostCenter"]);
</script>

<template>
  <div class="flex mt-2">
    <!-- Campo para el código -->
    <div
      class="block w-full border border-gray-300 rounded-l px-4 py-2 focus:outline-none"
    >
      {{ code || "Código" }}
    </div>

    <!-- Campo para la descripción -->
    <input
      v-model="name"
      type="text"
      class="block w-full border border-gray-300 rounded-l px-4 py-2 focus:outline-none"
      placeholder="Buscar..."
    />

    <!-- Botón para abrir el modal -->
    <button
      @click="toggleModal"
      class="bg-slate-500 text-white px-4 py-2 hover:bg-slate-600 focus:outline-none"
    >
      @
    </button>
  </div>

  <!-- Modal de Selección de Centro de Costo -->
  <CostCenterSelectModal
    :show="modal"
    :costCenters="costCenters"
    @close="toggleModal"
    @selectCostCenter="handleCostCenterSelect"
  />
</template>
