<script setup lang="ts">
// Imports
import AdminLayout from "@/Layouts/AdminLayout.vue";
import ModalCostCenter from "./ModalCostCenter.vue";
import { router, useForm } from "@inertiajs/vue3";
import { ref, reactive, watch } from "vue";
import axios from "axios";
import { ConfirmationModal, TextInput, SecondaryButton, PrimaryButton, Table, Paginate } from "@/Components";
import { TrashIcon, PencilIcon } from "@heroicons/vue/24/solid";
import { CostCenter } from "@/types/cost-center";
import { Errors, Filters, GeneralRequest } from "@/types";

// Props
const props = defineProps<{
  costCenters: GeneralRequest<CostCenter>; // Paginación de los bancos
  filters: Filters; // Filtros aplicados
}>();

// Refs
const modal = ref(false);
const search = ref(props.filters.search);

// Initial cost center object
const initialCostCenter = { id: undefined, name: "", code: "" };

// Reactives
const costCenter = useForm<CostCenter>({ ...initialCostCenter });
const errorForm = reactive<Errors>({});

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

const modalDelete = ref(false);
const deleteId = ref<Number>(0);

const toggleDelete = () => {
  modalDelete.value = !modalDelete.value;
};

const save = () => {
  // Determinar el método HTTP y la ruta correspondiente
  const isUpdate = Boolean(costCenter.id);
  const routeMethod = isUpdate ? "put" : "post";
  const routeName = isUpdate
    ? route("costCenter.update", { id: costCenter.id }) // Asegurarte de que uses el formato correcto
    : route("costCenter.store");

  // Preparar la solicitud
  costCenter[routeMethod](routeName, {
    onSuccess: () => {
      toggle(); // Cerrar modal o reiniciar estados
      resetErrorForm(); // Limpiar errores del formulario
      router.reload({ only: ["costCenters"] }); // Recargar datos
    },
    onError: (error: Errors) => {
      resetErrorForm(); // Asegurarte de limpiar los errores previos
      if (error.response?.data?.errors) {
        // Iterar sobre los errores recibidos del servidor
        Object.entries(error.response.data.errors).forEach(([key, value]) => {
          errorForm[key] = (value as string[])[0]; // Mostrar el primer error asociado a cada campo
        });
      } else {
        console.error("Error desconocido:", error);
        alert("Ocurrió un error inesperado. Por favor, intente nuevamente.");
      }
    },
  });
};

const update = (costCenterEdit: CostCenter) => {
  resetErrorForm();
  Object.assign(costCenter, costCenterEdit);
  toggle();
};

const removeCostCenter = (costCenterId: Number) => {
  toggleDelete();
  deleteId.value = costCenterId;
};

const deleteCostCenter = () => {
  router.delete(route("costCenter.delete", deleteId.value), {
    onSuccess: () => {
      toggleDelete();
    },
    onError: (error) => {
      console.error("Error al eliminar el centro de costo", error);
    },
  });
};

watch(
  search,
  async (newQuery) => {
    const url = route("costcenter.index");

    try {
      await router.get(
        url,
        { search: newQuery, page: props.costCenters.current_page }, // Mantener la página actual
        { preserveState: true }
      );
    } catch (error) {
      console.error("Error al filtrar:", error);
    }
  },
  { immediate: false }
);

const toggleState = (costcenterId: Number, currentState: Boolean) => {
  const newState = !currentState; // Inverse the current state (active to inactive and vice versa)
  axios
    .put(route("costCenter.updateState", { id: costcenterId }), {
      state: newState,
    })
    .then(() => {
      // On success, reload the page or update the local data
      router.visit(route("costcenter.index"));
    })
    .catch((error) => {
      console.error("Error al cambiar el estado del centro de costo", error);
      alert("Ocurrió un error al cambiar el estado. Intenta de nuevo.");
    });
};
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
          class="mt-2 sm:mt-0 px-2 bg-success dark:bg-green-600 hover:bg-successhover text-2xl text-white rounded font-bold">
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
              <th>ESTADO</th>
              <th class="w-1"></th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(costCenter, i) in props.costCenters.data" :key="costCenter.id" class="border-t [&>td]:py-2">
              <td>{{ i + 1 }}</td>
              <td>{{ costCenter.code }}</td>
              <td>{{ costCenter.name }}</td>
              <td>
                <button :class="costCenter.state ? 'bg-success' : 'bg-danger'"
                  @click="() => costCenter.id && costCenter.state && toggleState(costCenter.id, costCenter.state)"
                  class="rounded px-2 py-1 text-white">
                  {{ costCenter.state ? "Activo" : "Inactivo" }}
                </button>
              </td>
              <td class="flex justify-end">
                <div class="relative inline-flex gap-1">
                  <button class="rounded px-1 py-1 bg-danger hover:bg-dangerhover text-white"
                    @click="() => costCenter.id && removeCostCenter(costCenter.id)">
                    <TrashIcon class="size-6 text-white" />
                  </button>
                  <button class="rounded px-2 py-1 bg-primary hover:bg-primaryhover text-white"
                    @click="update(costCenter)">
                    <PencilIcon class="size-4 text-white" />
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </Table>
      </div>
    </div>
    <Paginate :page="props.costCenters" />
  </AdminLayout>

  <!-- Modal Component for CostCenter -->
  <ModalCostCenter :show="modal" :costCenter="costCenter" :error="errorForm" @close="toggle" @save="save" />
  <ConfirmationModal :show="modalDelete">
    <template #title> ELIMINAR CENTRO DE COSTOS </template>
    <template #content> Esta seguro de eliminar el centro de costo? </template>
    <template #footer>
      <SecondaryButton @click="modalDelete = !modalDelete" class="mr-2">Cancelar</SecondaryButton>

      <PrimaryButton type="button" @click="deleteCostCenter">Aceptar</PrimaryButton>
    </template>
  </ConfirmationModal>
</template>
