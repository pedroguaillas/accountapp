<script setup>
// Imports
import { ref, reactive, watch } from "vue";
import AdminLayout from "@/Layouts/AdminLayout.vue";
import FormModal from "./FormModal.vue";
import { router, useForm } from "@inertiajs/vue3";
import Table from "@/Components/Table.vue";
import axios from "axios";
import ConfirmationModal from "@/Components/ConfirmationModal.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import Paginate from "@/Components/Paginate.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import { TrashIcon, PencilIcon } from "@heroicons/vue/24/solid";

// Props
const props = defineProps({
  branches: { type: Object, default: () => ({}) },
  filters: { type: Object, default: () => ({}) },
});

// Refs
const modal = ref(false);
const modal1 = ref(false);
const deleteid = ref(0);
const search = ref(props.filters.search); // Término de búsqueda
const loading = ref(false); // Estado de carga

const toggle1 = () => {
  modal1.value = !modal1.value;
};

// Inicializador de objetos
const initialBranch = {
  number: "",
  name: "",
  city: "",
  address: "",
  is_matriz: false,
  enviroment_type: "",
};

// Reactives
const branch = useForm({ ...initialBranch });
const errorForm = reactive({ ...initialBranch });

const newBranch = () => {
  if (branch.id !== undefined) {
    delete branch.id;
  }
  Object.assign(branch, initialBranch);
  toggle();
};

const resetErrorForm = () => {
  Object.assign(errorForm, initialBranch);
};

const toggle = () => {
  modal.value = !modal.value;
};

const save = () => {
  // Validar campos obligatorios antes de enviar la solicitud
  if (!branch.number || !branch.name || !branch.city || !branch.address) {
    alert("Por favor, complete todos los campos obligatorios");
    return;
  }
  // Determinar el método HTTP y la ruta correspondiente
  const isUpdate = Boolean(branch.id);
  const routeMethod = isUpdate ? "put" : "post";
  const routeName = isUpdate
    ? route("branch.update", { id: branch.id }) // Ruta para actualización
    : route("branch.store"); // Ruta para creación

  // Preparar la solicitud
  branch[routeMethod](routeName, {
    onSuccess: () => {
      toggle(); // Cerrar modal o reiniciar estados
      resetErrorForm(); // Limpiar errores del formulario
      router.reload({ only: ["stores"] }); // Recargar datos
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

const update = (branchEdit) => {
  resetErrorForm();
  Object.keys(branchEdit).forEach((key) => {
    branch[key] = branchEdit[key];
  });
  toggle();
};

const removeBranch = (branchId) => {
  toggle1();
  deleteid.value = branchId;
};

const deleteBranch = () => {
  router.delete(route('branch.delete', deleteid.value), {
    onSuccess: () => {
      toggle1();
    },
    onError: (error) => {
      console.error('Error al eliminar la sucursal', error);
    },
  });
};

watch(
  search,

  async (newQuery) => {
    const url = route("branch.index");
    loading.value = true;
    console.log("si entra");

    try {
      await router.get(
        url,
        { search: newQuery, page: props.branches.current_page }, // Mantener la página actual
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
  const url = route("branch.index"); // Ruta hacia el backend
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

const toggleState = (branchId, currentState) => {
  const newState = !currentState; // Inverse the current state (active to inactive and vice versa)

  axios
    .put(route("branch.updateState", { id: branchId }), { state: newState })
    .then(() => {
      // On success, reload the page or update the local data
      router.visit(route("branch.index"));
    })
    .catch((error) => {
      console.error("Error al cambiar el estado de la sucursal", error);
      alert("Ocurrió un error al cambiar el estado. Intenta de nuevo.");
    });
};

</script>

<template>
  <AdminLayout title="Sucursales / establecimientos">
    <!-- Card -->
    <div class="p-4 bg-white rounded drop-shadow-md">
      <!-- Card Header -->
      <div class="flex flex-col sm:flex-row justify-between items-center">
        <h2 class="text-sm sm:text-lg font-bold w-full pb-2 sm:pb-0">
          Sucursales / establecimientos
        </h2>
        <div class="w-full flex sm:justify-end">
          <TextInput
            v-model="search"
            type="search"
            class="block sm:mr-2 h-8 w-full"
            placeholder="Buscar ..."
          />
        </div>
        <button
          @click="newBranch"
          class="mt-2 sm:mt-0 px-2 bg-success dark:bg-green-600 hover:bg-successhover text-2xl text-white rounded font-bold"
        >
          +
        </button>
      </div>

      <Table>
        <thead>
          <tr class="[&>th]:py-2">
            <th class="w-1">N°</th>
            <th>ESTAB</th>
            <th>NOMBRE</th>
            <th>CIUDAD</th>
            <th>DIRECCION</th>
            <th>MATRIZ</th>
            <th>AMBIENTE</th>
            <th>ESTADO</th>
            <th class="w-1"></th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="(branch, i) in props.branches.data"
            :key="branch.id"
            class="border-t [&>td]:py-2"
          >
            <td>{{ i + 1 }}</td>
            <td>{{ branch.number }}</td>
            <td>{{ branch.name }}</td>
            <td>{{ branch.city }}</td>
            <td>{{ branch.address }}</td>
            <td>{{ branch.is_matriz ? "Matriz" : "Sucursal" }}</td>
            <td>
              {{ branch.enviroment_type === 1 ? "Prueba" : "Producción" }}
            </td>
            <td >
              <button
                :class="branch.state ? 'bg-success' : 'bg-danger'"
                @click="toggleState(branch.id, branch.state)"
                class="rounded px-2 py-1 text-white"
              >
                {{ branch.state ? "Activo" : "Inactivo" }}
              </button>
            </td>
            <td class="flex justify-end">
              <div class="relative inline-flex gap-1">
                <button
                  class="rounded px-1 py-1 bg-danger hover:bg-dangerhover text-white"
                  @click="removeBranch(branch.id)"
                >
                  <TrashIcon class="size-6 text-white" />
                </button>
                <button
                  class="rounded px-2 py-1 bg-primary hover:bg-primaryhover text-white"
                  @click="update(branch)"
                >
                  <PencilIcon class="size-4 text-white" />
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </Table>
    </div>
    <Paginate :page="props.branches" @page-change="handlePageChange" />
  </AdminLayout>

  <FormModal
    :show="modal"
    :branch="branch"
    :error="errorForm"
    @close="toggle"
    @save="save"
  />

  <ConfirmationModal :show="modal1">
    <template #title> ELIMINAR ESTABLECIMIENTOS </template>
    <template #content> Esta seguro de eliminar el establecimiento? </template>
    <template #footer>
      <SecondaryButton @click="modal1 = !modal1" class="mr-2"
        >Cancelar</SecondaryButton
      >
      <PrimaryButton type="button" @click="deleteBranch">Aceptar</PrimaryButton>
    </template>
  </ConfirmationModal>
</template>
