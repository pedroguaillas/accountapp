<script setup lang="ts">
import PersonSelectModal from "./PersonSelectModal.vue";
import { MagnifyingGlassIcon } from "@heroicons/vue/24/solid";
import { ref, watch } from "vue";
import axios from "axios";
import { PeopleResponse, Person } from "@/types";

// Estado
const modal = ref(false);
const search = ref(""); // Texto del input
const searchModal = ref(""); // Texto del input
const identification = ref(""); // Identificación de la persona seleccionada
const people = ref<PeopleResponse>({
  data: [],
  last_page: 1,
  current_page: 1,
  total: 0,
});
const suggestion = ref<PeopleResponse>({
  data: [],
  last_page: 1,
  current_page: 1,
  total: 0,
});
const isDropdownOpen = ref(false);
const selectedPerson = ref<Person | null>(null);
const page = ref(1);

// Fetch de personas con paginación limitada (5 o 3 resultados)
const fetchPeople = async (search: string, page = 1, paginate = 10): Promise<PeopleResponse> => {
  try {
    const response = await axios.get(route("people.filters.index"), {
      params: { search, page, paginate },
    });
    return response.data;
  } catch (error) {
    console.error("Error al obtener personas:", error);
    return { data: [], last_page: 1, current_page: 1, total: 0 };
  }
};

// Abrir/Cerrar modal
const toggleModal = () => {
  modal.value = !modal.value;
};

// Seleccionar persona (desde dropdown o modal)
const handlePersonSelect = (person: Person) => {
  modal.value = false;
  search.value = person.name;
  identification.value = person.identification ?? '';
  isDropdownOpen.value = false;
  people.value = {
    data: [],
    last_page: 1,
    current_page: 1,
    total: 0,
  };
  selectedPerson.value = person; // Marcar persona como seleccionada
  console.log(selectedPerson.value.identification);
  emit("selectPerson", person);
};

// Detectar cambios en la búsqueda y hacer fetch solo si no hay persona seleccionada sugerencias
watch(search, async (newValue) => {
  if (!newValue) {
    selectedPerson.value = null;
    suggestion.value = {
      data: [],
      last_page: 1,
      current_page: 1,
      total: 0,
    };
    return;
  }
  suggestion.value = await fetchPeople(newValue, 1, 3); // Paginate = 3 para sugerencias
  isDropdownOpen.value = search.value.length > 0 && !selectedPerson.value;
});
//para cargar el inicio
watch(modal, async (newValue) => {
  if (newValue) {
    const response = await fetchPeople(""); // Obtener respuesta
    if (response && response.data) {
      people.value = response; // Asignar datos correctamente
      isDropdownOpen.value = false;
      search.value = "";
    } else {
      people.value = {
        data: [],
        last_page: 1,
        current_page: 1,
        total: 0,
      }; // En caso de error, asigna array vacío
    }
  }
});

watch(searchModal, async (newValue) => {
  if (!newValue) {
    selectedPerson.value = null;
    people.value = {
      data: [],
      last_page: 1,
      current_page: 1,
      total: 0,
    };
    return;
  }
  people.value = await fetchPeople(newValue);
});

watch(
  () => page.value,
  async (newPage) => {
    people.value = await fetchPeople(searchModal.value, newPage);
  }
);

// Cambiar de página
const nextPage = () => {
  if (page.value < people.value.last_page) {
    page.value++;
  }
};

const prevPage = () => {
  if (page.value > 1) {
    page.value--; // ✅ Esto activará automáticamente el `watch`
  }
};

const emit = defineEmits(["selectPerson"]);
</script>

<template>
  <div class="block w-full mt-2">
    <div class="flex">
      <!-- Campo de búsqueda con sugerencias -->
      <div class="flex-1 relative">
        <input v-model="search" type="search" class="border w-full border-gray-300 px-4 py-2 focus:outline-none"
          placeholder="Buscar persona..." @focus="isDropdownOpen = true" />
        <ul v-if="isDropdownOpen && suggestion.data.length"
          class="absolute z-10 bg-white border-b border-x border-gray-300 rounded-b w-full max-h-40 overflow-y-auto shadow-lg">
          <li v-for="person in suggestion.data" :key="person.id" @click="handlePersonSelect(person)"
            class="px-4 py-2 cursor-pointer hover:bg-gray-100">
            {{ person.name }}
          </li>
        </ul>
      </div>

      <!-- Botón para abrir el modal -->
      <button @click="toggleModal" class="w-[3em] bg-slate-500 text-white px-3 py-2 rounded-r hover:bg-slate-600">
        <MagnifyingGlassIcon class="size-6 text-white stroke-[3px]" />
      </button>
    </div>
  </div>

  <!-- Modal -->
  <PersonSelectModal :show="modal" :people="people" :search="searchModal" :page="page"
    @update:search="searchModal = $event" @close="toggleModal" @selectPerson="handlePersonSelect" @nextPage="nextPage"
    @prevPage="prevPage" />
</template>
