<script setup>
import DialogModal from "@/Components/DialogModal.vue";
import { ref, watch, computed } from "vue";
import axios from "axios";
import Table from "@/Components/Table.vue";
import TextInput from "@/Components/TextInput.vue";

const props = defineProps({
  show: { type: Boolean, default: false },
  people:{ type: Object, default: () => ({}) },
  search:{ type: String, default: ""},
});

const loading = ref(false);
const page = ref(1);
const totalPages = computed(() => props.people?.last_page ?? 1);

const emit = defineEmits(["close", "selectPerson"]);

// Cerrar la modal al hacer clic en una fila
const handlePersonSelect = (person) => {
  emit("selectPerson", person);
  emit("close"); // Cierra la modal
};

const nextPage = () => {
  if (page.value < totalPages.value) {
    page.value++;
    fetchPeople(); // Volver a obtener datos
  }
};

const prevPage = () => {
  if (page.value > 1) {
    page.value--;
    fetchPeople(); // Volver a obtener datos
  }
};

</script>

<template>
  <DialogModal :show="show" maxWidth="lg" @close="emit('close')">
    <template #title>Seleccionar una persona</template>
    <template #content>
      <div class="w-full flex sm:justify-between">
        <!-- Campo de búsqueda -->
        <TextInput
          v-model="props.search"
          type="text"
          placeholder="Buscar persona..."
          class="w-full p-2 border border-gray-300 rounded-md mb-2 text-sm"
        />
      </div>

      <!-- Tabla -->
      <Table class="mt-4 text-xs sm:text-sm table-auto w-full text-center text-gray-700">
        <thead>
          <tr class="[&>th]:py-2">
            <th class="w-1">N°</th>
            <th class="text-left">IDENTIFICACIÓN</th>
            <th class="text-left">NOMBRE</th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="(person, index) in props.people.data"
            :key="person.id"
            class="border-t [&>td]:py-2 cursor-pointer hover:bg-gray-100"
            @click="handlePersonSelect(person)"
          >
            <td>{{ index + 1 }}</td>
            <td class="text-left">{{ person.identification }}</td>
            <td class="text-left">{{ person.name }}</td>
          </tr>
          <tr v-if="loading">
            <td colspan="3" class="text-center py-4">Cargando...</td>
          </tr>
        </tbody>
      </Table>

      <!-- Paginación -->
      <div class="flex justify-between items-center mt-4">
        <button
          @click="props.people.prevPage"
          :disabled="page === 1"
          class="px-3 py-1 border rounded bg-gray-200 hover:bg-gray-300 disabled:opacity-50"
        >
          Anterior
        </button>
        <span class="text-sm">Página {{ page }} de {{ totalPages }}</span>
        <button
          @click="props.people.nextPage"
          :disabled="page === totalPages"
          class="px-3 py-1 border rounded bg-gray-200 hover:bg-gray-300 disabled:opacity-50"
        >
          Siguiente
        </button>
      </div>
    </template>
  </DialogModal>
</template>
