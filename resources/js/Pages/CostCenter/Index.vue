<script setup>
// Imports
import AdminLayout from "@/Layouts/AdminLayout.vue";
import ModalCostCenter from "./ModalCostCenter.vue";
import { router } from "@inertiajs/vue3";
import { ref, reactive, watch } from "vue";
import axios from "axios";
import Table from "@/Components/Table.vue";

// Props
const props = defineProps({
  costCenters: { type: Object, default: () => ({ data: [] }) }, // Default empty array to avoid undefined errors
  filters: { type: String, default: "" },
});

// Refs
const modal = ref(false);
const search = ref(props.filters);

// Initial cost center object
const initialCostCenter = { name: "", code: "", type: "", state: "" };

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

const save = () => {
  // Validating input fields before sending the request
  if (!costCenter.name || !costCenter.code || !costCenter.type) {
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
  if (confirm("¿Estás seguro de que deseas eliminar este centro de costos?")) {
    axios
      .delete(route("costCenter.delete", costCenterId))
      .then(() => {
        router.reload({ only: ["costCenters"] });
      })
      .catch((error) => {
        console.error("Error al eliminar el centro de costos", error);
      });
  }
};

const loading = ref(false);

// Watch for search changes
watch(
  search,
  async (newQuery) => {
    // Solo activar la búsqueda si la longitud es al menos 1
    const url = route("costcenter.index"); // Obtener la URL con la función route

    loading.value = true; // Activar el estado de carga

    try {
      // Realizar la solicitud GET utilizando router.get
      const response = await router.get(
        url,
        {
          search: newQuery, // Pasar los parámetros de búsqueda
        },
        { preserveState: true } //otros parametros
      );
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
      <div class="flex justify-between items-center">
        <h2 class="text-sm sm:text-lg font-bold">Centro de costos</h2>
        <button
          @click="newCostCenter"
          class="px-2 bg-green-500 dark:bg-green-600 text-2xl text-white rounded font-bold"
        >
          +
        </button>
      </div>
      <!-- Buscar -->
      Buscar
      <input v-model="search" type="search" name="search" />

      <!-- Resposive -->
      <div class="w-full overflow-x-auto">
        <!-- Tabla -->
        <Table>
          <thead>
            <tr class="[&>th]:py-2">
              <th class="w-1">N°</th>
              <th>CODIGO</th>
              <th>NOMBRE</th>
              <th>TIPO</th>
              <th class="w-1"></th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="(costCenter, i) in props.costCenters.data"
              :key="costCenter.id"
              class="border-t [&>td]:py-2"
            >
              <td>{{ i + 1 }}</td>
              <td>{{ costCenter.code }}</td>
              <td>{{ costCenter.name }}</td>
              <td>{{ costCenter.type }}</td>
              <td>
                <div
                  class="relative inline-flex [&>a>i]:text-white [&>button>i]:text-white"
                >
                  <button
                    class="rounded px-2 py-1 bg-red-500 text-white"
                    @click="removeCostCenter(costCenter.id)"
                  >
                    <i class="fa fa-trash"></i> Eliminar
                  </button>

                  <button
                    class="rounded px-2 py-1 bg-blue-500 text-white"
                    @click="update(costCenter)"
                  >
                    <i class="fa fa-edit"></i> Modificar
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </Table>
      </div>
    </div>
  </AdminLayout>

  <!-- Modal Component for CostCenter -->
  <ModalCostCenter
    :show="modal"
    :costCenter="costCenter"
    :error="errorForm"
    @close="toggle"
    @save="save"
  />
</template>
