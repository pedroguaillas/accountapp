<script setup lang="ts">
// Imports
import { ref, reactive, computed, watch } from "vue";
import AdminLayout from "@/Layouts/AdminLayout.vue";
import FormModal from "./FormModal.vue";
import { router, useForm } from "@inertiajs/vue3";
import axios from "axios";
import { DynamicSelect, TextInput, Paginate, Table } from "@/Components";
import { PencilIcon, PrinterIcon, EnvelopeIcon, DocumentArrowDownIcon, } from "@heroicons/vue/24/solid";
import { Errors, Filters, GeneralRequest, PaymentRol, RoleEgress, RoleIngress } from "@/types";

// Props
const props = defineProps<{
  paymentroles: GeneralRequest<PaymentRol>; // Paginación de los bancos
  filters: Filters; // Filtros aplicados
}>();

// Inicializador de objetos
const initialPaymentRol = {};

// Reactives
const paymentrol = reactive<PaymentRol>({ ...initialPaymentRol });

const toggle = () => {
  modal.value = !modal.value;
};

const edit = (paymentrolEdit: PaymentRol) => {
  Object.assign(paymentrol, { ...initialPaymentRol, ...paymentrolEdit });
  toggle();
};

const save = () => {
  console.log(paymentrol.ingresses);

  if (!Array.isArray(paymentrol.ingresses)) {
    paymentrol.ingresses = Object.values(paymentrol.ingresses);
  }
  if (!Array.isArray(paymentrol.egresses)) {
    paymentrol.egresses = Object.values(paymentrol.egresses);
  }

  const updatedData = {
    ingresses: paymentrol.ingresses.map((ingress: RoleIngress) => ({
      id: ingress.id,
      value: ingress.value,
    })),
    egresses: paymentrol.egresses.map((egress: RoleEgress) => ({
      id: egress.id,
      value: egress.value,
    })),
  };

  axios
    .put(route("paymentrol.update", paymentrol.id), updatedData)
    .then((response) => {
      if (response.data.success) {
        toggle(); // Cerrar la modal después de guardar
        router.visit(route("paymentrol.index"));
      }
    })
    .catch((error) => {
      console.error("Error al actualizar:", error);
    });
};
// Refs
const modal = ref(false);
const search = ref(props.filters.search); // Término de búsqueda
const hoveredRow = ref<number | null>(null);

// Filtros de Año y Mes
const selectedYear = ref(props.filters.year); // Año por defecto
const selectedMonth = ref(props.filters.month); // Mes actual por defecto

// Generar un arreglo de años (puedes modificar el rango según lo necesites)
const years = [
  { value: "2023", label: "2023" },
  { value: "2024", label: "2024" },
  { value: "2025", label: "2025" },
];

// Generar un arreglo de meses
const months = computed(() => {
  return [
    { value: "1", label: "Enero" },
    { value: "2", label: "Febrero" },
    { value: "3", label: "Marzo" },
    { value: "4", label: "Abril" },
    { value: "5", label: "Mayo" },
    { value: "6", label: "Junio" },
    { value: "7", label: "Julio" },
    { value: "8", label: "Agosto" },
    { value: "9", label: "Septiembre" },
    { value: "10", label: "Octubre" },
    { value: "11", label: "Noviembre" },
    { value: "12", label: "Diciembre" },
  ];
});

watch(
  [search, selectedYear, selectedMonth], // Las dependencias observadas
  async (newQuery) => {
    const url = route("paymentrol.index");
    try {
      await router.get(
        url,
        {
          search: newQuery[0],
          year: newQuery[1],
          month: newQuery[2],
          page: props.paymentroles.current_page,
        },
        { preserveState: true }
      );
    } catch (error) {
      console.error("Error al filtrar:", error);
    }
  },
  { immediate: false }
);

const exportToExcel = () => {
  const url = route("paymentrol.export", {
    search: search.value,
    year: selectedYear.value,
    month: selectedMonth.value,
  });

  // Redirigir a la URL para descargar el archivo
  window.location.href = url;
};

const selectAll = ref(false); // Estado del checkbox principal
const selectedIds = ref<(number | undefined)[]>([]);

// Función para seleccionar/deseleccionar todos
const toggleSelectAll = () => {
  // Actualiza los elementos secundarios según el estado actual de selectAll
  if (selectAll.value) {
    selectedIds.value = props.paymentroles.data.map((item) => item.id);
  } else {
    selectedIds.value = [];
  }
};

// Watcher para sincronizar el checkbox principal con los secundarios
watch(
  selectedIds,
  (newValue) => {
    selectAll.value = newValue.length === props.paymentroles.data.length;
  },
  { deep: true }
);

const generateJournals = () => {
  router.post(
    route("paymentrol.journal.generate"),
    { selectedIds: selectedIds.value },
    {
      onError: (errors: Errors) => {
        if (errors.response && errors.response.status === 412) {
          // Capturar el mensaje de error enviado desde el backend
          console.log(errors.response.data.error); // "Debe generar el ASIENTO DE SITUACION INICIAL"
          alert(errors.response.data.error); // Mostrarlo como alerta (o usa tu propia notificación)
        } else {
          console.log("Error inesperado:", errors);
        }
      },
    }
  );
};

const printAction = () => {
  console.log('Acción de impresión');
  // Aquí puedes agregar la lógica para la acción de impresión, por ejemplo, hacer una solicitud para generar un documento o abrir un cuadro de diálogo de impresión.
};
const envelopeAction = () => {
  console.log('Acción de impresión');
}

</script>


<template>
  <AdminLayout title="Roles de pago">
    <!-- Card -->
    <div class="p-4 bg-white rounded drop-shadow-md">
      <!-- Card Header -->
      <div class="flex flex-col sm:flex-row justify-between items-center">
        <h2 class="text-sm sm:text-lg font-bold w-full pb-2 sm:pb-0">
          Roles de pagos
        </h2>
        <div class="w-full flex sm:justify-end gap-2">
          <!-- Filtro Año -->
          <DynamicSelect class="block w-full" v-model="selectedYear" :options="years" :seleccione="false" autofocus />

          <!-- Filtro Mes -->

          <DynamicSelect class="block w-full" v-model="selectedMonth" :options="months" :seleccione="false" autofocus />

          <TextInput v-model="search" type="search" class="block w-full" placeholder="Buscar ..." />

          <button v-if="props.paymentroles.data.length > 0" @click="exportToExcel"
            class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-700">
            <DocumentArrowDownIcon class="size-6 text-red" />
          </button>

          <button class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-700" @click="generateJournals">
            GENERAR
          </button>
        </div>
      </div>

      <Table>
        <thead>
          <tr class="[&>th]:py-2">
            <th>
              <input type="checkbox" v-model="selectAll" @change="toggleSelectAll" />
            </th>
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
            <th class="text-right pr-4">ESTADO</th>
          </tr>
        </thead>
        <tbody class="min-w-full table-auto">
          <tr v-for="(paymentrol, i) in props.paymentroles.data" :key="paymentrol.id"
            class="border-t relative [&>td]:py-4 group hover:bg-slate-200"
            @mouseenter="() => paymentrol.id && (hoveredRow = paymentrol.id)" @mouseleave="hoveredRow = null">
            <td>
              <input type="checkbox" v-model="selectedIds" :value="paymentrol.id" />
            </td>
            <td>{{ i + 1 }}</td>
            <td>{{ paymentrol.cuit }}</td>
            <td class="text-left">{{ paymentrol.name }}</td>
            <td class="text-left">{{ paymentrol.position }}</td>
            <td>{{ paymentrol.days }}</td>
            <td class="text-right pr-4">
              {{ paymentrol.total_ingress_f.toFixed(2) }}
            </td>
            <td class="text-right pr-4">
              {{ paymentrol.total_ingress_o.toFixed(2) }}
            </td>
            <td class="text-right pr-4">
              {{ paymentrol.total_egress_f.toFixed(2) }}
            </td>
            <td class="text-right pr-4">
              {{ paymentrol.total_egress_o.toFixed(2) }}
            </td>
            <td class="text-right pr-4">
              {{ paymentrol.salary_receive !== undefined ? paymentrol.salary_receive.toFixed(2) : 'N/A' }}
            </td>
            <td class="text-right pr-4">
              {{ paymentrol.state }}
            </td>

            <!-- Superposición de opciones -->
            <td v-if="hoveredRow === paymentrol.id"
              class="flex justify-center absolute h-12 top-0 right-1/2 bg-white items-center shadow-md border rounded z-10 group-hover:block">
              <ul class="px-4 flex gap-4 rounded items-center justify-center">
                <li @click=" paymentrol.state !== 'GENERADO' && edit(paymentrol)" :class="{
                  'cursor-not-allowed text-gray-400':
                    paymentrol.state === 'GENERADO',
                  'hover:text-yellow-500 cursor-pointer':
                    paymentrol.state !== 'GENERADO',
                }">
                  <PencilIcon class="size-5 text-red" />
                </li>
                <li :class="{
                  'cursor-not-allowed text-gray-400':
                    paymentrol.state === 'CREADO',
                  'hover:text-yellow-500 cursor-pointer':
                    paymentrol.state !== 'CREADO',
                }" @click="paymentrol.state !== 'CREADO' && printAction()">
                  <PrinterIcon class="size-5 text-red" />
                </li>
                <li :class="{
                  'cursor-not-allowed text-gray-400':
                    paymentrol.state === 'CREADO',
                  'hover:text-purple-500 cursor-pointer':
                    paymentrol.state !== 'CREADO',
                }" @click="paymentrol.state !== 'CREADO' && envelopeAction()">
                  <EnvelopeIcon class="size-5 text-red" />
                </li>
              </ul>
            </td>
          </tr>
        </tbody>
      </Table>
    </div>
    <Paginate :page="props.paymentroles" />
  </AdminLayout>

  <FormModal :show="modal" :paymentRol="paymentrol" @close="toggle" @save="save" />
</template>