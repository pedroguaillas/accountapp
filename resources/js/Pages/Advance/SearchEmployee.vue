<script setup lang="ts">
import EmployeeSelectModal from "./EmployeeSelectModal.vue";
import { MagnifyingGlassIcon } from "@heroicons/vue/24/solid";
import { ref, watch } from "vue";
import axios from "axios";
import { GeneralRequest, Employee } from "@/types";

// Estado
const modal = ref(false);
const search = ref<string>("");
const searchModal = ref(""); // Texto del input
//const identification = ref(""); // Identificación de la persona seleccionada
const employees = ref<GeneralRequest<Employee> | null>(null); // Sugerencias obtenidas del backend
const suggestion = ref<GeneralRequest<Employee> | null>(null);
const isDropdownOpen = ref(false);
const selectedEmployees = ref<Employee | null>(null); // Controla si ya se seleccionó una persona
const page = ref(1);

// Fetch de personas con paginación limitada (5 o 3 resultados)
const fetchEmployee = async (search: string, page = 1, paginate = 10) => {
  try {
    const response = await axios.get(route("employees.filters.index"), {
      params: { search, page, paginate },
    });
    return response.data;
  } catch (error) {
    console.error("Error al obtener los empleados:", error);
    return { data: [], last_page: 1 };
  }
};

// Abrir/Cerrar modal
const toggleModal = () => {
  modal.value = !modal.value;
};

// Seleccionar persona (desde dropdown o modal)
const handleEmployeeSelect = (person: Employee) => {
  modal.value = false;
  search.value = person.name;
  //identification.value = person.identification;
  isDropdownOpen.value = false;
  employees.value = null; // Limpiar sugerencias
  selectedEmployees.value = person; // Marcar persona como seleccionada
  emit("selectEmployees", person);
};

// Detectar cambios en la búsqueda y hacer fetch solo si no hay persona seleccionada sugerencias
watch(search, async (newValue) => {
  if (!newValue) {
    selectedEmployees.value = null;
    suggestion.value = null; // Asegura que suggestion.data siempre existe
    return;
  }
  const response = await fetchEmployee(newValue, 1, 3); // Paginate = 3 para sugerencias
  suggestion.value = response ?? { data: [] }; // Asegura que siempre tenga data
  isDropdownOpen.value = search.value.length > 0 && !selectedEmployees.value;
});
//para cargar el inicio
watch(modal, async (newValue) => {
  if (newValue) {
    const response = await fetchEmployee(""); // Obtener respuesta
    if (response && response.data) {
      employees.value = response; // Asignar datos correctamente
      isDropdownOpen.value = false;
      search.value = "";
    } else {
      employees.value = null; // En caso de error, asigna array vacío
    }
  }
});

watch(searchModal, async (newValue) => {
  if (!newValue) {
    selectedEmployees.value = null;
    employees.value = null;
    return;
  }
  employees.value = await fetchEmployee(newValue);
});

watch(
  () => page.value,
  async (newPage) => {
    employees.value = await fetchEmployee(searchModal.value, newPage);
  }
);

// Cambiar de página
const nextPage = () => {
  if (employees.value?.last_page != null && page.value < employees.value.last_page) {
    page.value++;
  }
};
const prevPage = () => {
  if (page.value > 1) {
    page.value--; // ✅ Esto activará automáticamente el `watch`
  }
};

const emit = defineEmits(["selectEmployees"]);
</script>

<template>
  <div class="block w-full mt-2">
    <div class="flex">
      <!-- Campo de búsqueda con sugerencias -->
      <div class="flex-1 relative">
        <input v-model="search" type="search" class="border w-full border-gray-300 px-4 py-2 focus:outline-none"
          placeholder="Buscar persona..." @focus="isDropdownOpen = true" />
        <ul v-if="isDropdownOpen && suggestion?.data?.length"
          class="absolute z-10 bg-white border-b border-x border-gray-300 rounded-b w-full max-h-40 overflow-y-auto shadow-lg">
          <li v-for="employee in suggestion.data" :key="employee.id" @click="handleEmployeeSelect(employee)"
            class="px-4 py-2 cursor-pointer hover:bg-gray-100">
            {{ employee.name }}
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
  <EmployeeSelectModal :show="modal" v-if="employees !== null" :employees="employees" :search="searchModal" :page="page"
    @update:search="searchModal = $event" @close="toggleModal" @selectEmployees="handleEmployeeSelect"
    @nextPage="nextPage" @prevPage="prevPage" />
</template>
