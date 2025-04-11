<script setup lang="ts">
// Imports
import { ref, reactive, watch } from "vue";
import AdminLayout from "@/Layouts/AdminLayout.vue";
import { router, useForm, Link } from "@inertiajs/vue3";
import axios from "axios";
import { ConfirmationModal, TextInput, SecondaryButton, PrimaryButton, Table, Paginate } from "@/Components";
import { TrashIcon, PencilIcon } from "@heroicons/vue/24/solid";
import { Employee, Filters, GeneralRequest } from "@/types";

// Props
const props = defineProps<{
  employees: GeneralRequest<Employee>; // Paginación de los bancos
  filters: Filters; // Filtros aplicados
}>();


// Refs
const modalDelete = ref(false);
const deleteId = ref<Number>(0);
const search = ref(props.filters.search); // Término de búsqueda

const toggleDelete = () => {
  modalDelete.value = !modalDelete.value;
};

const date = new Date().toISOString().split("T")[0];

// Inicializador de objetos
const initialEmployee = {
  id: undefined,
  cuit: "",
  name: "",
  sector_code: "",
  post: "",
  days: "",
  salary: "",
  porcent_aportation: "",
  is_a_parner: false,
  is_a_qualified_craftsman: false,
  affiliated_with_spouse: false,
  date_start: date,
};

const removeEmployee = (employeeId: Number) => {
  toggleDelete();
  deleteId.value = employeeId;
};

const deleteEmployee = () => {
  router.delete(route("employee.delete", deleteId.value), {
    onSuccess: () => {
      toggleDelete();
    },
    onError: (error) => {
      console.error("Error al eliminar el empleado", error);
    },
  });
};

watch(
  search,

  async (newQuery) => {
    const url = route("employee.index");

    try {
      await router.get(
        url,
        { search: newQuery, page: props.employees.current_page }, // Mantener la página actual
        { preserveState: true }
      );
    } catch (error) {
      console.error("Error al filtrar:", error);
    } 
  },
  { immediate: false }
);

// Función para manejar el cambio de página
const toggleState = (employeeId: Number, currentState: boolean) => {
  const newState = !currentState; // Inverse the current state (active to inactive and vice versa)

  axios
    .put(route("employee.updateState", { id: employeeId }), {
      state: newState,
    })
    .then(() => {
      // On success, reload the page or update the local data
      router.visit(route("employee.index"));
    })
    .catch((error) => {
      console.error("Error al cambiar el estado del empleado", error);
      alert("Ocurrió un error al cambiar el estado. Intenta de nuevo.");
    });
};
</script>

<template>
  <AdminLayout title="Empleados">
    <!-- Card -->
    <div class="p-4 bg-white rounded drop-shadow-md">
      <!-- Card Header -->
      <div class="flex flex-col sm:flex-row justify-between items-center">
        <h2 class="text-sm sm:text-lg font-bold w-full pb-2 sm:pb-0">
          Empleados
        </h2>
        <div class="w-full flex sm:justify-end">
          <TextInput v-model="search" type="search" class="block sm:mr-2 h-8 w-full" placeholder="Buscar ..." />
        </div>
        <Link :href="route('employee.create')"
          class="mt-2 sm:mt-0 px-2 bg-success dark:bg-green-600 hover:bg-successhover text-2xl text-white rounded font-bold">
        +
        </Link>
      </div>

      <Table>
        <thead>
          <tr class="[&>th]:py-2">
            <th class="w-1">N°</th>
            <th>CEDULA</th>
            <th class="text-left">NOMBRE</th>
            <th class="text-left">CARGO</th>
            <th>DIAS</th>
            <th class="text-right pr-4">SALARIO</th>
            <th>ESTADO</th>
            <th class="w-1"></th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(employee, i) in props.employees.data" :key="employee.id" class="border-t [&>td]:py-2">
            <td>{{ i + 1 }}</td>
            <td>{{ employee.cuit }}</td>
            <td class="text-left">{{ employee.name }}</td>
            <td class="text-left">{{ employee.position }}</td>
            <td>{{ employee.days }}</td>
            <td class="text-right pr-4">{{ employee.salary.toFixed(2) }}</td>

            <td>
              <button :class="employee.state ? 'bg-success' : 'bg-danger'"
                @click="() => employee.id && toggleState(employee.id, employee.state)"
                class="rounded px-2 py-1 text-white">
                {{ employee.state ? "Activo" : "Inactivo" }}
              </button>
            </td>
            <td class="flex justify-end">
              <div class="relative inline-flex gap-1">
                <button class="rounded px-1 py-1 bg-danger hover:bg-dangerhover text-white"
                  @click="() => employee.id && removeEmployee(employee.id)">
                  <TrashIcon class="size-6 text-white" />
                </button>
                <Link class="rounded px-2 pt-2 bg-primary hover:bg-primaryhover text-white"
                  :href="route('employee.edit', employee.id)">
                <PencilIcon class="size-4 text-white" />
                </Link>
              </div>
            </td>
          </tr>
        </tbody>
      </Table>
    </div>
    <Paginate :page="props.employees" />
  </AdminLayout>

  <ConfirmationModal :show="modalDelete">
    <template #title> ELIMINAR EMPLEADOS </template>
    <template #content> Esta seguro de eliminar el empleado? </template>
    <template #footer>
      <SecondaryButton @click="modalDelete = !modalDelete" class="mr-2">Cancelar</SecondaryButton>

      <PrimaryButton type="button" @click="deleteEmployee">Aceptar</PrimaryButton>
    </template>
  </ConfirmationModal>
</template>
