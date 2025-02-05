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
  movementtypes: { type: Object, default: () => ({}) },
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

// Inicializador de objetos
const initialMovementType = {
  name: "",
  type: "",
};

// Reactives
const movementtype = useForm({ ...initialMovementType });
const errorForm = reactive({ ...initialMovementType });

const newMovementType = () => {
  if (movementtype.id !== undefined) {
    delete movementtype.id;
  }
  Object.assign(movementtype, initialMovementType);
  toggle();
};

const resetErrorForm = () => {
  Object.assign(errorForm, initialMovementType);
};

const toggle = () => {
  modal.value = !modal.value;
};

const save = () => {
  // Determinar el método HTTP y la ruta correspondiente

  const isUpdate = Boolean(movementtype.id);
  const routeMethod = isUpdate ? "put" : "post";
  const routeName = isUpdate
    ? route("movementtypes.update", { id: movementtype.id }) // Ruta para actualización
    : route("movementtypes.store"); // Ruta para creación

  // Preparar la solicitud
  movementtype[routeMethod](routeName, {
    onSuccess: () => {
      toggle(); // Cerrar modal o reiniciar estados
      resetErrorForm(); // Limpiar errores del formulario
      router.reload({ only: ["movementtypes"] }); // Recargar datos
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

const update = (movementTypeEdit) => {
  resetErrorForm();
  Object.keys(movementTypeEdit).forEach((key) => {
    movementtype[key] = movementTypeEdit[key];
  });
  toggle();
};

const removeMovementType = (movementtypeId) => {
  toggle1();
  deleteid.value = movementtypeId;
};

const deleteMovementType = () => {
  axios
    .delete(route("movementtypes.delete", deleteid.value)) // Eliminar centro de costos
    .then(() => {
      // Después de eliminar el centro de costos, redirigir a la ruta deseada
      router.visit(route("movementtypes.index"));
    })
    .catch((error) => {
      console.error("Error al eliminar a tipo de movimiento", error);
    });
};

watch(
  search,

  async (newQuery) => {
    const url = route("movementtypes.index");
    loading.value = true;
    try {
      await router.get(
        url,
        { search: newQuery, page: props.movementtypes.current_page }, // Mantener la página actual
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
  const url = route("movementtypes.index"); // Ruta hacia el backend
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
  <AdminLayout title="Tipos de movimientos Bancarios">
    <!-- Card -->
    <div class="p-4 bg-white rounded drop-shadow-md">
      <!-- Card Header -->
      <div class="flex flex-col sm:flex-row justify-between items-center">
        <h2 class="text-sm sm:text-lg font-bold w-full pb-2 sm:pb-0">
          Tipos de movimientos bancarios
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
          @click="newMovementType"
          class="mt-2 sm:mt-0 px-2 bg-success dark:bg-green-600 hover:bg-successhover text-2xl text-white rounded font-bold"
        >
          +
        </button>
      </div>

      <Table>
        <thead>
          <tr class="[&>th]:py-2">
            <th class="w-1">N°</th>
            <th class="text-left p-2">NOMBRE</th>
            <th>TIPO</th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="(movementtype, i) in props.movementtypes.data"
            :key="movementtype.id"
            class="border-t [&>td]:py-2"
          >
            <td>{{ i + 1 }}</td>
            <td class="text-left p-2">{{ movementtype.name }}</td>
            <td>{{ movementtype.type }}</td>
            <td class="flex justify-end">
              <div class="relative inline-flex gap-1">
                <button
                  class="rounded px-1 py-1 bg-danger hover:bg-dangerhover text-white"
                  @click="removeMovementType(movementtype.id)"
                >
                  <TrashIcon class="size-6 text-white" />
                </button>
                <button
                  class="rounded px-2 py-1 bg-primary hover:bg-primaryhover text-white"
                  @click="update(movementtype)"
                >
                  <PencilIcon class="size-4 text-white" />
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </Table>
    </div>
    <Paginate :page="props.movementtypes" @page-change="handlePageChange" />
  </AdminLayout>

  <FormModal
    :show="modal"
    :movementtype="movementtype"
    :error="errorForm"
    @close="toggle"
    @save="save"
  />

  <ConfirmationModal :show="modal1">
    <template #title> ELIMINAR TIPO DE MOVIMIENTO BANCARIO </template>
    <template #content>
      Esta seguro de eliminar el movimiento bancario?
    </template>
    <template #footer>
      <SecondaryButton @click="modal1 = !modal1" class="mr-2"
        >Cancelar</SecondaryButton
      >
      <PrimaryButton type="button" @click="deleteMovementType"
        >Aceptar</PrimaryButton
      >
    </template>
  </ConfirmationModal>
</template>
