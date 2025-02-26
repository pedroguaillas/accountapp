<script setup>
// Imports
import { ref, computed } from "vue";
import DialogModal from "@/Components/DialogModal.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import Table from "@/Components/Table.vue";

// Props
const props = defineProps({
  show: { type: Boolean, default: false },
  withholdings: { type: Array, default: () => [] },
});

// Emits
defineEmits(["close", "selectWithholding"]);

// Estado para el campo de búsqueda
const searchQuery = ref("");

// Filtrar retenciones según el valor ingresado en el input
const filteredWithholdings = computed(() => {
  return props.withholdings.filter((withholding) =>
    [withholding.code, withholding.name, withholding.type]
      .some(field => field.toLowerCase().includes(searchQuery.value.toLowerCase()))
  );
});
</script>

<template>
  <DialogModal :show="show" maxWidth="lg" @close="$emit('close')">
    <template #title>
      <h2 class="text-sm sm:text-lg font-bold w-full pb-2 sm:pb-0">
        Seleccionar Retención
      </h2>
    </template>
    
    <template #content>
      <!-- Campo de búsqueda -->
      <div class="mb-3">
        <input
          v-model="searchQuery"
          type="text"
          placeholder="Buscar por código, nombre o tipo..."
          class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring focus:border-blue-300"
        />
      </div>

      <div class="mt-0 max-h-[300px] overflow-y-auto">
        <Table>
          <thead>
            <tr>
              <th class="text-left px-4 py-2">Código</th>
              <th class="text-right px-4 py-2">Nombre</th>
              <th class="text-left px-4 py-2">Porcentaje</th>
              <th class="text-left px-4 py-2">Tipo</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="withholding in filteredWithholdings"
              :key="withholding.id"
              class="h-8 cursor-pointer hover:bg-gray-200"
              @click="$emit('selectWithholding', withholding)"
            >
              <td class="px-4 py-2">{{ withholding.code }}</td>
              <td class="px-4 py-2">{{ withholding.name }}</td>
              <td class="px-4 py-2">{{ withholding.percentage }}%</td>
              <td class="px-4 py-2">{{ withholding.type }}</td>
            </tr>
            <tr v-if="filteredWithholdings.length === 0">
              <td colspan="4" class="text-center py-2 text-gray-500">
                No se encontraron resultados.
              </td>
            </tr>
          </tbody>
        </Table>
      </div>
    </template>

    <template #footer>
      <SecondaryButton @click="$emit('close')" class="mr-2">
        Cerrar
      </SecondaryButton>
    </template>
  </DialogModal>
</template>
