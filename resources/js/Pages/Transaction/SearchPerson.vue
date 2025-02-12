<script setup>
import PersonSelectModal from "./PersonSelectModal.vue";
import { MagnifyingGlassIcon } from "@heroicons/vue/24/solid";
import { ref, computed, watch } from "vue";
import axios from "axios";

// Estado
const modal = ref(false);
const search = ref(""); // Texto del input
const searchModal = ref(""); // Texto del input
const identification = ref(""); // Identificación de la persona seleccionada
const people = ref({}); // Sugerencias obtenidas del backend
const suggestion = ref([]);
const isDropdownOpen = ref(false);
const selectedPerson = ref(null); // Controla si ya se seleccionó una persona

// Fetch de personas con paginación limitada (5 o 3 resultados)
const fetchPeople = async () => {
  if (!search.value || selectedPerson.value) {
    people.value = [];
    return; // Retorna siempre un objeto válido
  }
  try {
    console.log("hola");
    const response = await axios.get(route("people.filters.index"), {
      params: { search: search.value, page: 1, paginate: 3 },
    });
    return response; // Retornar la respuesta completa
  } catch (error) {
    console.error("Error al obtener personas:", error);
    return { data: {} }; // Manejar error retornando un objeto vacío
  }
};

// Abrir/Cerrar modal
const toggleModal = () => {
  modal.value = !modal.value;
};

// Seleccionar persona (desde dropdown o modal)
const handlePersonSelect = (person) => {
  search.value = person.name;
  identification.value = person.identification;
  isDropdownOpen.value = false;
  people.value = []; // Limpiar sugerencias
  selectedPerson.value = person; // Marcar persona como seleccionada

  emit("selectPerson", person);
};

// Detectar cambios en la búsqueda y hacer fetch solo si no hay persona seleccionada
watch(search, async (newValue) => {
  if (!newValue) {
    selectedPerson.value = null; // Permitir nuevas búsquedas si se borra el campo
  }
  const { data } = await fetchPeople();
  suggestion.value = data;
  isDropdownOpen.value = search.value.length > 0 && !selectedPerson.value;
});

watch(searchModal, async (newValue) => {
  if (!newValue) {
    selectedPerson.value = null; // Permitir nuevas búsquedas si se borra el campo
  }
  person.value = await fetchPeople();
  isDropdownOpen.value = search.value.length > 0 && !selectedPerson.value;
});

watch(modal, async (newValue) => {
  if (newValue) {
    
    const response = await fetchPeople(); // Obtener respuesta
    console.log(response);
    if (response) {
      people.value = response.data; // Asignar solo si hay datos
    }
  }
});

// Cambiar de página
const nextPage = () => {
  if (page.value < totalPages.value) {
    page.value++;
    fetchPeople();
  }
};

const prevPage = () => {
  if (page.value > 1) {
    page.value--;
    fetchPeople();
  }
};



const emit = defineEmits(["selectPerson"]);
</script>

<template>
  <div class="block w-full mt-2">
    <div class="flex">
      <!-- Campo para identificación -->
      <div
        class="w-[10em] border-y border-l border-gray-300 text-gray-500 px-4 py-2"
      >
        {{ identification || "Identificación" }}
      </div>

      <!-- Campo de búsqueda con sugerencias -->
      <div class="flex-1 relative">
        <input
          v-model="search"
          type="search"
          class="border w-full border-gray-300 px-4 py-2 focus:outline-none"
          placeholder="Buscar persona..."
          @focus="isDropdownOpen = true"
        />
        <ul
          v-if="isDropdownOpen && suggestion.length"
          class="absolute z-10 bg-white border-b border-x border-gray-300 rounded-b w-full max-h-40 overflow-y-auto shadow-lg"
        >
          <li
            v-for="person in suggestion"
            :key="person.id"
            @click="handlePersonSelect(person)"
            class="px-4 py-2 cursor-pointer hover:bg-gray-100"
          >
            {{ person.name }}
          </li>
        </ul>
      </div>

      <!-- Botón para abrir el modal -->
      <button
        @click="toggleModal"
        class="w-[3em] bg-slate-500 text-white px-3 py-2 rounded-r hover:bg-slate-600"
      >
        <MagnifyingGlassIcon class="size-6 text-white stroke-[3px]" />
      </button>
    </div>
  </div>

  <!-- Modal -->
  <PersonSelectModal
    :show="modal"
    :people="people"
    :search="searchModal"
    @close="toggleModal"
    @selectPerson="handlePersonSelect"
  />
</template>
