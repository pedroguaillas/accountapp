<script setup>
import { ref } from "vue";
import IceSelectModel from "./IceSelectModel.vue";
import InputLabel from "@/Components/InputLabel.vue";
import { MagnifyingGlassIcon } from "@heroicons/vue/24/solid";

// Props
defineProps({
  globalIces: { type: Array, default: () => [] },
});

// Refs
const iceId = ref(0);
const code = ref("");
const search = ref("");
const percentage = ref("");
const modal = ref(false);

// Método para alternar la visibilidad de la modal
const toggleModal = () => {
  modal.value = !modal.value;
};

// Método para recibir la retención seleccionada
const selectedIce= ref(null); // Nuevo ref para almacenar la selección temporalmente

const handleIceSelect = (ice) => {
  console.log("Retención seleccionada:", ice);

  // Guarda la retención seleccionada sin enviarla al componente principal
  selectedIce.value = ice;

  // Asigna los valores a los campos del formulario
  iceId.value = ice.id;
  search.value = ice.name;
  code.value = ice.code;
  percentage.value = ice.percentage;

  toggleModal(); // Cierra la modal
};

const handleSave = () => {
  if (selectedIce.value) {
    emit("addIces", selectedIce.value); // Solo se envía al padre cuando se presiona "Guardar"
  }
};

// Emit
const emit = defineEmits(["addIces"]);
</script>


<template>
  <div class="mt-2 grid grid-cols-5 gap-2">
    <!-- Código -->
    <div>
      <InputLabel for="code" value="Código" />
      <input
        v-model="code"
        class="block w-full border border-gray-300 px-4 py-2 focus:outline-none"
        disabled
      />
    </div>

    <!-- Nombre -->
    <div class="col-span-2">
      <InputLabel for="search" value="Buscar" />
      <div class="flex">
        <input
          v-model="search"
          type="search"
          class="block w-full border border-gray-300 px-4 py-1 focus:outline-none"
          disabled
        />
        <button
          @click="toggleModal"
          class="bg-slate-500 text-white px-3 py-2 hover:bg-slate-600 focus:outline-none"
        >
          <MagnifyingGlassIcon class="size-6 text-white stroke-[3px]" />
        </button>
      </div>
    </div>

    <!-- Porcentaje -->
    <div>
      <InputLabel for="percentage" value="Porcentaje" />
      <input
        v-model="percentage"
        type="number"
        class="block w-full border border-gray-300 px-4 py-2 focus:outline-none"
      />
    </div>

    <button
      @click="handleSave"
      class="mt-4 px-4 py-2 bg-blue-500 hover:bg-blue-700 text-white font-bold rounded"
    >
      Guardar
    </button>
  </div>

  <!-- Modal de Selección de Retención -->
  <IceSelectModel
    :show="modal"
    :ices="globalIces"
    @close="toggleModal"
    @selectIce="handleIceSelect"
  />
</template>
