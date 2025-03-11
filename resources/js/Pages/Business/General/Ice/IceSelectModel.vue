<script setup>
import { ref, computed } from "vue";
import DialogModal from "@/Components/DialogModal.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import Table from "@/Components/Table.vue";

// Props
const props = defineProps({
  show: { type: Boolean, default: false },
  ices: { type: Array, default: () => [] },
});

// Emits
defineEmits(["close", "selectIce"]);

// Estado de búsqueda
const searchQuery = ref("");

// Computed para filtrar los ICEs en base a la búsqueda
const filteredIces = computed(() => {
  if (!searchQuery.value) {
    return props.ices;
  }

  const query = searchQuery.value.toLowerCase();

  return props.ices.filter((ice) =>
    String(ice.code).toLowerCase().includes(query) || 
    String(ice.name).toLowerCase().includes(query)
  );
});

</script>

<template>
  <DialogModal :show="show" maxWidth="lg" @close="$emit('close')">
    <template #title>
      <h2 class="text-sm sm:text-lg font-bold w-full pb-2 sm:pb-0">
        Seleccionar ICE
      </h2>
    </template>

    <template #content>
      <!-- Campo de búsqueda -->
      <div class="mb-3">
        <label for="search" class="sr-only">Buscar ICE</label>
        <input
          id="search"
          v-model="searchQuery"
          type="text"
          placeholder="Buscar por código o nombre"
          class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring focus:border-blue-300"
          @keyup.enter="
            filteredIces.length > 0 && $emit('selectIce', filteredIces[0])
          "
        />
      </div>

      <!-- Tabla con scroll en dispositivos pequeños -->
      <div class="mt-0 max-h-[300px] overflow-y-auto overflow-x-auto">
        <Table>
          <thead>
            <tr class="bg-gray-100">
              <th class="text-left px-4 py-2">Código</th>
              <th class="text-left px-4 py-2">Nombre</th>
              <th class="text-right px-4 py-2">%</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="ice in filteredIces"
              :key="ice.id"
              class="h-8 cursor-pointer hover:bg-gray-200 transition-colors"
              @click="$emit('selectIce', ice)"
            >
              <td class="px-4 py-2">{{ ice.code }}</td>
              <td class="px-4 py-2">{{ ice.name }}</td>
              <td class="px-4 py-2 text-right">{{ ice.percentage }}%</td>
            </tr>
            <tr v-if="filteredIces.length === 0">
              <td colspan="3" class="text-center py-2 text-gray-500">
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
