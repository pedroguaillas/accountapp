<script setup>

// Imports
import CostCenterSelectModal from "./CostCenterSelectModal.vue";
import { MagnifyingGlassIcon } from "@heroicons/vue/24/solid";
import { ref, computed } from "vue";

// Props
const props = defineProps({
  costCenters: { type: Array, default: () => [] },
});

// Refs
const modal = ref(false); // Estado del modal
const code = ref("");
const name = ref("");
const isDropdownOpen = ref(false);

// Sugerencias filtradas según el texto ingresado
const filteredCostCenters = computed(() =>
  props.costCenters.filter((costCenter) =>
    costCenter.name.toLowerCase().includes(name.value.toLowerCase())
  )
);

// Método para alternar la visibilidad del modal
const toggleModal = () => {
  modal.value = !modal.value;
};

// Método para recibir el centro de costo seleccionado y enviarlo al componente create
const handleCostCenterSelect = (costCenter) => {
  name.value = costCenter.name;
  code.value = costCenter.code;

  isDropdownOpen.value = false; // Cerrar el menú desplegable
  // Emitir el centro de costo seleccionado
  emit("selectCostCenter", costCenter);
};

// Control de eventos
const handleKey = () => {
  if (name.value.length > 0) {
    isDropdownOpen.value = true;
  }
};

const handleInputBlur = () => {
  setTimeout(() => (isDropdownOpen.value = false), 200); // Retraso para permitir clic en la sugerencia
};

const emit = defineEmits(["selectCostCenter"]);
</script>

<template>
  <div class="block w-full mt-2">

    <div class="flex">

      <!-- Campo para el código -->
      <div class="w-[6em] border-y border-l border-gray-300 text-gray-500 rounded-l px-4 py-2 focus:outline-none">
        {{ code || "Código" }}
      </div>

      <!-- Campo para la descripción -->
      <div class="flex-1 relative">
        <input v-model="name" type="search" class="border w-full border-gray-300 px-4 py-2 focus:outline-none"
          placeholder="Buscar ..." @keydown="handleKey" @blur="handleInputBlur" />
        <ul v-if="isDropdownOpen && filteredCostCenters.length"
          class="absolute z-10 bg-white border-b border-x border-gray-300 rounded-b w-full max-h-40 overflow-y-auto shadow-lg">
          <li v-for="costCenter in filteredCostCenters" :key="costCenter.id" @click="handleCostCenterSelect(costCenter)"
            class="px-4 py-2 cursor-pointer hover:bg-gray-100">
            {{ costCenter.name }}
          </li>
        </ul>
      </div>

      <!-- Botón para abrir el modal -->
      <button @click="toggleModal"
        class="w-[3em] bg-slate-500 text-white px-3 py-2 rounded-r hover:bg-slate-600 focus:outline-none">
        <MagnifyingGlassIcon class="size-6 text-white stroke-[3px]" />
      </button>

    </div>

  </div>

  <!-- Modal de Selección de Centro de Costo -->
  <CostCenterSelectModal :show="modal" :costCenters="props.costCenters" @close="toggleModal"
    @selectCostCenter="handleCostCenterSelect" />
</template>
