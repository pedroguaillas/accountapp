<script setup lang="ts">
import { ref } from "vue";
import WithholdingSelectModel from "./WithholdingSelectModel.vue";
import { InputLabel } from "@/Components";
import { MagnifyingGlassIcon } from "@heroicons/vue/24/solid";
import { Withholding } from "@/types";

// Props
defineProps<{
  globalWithholdings: Withholding[];
}>();

// Refs
const withholdingId = ref(0);
const code = ref("");
const search = ref("");
const percentage = ref("");
const type = ref("");
const modal = ref(false);

// Método para alternar la visibilidad de la modal
const toggleModal = () => {
  modal.value = !modal.value;
};

// Método para recibir la retención seleccionada
const selectedWithholding = ref<Withholding | null>(null); // Nuevo ref para almacenar la selección temporalmente

const handleWithholdingSelect = (withholding: Withholding) => {
  console.log("Retención seleccionada:", withholding);

  // Guarda la retención seleccionada sin enviarla al componente principal
  selectedWithholding.value = withholding;

  // Asigna los valores a los campos del formulario
  withholdingId.value = withholding.id ?? 0;
  search.value = withholding.name;
  code.value = withholding.code;
  percentage.value = String(withholding.percentage);
  type.value = withholding.type;

  toggleModal(); // Cierra la modal
};

const handleSave = () => {
  if (selectedWithholding.value) {
    emit("addWithholdings", selectedWithholding.value); // Solo se envía al padre cuando se presiona "Guardar"
  }
};

// Emit
const emit = defineEmits(["addWithholdings"]);
</script>


<template>
  <div class="mt-2 grid grid-cols-5 gap-2">
    <!-- Código -->
    <div>
      <InputLabel for="code" value="Código" />
      <input v-model="code" class="block w-full border border-gray-300 px-4 py-2 focus:outline-none" disabled />
    </div>

    <!-- Nombre -->
    <div class="col-span-2">
      <InputLabel for="search" value="Buscar" />
      <div class="flex">
        <input v-model="search" type="search" class="block w-full border border-gray-300 px-4 py-1 focus:outline-none"
          disabled />
        <button @click="toggleModal" class="bg-slate-500 text-white px-3 py-2 hover:bg-slate-600 focus:outline-none">
          <MagnifyingGlassIcon class="size-6 text-white stroke-[3px]" />
        </button>
      </div>
    </div>

    <!-- Porcentaje -->
    <div>
      <InputLabel for="percentage" value="Porcentaje" />
      <input v-model="percentage" type="number"
        class="block w-full border border-gray-300 px-4 py-2 focus:outline-none" />
    </div>

    <!-- Tipo -->
    <div>
      <InputLabel for="type" value="Tipo" />
      <input v-model="type" class="block w-full border border-gray-300 px-4 py-2 focus:outline-none" disabled />
    </div>

    <button @click="handleSave" class="mt-4 px-4 py-2 bg-blue-500 hover:bg-blue-700 text-white font-bold rounded">
      Guardar
    </button>
  </div>

  <!-- Modal de Selección de Retención -->
  <WithholdingSelectModel :show="modal" :withholdings="globalWithholdings" @close="toggleModal"
    @selectWithholding="handleWithholdingSelect" />
</template>
