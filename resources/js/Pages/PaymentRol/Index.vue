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
import { PencilIcon, PrinterIcon, EnvelopeIcon } from "@heroicons/vue/24/solid";

// Props
const props = defineProps({
  paymentroles: { type: Object, default: () => ({}) },
  filters: { type: String, default: "" },
  roleingress: { type: Array, default: () => [] },
  roleegress: { type: Array, default: () => [] },
});

// Refs
const modal = ref(false);
const modal1 = ref(false);
const deleteid = ref(0);
const search = ref(""); // Término de búsqueda
const loading = ref(false); // Estado de carga
const hoveredRow = ref(null);

// Filtros de Año y Mes
const selectedYear = ref(new Date().getFullYear()); // Año por defecto
const selectedMonth = ref(new Date().getMonth()); // Mes seleccionado
const checkoptions = ref(false);
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

    <div v-if="checkoptions" >
      <ul>
        <li>Editar</li>
        <li>Eliminar</li>
        <li>Imprimir</li>
        <li>Mostrar</li>
        <li>Enviar</li>
      </ul>

    </div>

      <Table>
        <thead>
          <tr class="[&>th]:py-2">
            <th></th>
            <th class="w-1">N°</th>
            <th>CEDULA</th>
            <th class="text-left">NOMBRE</th>
            <th class="text-left">CARGO</th>
            <th>DIAS</th>
            <th class="text-right pr-4">INGRESOS FIJOS</th>
            <th class="text-right pr-4">OTROS INGRESOS</th>
            <th class="text-right pr-4">EGRESOS FIJOS</th>
            <th class="text-right pr-4">OTROS EGRESOS</th>
            <th class="text-right pr-4">SUELDO A RECIBIR</th>
          </tr>
        </thead>
        <tbody class="min-w-full table-auto">
          <tr
            v-for="(paymentrol, i) in props.paymentroles.data"
            :key="paymentrol.id"
            class="border-t relative [&>td]:py-4 group hover:bg-slate-200"
            @mouseenter="hoveredRow = paymentrol.id"
            @mouseleave="hoveredRow = null"
          >
            <td><input v-model="checkoptions" type="checkbox" /></td>
            <td>{{ i + 1 }}</td>
            <td>{{ paymentrol.cuit }}</td>
            <td class="text-left">{{ paymentrol.name }}</td>
            <td class="text-left">{{ paymentrol.position }}</td>
            <td>{{ paymentrol.days }}</td>
            <td class="text-right pr-4">{{ paymentrol.salary.toFixed(2) }}</td>
            <td class="text-right pr-4">{{ paymentrol.salary.toFixed(2) }}</td>
            <td class="text-right pr-4">{{ paymentrol.salary.toFixed(2) }}</td>
            <td class="text-right pr-4">{{ paymentrol.salary.toFixed(2) }}</td>
            <td class="text-right pr-4">{{ paymentrol.salary.toFixed(2) }}</td>

            <!-- Superposición de opciones -->
            <td
              v-if="hoveredRow === paymentrol.id"
              class=" flex justify-center absolute h-12 top-0 right-1/2  bg-white items-center shadow-md border rounded z-10 group-hover:block"
            >
              <ul class=" px-4 flex gap-4 rounded items-center justify-center">
                <li
                  @click="edit(paymentrol)"
                  class="m-0 cursor-pointer hover:text-blue-500"
                >
                  <PencilIcon class="size-5 text-red" />
                </li> 
                <li class="m-0 cursor-pointer hover:text-yellow-500">
                  <PrinterIcon class="size-5 text-red" />
                </li>
                <li class="m-0 cursor-pointer hover:text-purple-500">
                  <EnvelopeIcon class="size-5 text-red" />
                </li>
              </ul>
            </td>
          </tr>
        </tbody>
      </Table>
    </div>
    <Paginate :page="props.paymentroles" @page-change="handlePageChange" />
  </AdminLayout>

  <FormModal
    :show="modal"
    :paymentRol="paymentrol"
    :roleingress='roleingress'
    :roleegress='roleegress'
    :error="errorForm"
    @close="toggle"
    @save="save"
  />
</template>