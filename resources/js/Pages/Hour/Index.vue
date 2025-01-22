<script setup>
// Imports
import { ref, reactive, watch } from "vue";
import AdminLayout from "@/Layouts/AdminLayout.vue";
import ModalHour from "./ModalHour.vue";
import { router, useForm } from "@inertiajs/vue3";
import axios from "axios";
import Table from "@/Components/Table.vue";
import TextInput from "@/Components/TextInput.vue";
import Paginate from "@/Components/Paginate.vue";
import ConfirmationModal from "@/Components/ConfirmationModal.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import { TrashIcon, PencilIcon } from "@heroicons/vue/24/solid";

//Props
const props = defineProps({
  hours: { type: Object, default: () => ({}) },
  filters: { type: String, default: "" },
  employees: { type: Array, default: () => [] },
});

// Refs
const modal = ref(false);
const search = ref(""); // Término de búsqueda
const loading = ref(false); // Estado de carga
const modal1 = ref(false);

const date = new Date().toISOString().split("T")[0];
//El inicializador de objetos
const initialHour = {
  detail: "",
  amount: "",
  employee_id: "",
  type: "",
  date,
};

// Reactives
const hour = useForm({ ...initialHour });
const errorForm = reactive({});
const deleteid = ref(0);

const newHour = () => {
  // Reinicio el formularios con valores vacios
  if (hour.id !== undefined) {
    delete hour.id;
  }
  Object.assign(hour, initialHour);

  Object.assign(errorForm, { ...initialHour, date: "" });
  // Muestro el modal
  toggle();
};

const resetErrorForm = () => {
  Object.assign(errorForm, initialHour);
};

const toggle = () => {
  modal.value = !modal.value;
};

const toggle1 = () => {
  modal1.value = !modal1.value;
};

const save = () => {
  // Determinar el método HTTP y la ruta correspondiente
  const isUpdate = Boolean(hour.id);
  const routeMethod = isUpdate ? "put" : "post";
  const routeName = isUpdate
    ? route("hours.update", { id: hour.id }) // Ruta para actualización
    : route("hours.store"); // Ruta para creación

  // Preparar la solicitud
  hour[routeMethod](routeName, {
    onSuccess: () => {
      toggle(); // Cerrar modal o reiniciar estados
      resetErrorForm(); // Limpiar errores del formulario
      router.reload({ only: ["horas"] }); // Recargar datos
    },
    onError: (error) => {
      resetErrorForm(); // Asegurarte de limpiar los errores previos

      // Iterar sobre los errores recibidos del servidor
      Object.entries(error).forEach(([key, value]) => {
        errorForm[key] = value; // Mostrar el primer error asociado a cada campo
      });
    },
  });
};

const update = (hourEdit) => {
  resetErrorForm();
  Object.keys(hourEdit).forEach((key) => {
    hour[key] = hourEdit[key];
  });
  toggle();
};

const removeHour = (hourId) => {
  toggle1();
  deleteid.value = hourId;
};

const deletehour = () => {
  axios
    .delete(route("hours.delete", deleteid.value))
    .then(() => {
      router.visit(route("hours.index")); // Redirige a la ruta deseada
    })
    .catch((error) => {
      console.error("Error al eliminar la hora", error);
    });
};

watch(
  search,

  async (newQuery) => {
    const url = route("hours.index");
    loading.value = true;
    console.log("si entra");

    try {
      await router.get(
        url,
        { search: newQuery, page: props.hours.current_page }, // Mantener la página actual
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
  const url = route("hours.index"); // Ruta hacia el backend
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
  <AdminLayout title="Horas Extra">
    <!-- Card -->
    <div class="p-4 bg-white rounded drop-shadow-md">
      <!-- Card Header -->
      <div class="flex flex-col sm:flex-row justify-between items-center">
        <h2 class="text-sm sm:text-lg font-bold w-full pb-2 sm:pb-0">
          Horas Extra/Normales
        </h2>
        <div class="w-full flex justify-end">
          <TextInput
            v-model="search"
            type="text"
            class="mt-1 block w-[50%] mr-2 h-8"
            minlength="3"
            maxlength="300"
            required
            placeholder="Buscar..."
          />
        </div>
        <button
          @click="newHour"
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
              <th>CEDULA</th>
              <th class="text-left">NOMBRE</th>
              <th class="text-right pr-4">HORAS</th>
              <th class="w-1"></th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="(hour, i) in props.hours.data"
              :key="hour.id"
              class="border-t [&>td]:py-2"
            >
              <td>{{ i + 1 }}</td>
              <td>{{ hour.cuit }}</td>
              <td class="text-left">{{ hour.name }}</td>
              <td class="text-right pr-4">{{ hour.amount }}</td>

              <td class="flex justify-end">
                <div class="relative inline-flex gap-1">
                  <button
                    class="rounded px-1 py-1 bg-red-500 text-white"
                    @click="removeHour(hour.id)"
                  >
                    <TrashIcon class="size-6 text-white" />
                  </button>
                  <button
                    class="rounded px-2 py-1 bg-blue-500 text-white"
                    @click="update(hour)"
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
    <Paginate :page="props.hours" @page-change="handlePageChange" />
  </AdminLayout>

  <ModalHour
    :show="modal"
    :hour="hour"
    :error="errorForm"
    :employees="props.employees"
    @close="toggle"
    @save="save"
  />

  <ConfirmationModal :show="modal1">
    <template #title> ELIMINAR HORAS EXTRAS</template>
    <template #content> Esta seguro de eliminar la hora extra? </template>
    <template #footer>
      <SecondaryButton @click="modal1 = !modal1" class="mr-2"
        >Cancelar</SecondaryButton
      >
      <PrimaryButton type="button" @click="deletehour"
        >Aceptar</PrimaryButton
      >
    </template>
  </ConfirmationModal>
</template>