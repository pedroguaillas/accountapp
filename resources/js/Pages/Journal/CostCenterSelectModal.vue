<script setup>
//imports
import DialogModal from "@/Components/DialogModal.vue";

// Props
defineProps({
  costCenters: { type: Array, default: () => [] },
  show: { type: Boolean, default: false },
});

// Método para seleccionar centro de costo
const handleSelectCostCenter = (costCenter) => {
  emit("selectCostCenter", costCenter); // Emitir el centro de costo seleccionado
  emit("close"); // Cerrar el modal
};

// Emits
const emit = defineEmits(["close", "selectCostCenter"]);
</script>

<template>
  <DialogModal :show="show" maxWidth="lg" @close="emit('close')">
    <template #title> Seleccionar Centro de Costo </template>
    <template #content>
      <div class="mt-4 max-h-[300px] overflow-y-auto">
        <table
          class="mt-4 text-xs sm:text-sm table-auto w-full text-center text-gray-700"
        >
          <thead>
            <tr class="[&>th]:py-2">
              <th class="w-1">N°</th>
              <th>Centro de Costo</th>
              <th>Descripción</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="(costCenter, i) in costCenters"
              :key="costCenter.id"
              class="border-t [&>td]:py-2 cursor-pointer"
              @click="handleSelectCostCenter(costCenter)"
            >
              <td>{{ i + 1 }}</td>
              <td>{{ costCenter.code }}</td>
              <td>{{ costCenter.name }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </template>
  </DialogModal>
</template>
