<script setup lang="ts">

// Imports
import { DialogModal } from "@/Components";
import { CostCenter } from "@/types";

// Props
const props = defineProps<{
  costCenters: CostCenter[]; // Paginación de los bancos
  show: boolean;
}>()

// Método para seleccionar centro de costo
const handleSelectCostCenter = (costCenter: CostCenter) => {
  emit("selectCostCenter", costCenter); // Emitir el centro de costo seleccionado
  emit("close"); // Cerrar el modal
};

// Emits
const emit = defineEmits(["close", "selectCostCenter"]);
</script>

<template>
  <DialogModal :show="show" maxWidth="lg" @close="emit('close')">
    <template #title>Seleccionar centro de costo</template>
    <template #content>
      <div class="mt-0 max-h-[300px] overflow-y-auto">
        <table class="mt-4 text-xs sm:text-sm table-auto w-full text-center text-gray-700">
          <thead>
            <tr class="[&>th]:py-2">
              <th class="w-1">N°</th>
              <th>Código</th>
              <th class="text-left">Descripción</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(costCenter, i) in costCenters" :key="costCenter.id" class="border-t [&>td]:py-2 cursor-pointer"
              @click="handleSelectCostCenter(costCenter)">
              <td>{{ i + 1 }}</td>
              <td>{{ costCenter.code }}</td>
              <td class="text-left">{{ costCenter.name }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </template>
  </DialogModal>
</template>
