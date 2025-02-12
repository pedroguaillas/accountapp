<script setup>
import { MagnifyingGlassIcon } from "@heroicons/vue/24/solid";
import DialogModal from "@/Components/DialogModal.vue";
import { ref, computed, watch } from "vue";
import axios from "axios";
import Table from "@/Components/Table.vue";
import TextInput from "@/Components/TextInput.vue";

const props = defineProps({
  show: { type: Boolean, default: false },
});
// Refs
const search = ref("");
const identification = ref("");
const isDropdownOpen = ref(false);
const people = ref([]);
const currentPage = ref(1);
const lastPage = ref(1);
const loading = ref(false);

// Función para obtener personas desde el backend
const fetchPeople = async (page = 1) => {
  loading.value = true;
  console.log("En este momento");
  try {
    const response = await axios.get(route("people.filters.index"), {
      params: { search: search.value, page },
    });

    people.value = response.data.data;
    currentPage.value = response.data.current_page;
    lastPage.value = response.data.last_page;
  } catch (error) {
    console.error("Error al obtener personas:", error);
  } finally {
    loading.value = false;
  }
};

watch(
  () => props.show,
  (newValue) => {
    if (newValue) {
      fetchPeople(); // Llama a la función cuando el modal se abre
    }
  }
);

// Verificar cambios en la búsqueda y cargar resultados
watch(search, () => {
  fetchPeople();
  isDropdownOpen.value = true;
});

// Método para seleccionar una persona
const handlePersonSelect = (person) => {
  search.value = person.search;
  identification.value = person.identification;
  isDropdownOpen.value = false;
  emit("selectPerson", person);
};

// Función para cambiar de página
const handlePageChange = (page) => {
  if (page > 0 && page <= lastPage.value) {
    fetchPeople(page);
  }
};

// Control de eventos
const emit = defineEmits(["selectPerson"]);
</script>
<template>
  <DialogModal :show="show" maxWidth="lg" @close="emit('close')">
    <template #title>Seleccionar una persona</template>
    <template #content>
      <div class="w-full flex sm:justify-end">
        <TextInput
          v-model="search"
          type="text"
          placeholder="Buscar persona..."
          class="w-full p-2 border border-gray-300 rounded-md mb-2 text-sm"
        />
      </div>
      <div class="mt-0">
        <Table
          class="mt-4 text-xs sm:text-sm table-auto w-full text-center text-gray-700"
        >
          <thead>
            <tr class="[&>th]:py-2">
              <th class="w-1">N°</th>
              <th class="text-left">IDENTIFICACIÓN</th>
              <th class="text-left">NOMBRE</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="(person, index) in people"
              :key="person.id"
              class="border-t [&>td]:py-2 cursor-pointer hover:bg-gray-100"
              @click="handlePersonSelect(person)"
            >
              <td>{{ index + 1 }}</td>
              <td class="text-left">{{ person.identification }}</td>
              <td class="text-left">{{ person.name }}</td>
            </tr>
          </tbody>
        </Table>

        <!-- Paginación -->
        <div
          v-if="people.length"
          class="flex justify-between px-4 py-2 bg-gray-100"
        >
          <button
            @click="handlePageChange(currentPage - 1)"
            :disabled="currentPage === 1"
            class="text-blue-500"
          >
            Anterior
          </button>
          <span>Página {{ currentPage }} de {{ lastPage }}</span>
          <button
            @click="handlePageChange(currentPage + 1)"
            :disabled="currentPage === lastPage"
            class="text-blue-500"
          >
            Siguiente
          </button>
        </div>
      </div>
    </template>
  </DialogModal>
</template>
