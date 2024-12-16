<script setup>
// Imports
import { ref, reactive, computed, watch } from "vue";
import AdminLayout from "@/Layouts/AdminLayout.vue";
import FormModal from "./FormModal.vue";
import { router, useForm, Link } from "@inertiajs/vue3";
import DynamicSelect from "@/Components/DynamicSelect.vue";
import Table from "@/Components/Table.vue";
import axios from "axios";
import ConfirmationModal from "@/Components/ConfirmationModal.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import Paginate from "@/Components/Paginate.vue";
import { TrashIcon, PencilIcon } from "@heroicons/vue/24/solid";

// Props
const props = defineProps({
  paymentroles: { type: Object, default: () => ({}) },
  filters: { type: String, default: "" },
});

// Refs
const modal = ref(false);
const modal1 = ref(false);
const deleteid = ref(0);
const search = ref(""); // Término de búsqueda
const loading = ref(false); // Estado de carga

// Filtros de Año y Mes
const selectedYear = ref(new Date().getFullYear()); // Año por defecto
const selectedMonth = ref(new Date().getMonth()); // Mes seleccionado

// Generar un arreglo de años (puedes modificar el rango según lo necesites)

const years = [
  { value: "2023", label: "2023" },
  { value: "2024", label: "2024" },
];

// Generar un arreglo de meses
const months = computed(() => {
  return [
    { value: "01", label: "Enero" },
    { value: "02", label: "Febrero" },
    { value: "03", label: "Marzo" },
    { value: "04", label: "Abril" },
    { value: "05", label: "Mayo" },
    { value: "06", label: "Junio" },
    { value: "07", label: "Julio" },
    { value: "08", label: "Agosto" },
    { value: "09", label: "Septiembre" },
    { value: "10", label: "Octubre" },
    { value: "11", label: "Noviembre" },
    { value: "12", label: "Diciembre" },
  ];
});

// Función para aplicar los filtros de Año y Mes
const filterDataByYearAndMonth = () => {
  const year = selectedYear.value;
  const month = selectedMonth.value;

  // Filtra los datos según el año y mes seleccionados
  // Este es un ejemplo, asegúrate de tener acceso a los datos adecuados para filtrarlos
  axios
    .get(route("paymentrol.index"), {
      params: {
        year,
        month,
      },
    })
    .then((response) => {
      // Actualizar los roles de pago con los datos filtrados
      props.paymentroles = response.data;
    })
    .catch((error) => {
      console.error("Error al filtrar los roles de pago:", error);
    });
};

// Inicializador de objetos
const initialPaymentRol = {};

// Reactives
const paymentrol = useForm({ ...initialPaymentRol });
const errorForm = reactive({});

const newPaymentRol = () => {
  if (paymentrol.id !== undefined) {
    delete paymentrol.id;
  }
  Object.assign(paymentrol, initialPaymentRol);
  Object.assign(errorForm, { ...initialPaymentRol });
  toggle();
};

const resetErrorForm = () => {
  Object.assign(errorForm, initialPaymentRol);
};

const toggle = () => {
  modal.value = !modal.value;
};

const edit = (paymentrolEdit) => {
  resetErrorForm();
  Object.assign(paymentrol, { ...initialPaymentRol, ...paymentrolEdit });
  Object.assign(errorForm, { ...initialPaymentRol, date_start: "" });
  toggle();
};

const removePaymentRol = (paymentrolId) => {
  toggle1();
  deleteid.value = paymentrolId;
};

const deletePaymentRol = () => {
  axios
    .delete(route("paymentrol.delete", deleteid.value)) // Eliminar centro de costos
    .then(() => {
      // Después de eliminar el centro de costos, redirigir a la ruta deseada
      router.visit(route("paymentrol.index"));
    })
    .catch((error) => {
      console.error("Error al eliminar el rol", error);
    });
};

// Función para manejar el cambio de página
const handlePageChange = async (page) => {
  const url = route("paymentrol.index"); // Ruta hacia el backend
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

// Función para manejar la búsqueda
watch(
  search,
  async (newQuery) => {
    const url = route("paymentrol.index");
    loading.value = true;

    try {
      await router.get(
        url,
        { search: newQuery, page: props.paymentroles.current_page }, // Mantener la página actual
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

</script>

<template>
  <AdminLayout title="Roles de pago">
    <!-- Card -->
    <div class="p-4 bg-white rounded drop-shadow-md">
      <!-- Card Header -->
      <div class="flex flex-col sm:flex-row justify-between items-center">
        <h2 class="text-sm sm:text-lg font-bold w-full pb-2 sm:pb-0">
          Roles de Pago
        </h2>
        <div class="w-full flex sm:justify-end gap-2">
          <!-- Filtro Año -->
          <DynamicSelect
            class="mt-1 block w-full"
            v-model="selectedYear"
            :options="years"
            :seleccione="false"
            autofocus
          />

          <!-- Filtro Mes -->

          <DynamicSelect
            class="mt-1 block w-full"
            v-model="selectedMonth"
            :options="months"
            :seleccione="false"
            autofocus
          />

          <TextInput
            v-model="search"
            type="search"
            class="block sm:mr-2 w-full"
            placeholder="Buscar ..."
          />
        </div>
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
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="(paymentrol, i) in props.paymentroles.data"
            :key="paymentrol.id"
            class="border-t [&>td]:py-2" 
          >
            <td>{{ i + 1 }}</td>
            <td>{{ paymentrol.cuit }}</td>
            <td class="text-left">{{ paymentrol.name }}</td>
            <td class="text-left">{{ paymentrol.position }}</td>
            <td>{{ paymentrol.days }}</td>
            <td class="text-right pr-4">{{ paymentrol.salary.toFixed(2) }}</td>
          </tr>
        </tbody>
      </Table>
    </div>
    <Paginate :page="props.paymentroles" @page-change="handlePageChange" />
  </AdminLayout>

  <FormModal
    :show="modal"
    :paymentrol="paymentrol"
    :error="errorForm"
    @close="toggle"
    @save="save"
  />

  <ConfirmationModal :show="modal1">
    <template #title> ELIMINAR ROLES DE PAGOS </template>
    <template #content> Esta seguro de eliminar el rol de pago? </template>
    <template #footer>
      <PrimaryButton type="button" @click="deletePaymentRol">
        Aceptar
      </PrimaryButton>
    </template>
  </ConfirmationModal>
</template>
