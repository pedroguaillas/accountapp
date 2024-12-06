<script setup>
// Imports
import AdminLayout from "@/Layouts/AdminLayout.vue";
import ModalCostCenter from "./ModalCostCenter.vue";
import { router } from "@inertiajs/vue3";
import { ref, reactive, watch } from "vue";
import axios from "axios";
import Table from "@/Components/Table.vue";
import ConfirmationModal from "@/Components/ConfirmationModal.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import Paginate from "@/Components/Paginate.vue";

// Props
const props = defineProps({
  costCenters: { type: Object, default: () => ({ data: [] }) }, // Default empty array to avoid undefined errors
  filters: { type: String, default: "" },
});

// Refs
const modal = ref(false);
const search = ref(props.filters);

// Initial cost center object
const initialCostCenter = { name: "", code: "", state: "" };

// Reactives
const costCenter = reactive({ ...initialCostCenter });
const errorForm = reactive({ ...initialCostCenter });

// Functions
const newCostCenter = () => {
  if (costCenter.id !== undefined) {
    delete costCenter.id;
  }
  Object.assign(costCenter, initialCostCenter);
  toggle();
};

const resetErrorForm = () => {
  Object.assign(errorForm, initialCostCenter);
};

const toggle = () => {
  modal.value = !modal.value;
};

const modal1 = ref(false);
const deleteid = ref(0);

const toggle1 = () => {
  modal1.value = !modal1.value;
};

const save = () => {
  // Validating input fields before sending the request
  if (!costCenter.name || !costCenter.code) {
    alert("Por favor, complete todos los campos");
    return;
  }

  const routeMethod = costCenter.id ? "put" : "post";
  const routeName = costCenter.id
    ? route("costCenter.update", costCenter.id)
    : route("costCenter.store");

  axios[routeMethod](routeName, costCenter)
    .then(() => {
      toggle();
      resetErrorForm();
      router.reload({ only: ["costCenters"] });
    })
    .catch((error) => {
      resetErrorForm();
      if (error.response) {
        Object.keys(error.response.data.errors).forEach((key) => {
          errorForm[key] = error.response.data.errors[key][0];
        });
      } else {
        console.error("Error desconocido", error);
        alert("Hubo un error al procesar la solicitud");
      }
    });
};

const update = (costCenterEdit) => {
  resetErrorForm();
  Object.assign(costCenter, costCenterEdit);
  toggle();
};

const removeCostCenter = (costCenterId) => {
  toggle1();
  deleteid.value = costCenterId;
};

const deletecostcenter = () => {
  axios
    .delete(route("costCenter.delete", deleteid.value)) // Eliminar centro de costos
    .then(() => {
      // Después de eliminar el centro de costos, redirigir a la ruta deseada
      router.visit(route("costcenter.index"));
    })
    .catch((error) => {
      console.error("Error al eliminar el centro de costos", error);
    });
};

const loading = ref(false);

watch(
  search,
  async (newQuery) => {
    const url = route("costcenter.index"); // Obtener la URL con la función route
    loading.value = true; // Activar el estado de carga

    try {
      if (newQuery.length === 0) {
        // Si el término de búsqueda está vacío, recargar todos los registros
        await router.get(
          url,
          {}, // Sin parámetros de búsqueda
          { preserveState: true }
        );
      } else if (newQuery.length >= 1) {
        // Si hay término de búsqueda, realizar la solicitud con el parámetro
        await router.get(
          url,
          { search: newQuery }, // Pasar los parámetros de búsqueda
          { preserveState: true }
        );
      }
    } catch (error) {
      console.log(error);
    } finally {
      // Finalizar el estado de carga
      loading.value = false;
    }
  },
  { immediate: false } // Ejecutar inmediatamente al montar el componente
);

// Watch for search changes
</script>

<template>
  <AdminLayout title="Centro de costos">
    <!-- Card -->
    <div class="p-4 bg-white rounded drop-shadow-md">
      <!-- Card Header -->
      <div class="flex flex-col sm:flex-row justify-between items-center">
        <h2 class="text-sm sm:text-lg font-bold w-full pb-2 sm:pb-0">
          Centro de costos
        </h2>
        <div class="w-full flex sm:justify-end">
          <TextInput v-model="search" type="text" class="block sm:mr-2 h-8 w-full" placeholder="Buscar" />
        </div>
        <button @click="newCostCenter"
          class="mt-2 sm:mt-0 px-2 bg-green-500 dark:bg-green-600 text-2xl text-white rounded font-bold">
          +
        </button>
      </div>

      <!-- Resposive -->
      <div class="w-full overflow-x-auto">
        <!-- Tabla -->
        <Table>
          <thead>
            <tr class="[&>th]:py-2">
              <th class="w-1">N°</th>
              <th>CODIGO</th>
              <th>NOMBRE</th>
              <th class="w-1"></th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(costCenter, i) in props.costCenters.data" :key="costCenter.id" class="border-t [&>td]:py-2">
              <td>{{ i + 1 }}</td>
              <td>{{ costCenter.code }}</td>
              <td>{{ costCenter.name }}</td>
              <td>
                <div class="relative inline-flex [&>a>i]:text-white [&>button>i]:text-white">
                  <button class="rounded px-2 py-1 bg-red-500 text-white" @click="removeCostCenter(costCenter.id)">
                    <i class="fa fa-trash"></i> Eliminar
                  </button>

                  <button class="rounded px-2 py-1 bg-blue-500 text-white" @click="update(costCenter)">
                    <i class="fa fa-edit"></i> Modificar
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </Table>
        <Paginate :page="props.costCenters"/>
      </div>
    </div>
  </AdminLayout>

  <!-- Modal Component for CostCenter -->
  <ModalCostCenter :show="modal" :costCenter="costCenter" :error="errorForm" @close="toggle" @save="save" />
  <ConfirmationModal :show="modal1">
    <template #title> ELIMINAR CENTRO DE COSTOS </template>
    <template #content> Esta seguro de eliminar el centro de costo? </template>
    <template #footer>
      <PrimaryButton type="button" @click="deletecostcenter">Aceptar</PrimaryButton>
    </template>
  </ConfirmationModal>
</template>
