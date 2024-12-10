<script setup>
// Imports
import AdminLayout from "@/Layouts/AdminLayout.vue";
import ModalCostCenter from "./ModalCostCenter.vue";
import { router, useForm } from "@inertiajs/vue3";
import { ref, reactive, watch } from "vue";
import axios from "axios";
import Table from "@/Components/Table.vue";
import ConfirmationModal from "@/Components/ConfirmationModal.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import Paginate from "@/Components/Paginate.vue";
import { TrashIcon, PencilIcon } from "@heroicons/vue/24/solid";

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
const costCenter = useForm({ ...initialCostCenter });
const errorForm = reactive({});

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

//   const routeMethod = costCenter.id ? "put" : "post";
// const routeName = costCenter.id
//   ? route("costCenter.update", costCenter.id)
//   : route("costCenter.store");

// axios[routeMethod](routeName, costCenter)
// .then(() => {
//   toggle();
//   resetErrorForm();
//   router.reload({ only: ["costCenters"] });
// })
// .catch((error) => {
//   resetErrorForm();
//   if (error.response) {
//     Object.keys(error.response.data.errors).forEach((key) => {
//       errorForm[key] = error.response.data.errors[key][0];
//     });
//   } else {
//     console.error("Error desconocido", error);
//     alert("Hubo un error al procesar la solicitud");
//   }
// });
const save = () => {
  // Validar los campos obligatorios
  if (!costCenter.name || !costCenter.code) {
    alert("Por favor, complete todos los campos requeridos.");
    return;
  }

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
    onError: (error) => {
      resetErrorForm(); // Asegurarte de limpiar los errores previos
      if (error.response?.data?.errors) {
        // Iterar sobre los errores recibidos del servidor
        Object.entries(error.response.data.errors).forEach(([key, value]) => {
          errorForm[key] = value[0]; // Mostrar el primer error asociado a cada campo
        });
      } else {
        console.error("Error desconocido:", error);
        alert("Ocurrió un error inesperado. Por favor, intente nuevamente.");
      }
    },
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
    const url = route("costcenter.index");
    loading.value = true;

    try {
      await router.get(
        url,
        { search: newQuery, page: props.costCenters.current_page }, // Mantener la página actual
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
  const url = route("costcenter.index"); // Ruta hacia el backend
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
          <TextInput
            v-model="search"
            type="text"
            class="block sm:mr-2 h-8 w-full"
            placeholder="Buscar"
          />
        </div>
        <button
          @click="newCostCenter"
          class="mt-2 sm:mt-0 px-2 bg-green-500 dark:bg-green-600 text-2xl text-white rounded font-bold"
        >
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
            <tr
              v-for="(costCenter, i) in props.costCenters.data"
              :key="costCenter.id"
              class="border-t [&>td]:py-2"
            >
              <td>{{ i + 1 }}</td>
              <td>{{ costCenter.code }}</td>
              <td>{{ costCenter.name }}</td>

              <td class="flex justify-end">
                <div class="relative inline-flex gap-1">
                  <button
                    class="rounded px-1 py-1 bg-red-500 text-white"
                    @click="removeCostCenter(costCenter.id)"
                  >
                    <TrashIcon class="size-6 text-white" />
                  </button>
                  <button
                    class="rounded px-2 py-1 bg-blue-500 text-white"
                    @click="update(costCenter)"
                  >
                    <PencilIcon class="size-4 text-white" />
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </Table>
      </div>
    </div>
    <Paginate :page="props.costCenters" @page-change="handlePageChange" />
  </AdminLayout>

  <!-- Modal Component for CostCenter -->
  <ModalCostCenter
    :show="modal"
    :costCenter="costCenter"
    :error="errorForm"
    @close="toggle"
    @save="save"
  />
  <ConfirmationModal :show="modal1">
    <template #title> ELIMINAR CENTRO DE COSTOS </template>
    <template #content> Esta seguro de eliminar el centro de costo? </template>
    <template #footer>
      <PrimaryButton type="button" @click="deletecostcenter"
        >Aceptar</PrimaryButton
      >
    </template>
  </ConfirmationModal>
</template>
