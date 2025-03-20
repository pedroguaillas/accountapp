<script setup>
// Imports
import { ref, reactive, watch } from "vue";
import AdminLayout from "@/Layouts/AdminLayout.vue";
import ModalAdvance from "./ModalAdvance.vue";
import { router, useForm, Link } from "@inertiajs/vue3";
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
  advances: { type: Object, default: () => ({}) },
  filters: { type: Object, default: () => ({}) },
  employees: { type: Array, default: () => [] },
});

// Refs
const modal = ref(false);
const search = ref(props.filters.search); // Término de búsqueda
const loading = ref(false); // Estado de carga
const modal1 = ref(false);

const date = new Date().toISOString().split("T")[0];
//El inicializador de objetos
const initialAdvance = {
  detail: "",
  amount: "",
  employee_id: "",
  type: "",
  payment_type: "",
};

// Reactives
const advance = useForm({ ...initialAdvance,date });
const errorForm = reactive({});
const deleteid = ref(0);

const newAdvance = () => {
  // Reinicio el formularios con valores vacios
  if (advance.id !== undefined) {
    delete advance.id;
  }
  Object.assign(advance, { ...initialAdvance, date });
  resetErrorForm();
  // Muestro el modal
  toggle();
};

const resetErrorForm = () => {
  Object.assign(errorForm, initialAdvance);
};

const toggle = () => {
  modal.value = !modal.value;
};

const toggle1 = () => {
  modal1.value = !modal1.value;
};

const save = () => {
  // Determinar el método HTTP y la ruta correspondiente
  const isUpdate = Boolean(advance.id);
  const routeMethod = isUpdate ? "put" : "post";
  const routeName = isUpdate
    ? route("advances.update", { id: advance.id }) // Ruta para actualización
    : route("advances.store"); // Ruta para creación

  // Preparar la solicitud
  advance[routeMethod](routeName, {
    onSuccess: () => {
      toggle(); // Cerrar modal o reiniciar estados
      resetErrorForm(); // Limpiar errores del formulario
      router.reload({ only: ["adelantos"] }); // Recargar datos
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

const update = (advanceEdit) => {
  resetErrorForm();
  Object.keys(advanceEdit).forEach((key) => {
    advance[key] = advanceEdit[key];
  });
  toggle();
};

const removeAdvance = (advanceId) => {
  toggle1();
  deleteid.value = advanceId;
};

const deleteadvance = () => {
  axios
    .delete(route("advances.delete", deleteid.value))
    .then(() => {
      router.visit(route("advances.index")); // Redirige a la ruta deseada
    })
    .catch((error) => {
      console.error("Error al eliminar el adelanto", error);
    });
};

watch(
  search,

  async (newQuery) => {
    const url = route("advances.index");
    loading.value = true;
    console.log("si entra");

    try {
      await router.get(
        url,
        { search: newQuery, page: props.advances.current_page }, // Mantener la página actual
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
  const url = route("advances.index"); // Ruta hacia el backend
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
  <AdminLayout title="Adelantos">
    <!-- Card -->
    <div class="p-4 bg-white rounded drop-shadow-md">
      <!-- Card Header -->
      <div class="flex flex-col sm:flex-row justify-between items-center">
        <h2 class="text-sm sm:text-lg font-bold w-full pb-2 sm:pb-0">
          Adelantos de sueldo
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
         <Link
          :href="route('advances.create')"
          class="mt-2 sm:mt-0 px-2 bg-success dark:bg-green-600 hover:bg-successhover text-2xl text-white rounded font-bold"
        >
          +
        </Link>
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
              <th class="text-right pr-4">ADELANTO</th>
              <th class="w-1"></th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="(advance, i) in props.advances.data"
              :key="advance.id"
              class="border-t [&>td]:py-2"
            >
              <td>{{ i + 1 }}</td>
              <td>{{ advance.cuit }}</td>
              <td class="text-left">{{ advance.name }}</td>
              <td class="text-right pr-4">{{ advance.amount }}</td>

              <td class="flex justify-end">
                <div class="relative inline-flex gap-1">
                  <button
                    class="rounded px-1 py-1 bg-danger hover:bg-dangerhover text-white"
                    @click="removeAdvance(advance.id)"
                  >
                    <TrashIcon class="size-6 text-white" />
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </Table>
      </div>
    </div>
    <Paginate :page="props.advances" @page-change="handlePageChange" />
  </AdminLayout>

  <ModalAdvance
    :show="modal"
    :advance="advance"
    :error="errorForm"
    :employees="props.employees"
    :date="date"
    @close="toggle"
    @save="save"
  />

  <ConfirmationModal :show="modal1">
    <template #title> ELIMINAR ADELANTOS</template>
    <template #content> Esta seguro de eliminar el adelanto? </template>
    <template #footer>
      <SecondaryButton @click="modal1 = !modal1" class="mr-2"
        >Cancelar</SecondaryButton
      >
      <PrimaryButton type="button" @click="deleteadvance"
        >Aceptar</PrimaryButton
      >
    </template>
  </ConfirmationModal>
</template>