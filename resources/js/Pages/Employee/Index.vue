<script setup>
// Imports
import { ref, reactive, watch } from "vue";
import AdminLayout from "@/Layouts/AdminLayout.vue";
import FormModal from "./FormModal.vue";
import { router, useForm, Link } from "@inertiajs/vue3";
import Table from "@/Components/Table.vue";
import axios from "axios";
import ConfirmationModal from "@/Components/ConfirmationModal.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import Paginate from "@/Components/Paginate.vue";
import { TrashIcon, PencilIcon } from "@heroicons/vue/24/solid";

// Props
const props = defineProps({
  employees: { type: Object, default: () => ({}) },
  filters: { type: String, default: "" },
});

// Refs
const modal = ref(false);
const modal1 = ref(false);
const deleteid = ref(0);
const search = ref(""); // Término de búsqueda
const loading = ref(false); // Estado de carga

const toggle1 = () => {
  modal1.value = !modal1.value;
};

const date = new Date().toISOString().split("T")[0];

// Inicializador de objetos
const initialEmployee = {
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

// Reactives
const employee = useForm({ ...initialEmployee });
const errorForm = reactive({});

const newEmployee = () => {
  if (employee.id !== undefined) {
    delete employee.id;
  }
  Object.assign(employee, initialEmployee);
  Object.assign(errorForm, { ...initialEmployee, date_start: "" });
  toggle();
};

const resetErrorForm = () => {
  Object.assign(errorForm, initialEmployee);
};

const toggle = () => {
  modal.value = !modal.value;
};

const edit = (employeeEdit) => {
  resetErrorForm();
  Object.assign(employee, { ...initialEmployee, ...employeeEdit });
  Object.assign(errorForm, { ...initialEmployee, date_start: "" });
  toggle();
};

const removeEmployee = (employeeId) => {
  toggle1();
  deleteid.value = employeeId;
};

const deleteEmployee = () => {
  axios
    .delete(route("employee.delete", deleteid.value)) // Eliminar centro de costos
    .then(() => {
      // Después de eliminar el centro de costos, redirigir a la ruta deseada
      router.visit(route("employee.index"));
    })
    .catch((error) => {
      console.error("Error al eliminar al empleado", error);
    });
};

watch(
  search,

  async (newQuery) => {
    const url = route("employee.index");
    loading.value = true;
    console.log("si entra");

    try {
      await router.get(
        url,
        { search: newQuery, page: props.employees.current_page }, // Mantener la página actual
        { preserveState: true }
      );
    } catch (error) {
      console.error("Error al filtrar:", error);
    } finally {
      loading.value = false;
    }
  },
  { immediate: false }
);

// Función para manejar el cambio de página
const handlePageChange = async (page) => {
  const url = route("employee.index"); // Ruta hacia el backend
  loading.value = true;

  try {
    await router.get(
      url,
      { page, search: search.value }, // Incluye tanto la página como el término de búsqueda
      { preserveState: true }
    );
  } catch (error) {
    console.error("Error al paginar:", error);
  } finally {
    loading.value = false;
  }
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
          <TextInput
            v-model="search"
            type="search"
            class="block sm:mr-2 h-8 w-full"
            placeholder="Buscar ..."
          />
        </div>
        <Link
          :href="route('employee.create')"
          class="mt-2 sm:mt-0 px-2 bg-green-500 dark:bg-green-600 text-2xl text-white rounded font-bold"
        >
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
            <th class="w-1"></th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="(employee, i) in props.employees.data"
            :key="employee.id"
            class="border-t [&>td]:py-2"
          >
            <td>{{ i + 1 }}</td>
            <td>{{ employee.cuit }}</td>
            <td class="text-left">{{ employee.name }}</td>
            <td class="text-left">{{ employee.position }}</td>
            <td>{{ employee.days }}</td>
            <td class="text-right pr-4">{{ employee.salary.toFixed(2) }}</td>
            <td class="flex justify-end">
              <div class="relative inline-flex gap-1">
                <button
                  class="rounded px-1 py-1 bg-red-500 text-white"
                  @click="removeEmployee(employee.id)"
                >
                  <TrashIcon class="size-6 text-white" />
                </button>
                <Link
                  class="rounded px-2 py-1 bg-blue-500 text-white"
                  :href="route('employee.edit',employee.id)"
                >
                  <PencilIcon class="size-4 text-white" />
                </Link>
              </div>
            </td>
          </tr>
        </tbody>
      </Table>
    </div>
    <Paginate :page="props.employees" @page-change="handlePageChange" />
  </AdminLayout>

  <FormModal
    :show="modal"
    :employee="employee"
    :error="errorForm"
    @close="toggle"
    @save="save"
  />

  <ConfirmationModal :show="modal1">
    <template #title> ELIMINAR EMPLEADOS </template>
    <template #content> Esta seguro de eliminar el empleado? </template>
    <template #footer>
      <PrimaryButton type="button" @click="deleteEmployee"
        >Aceptar</PrimaryButton
      >
    </template>
  </ConfirmationModal>
</template>
