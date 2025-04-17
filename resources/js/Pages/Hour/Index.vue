<script setup lang="ts">
// Imports
import { ref, reactive, watch } from "vue";
import AdminLayout from "@/Layouts/AdminLayout.vue";
import ModalHour from "./ModalHour.vue";
import { router, useForm } from "@inertiajs/vue3";
import { ConfirmationModal, TextInput, SecondaryButton, PrimaryButton, Table, Paginate } from "@/Components";
import { TrashIcon, PencilIcon } from "@heroicons/vue/24/solid";
import { Employee, Errors, Filters, GeneralRequest, Hour } from "@/types";
import { getEcuadorDate } from "@/helpers/dateHelper";
//Props
const props = defineProps<{
  hours: GeneralRequest<Hour>; // Paginación de los bancos
  filters: Filters; // Filtros aplicados
  employees: Employee[];
}>();

// Refs
const modal = ref(false);
const search = ref(props.filters.search); // Término de búsqueda
const modalDelete = ref(false);
const date = getEcuadorDate();

//El inicializador de objetos
const initialHour = {
  id: undefined,
  detail: "",
  amount: "",
  employee_id: 0,
  type: "",
};

// Reactives
const hour = useForm<Hour>({ ...initialHour, date });
const errorForm = reactive<Errors>({});
const deleteid = ref<Number>(0);

const newHour = () => {
  resetErrorForm();
  // Reinicio el formularios con valores vacios
  if (hour.id !== undefined) {
    delete hour.id;
  }
  Object.assign(hour, { ...initialHour, date });

  resetErrorForm();
  // Muestro el modal
  toggle();
};

const resetErrorForm = () => {
  for (const key in errorForm) {
    errorForm[key] = "";
  }
};

const toggle = () => {
  modal.value = !modal.value;
};

const toggleDelete = () => {
  modalDelete.value = !modalDelete.value;
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
      router.reload({ only: ["horas"] }); // Recargar datos
    },
    onError: (error: Errors) => {
      resetErrorForm(); // Asegurarte de limpiar los errores previos
      if (error) {
        // Iterar sobre los errores recibidos del servidor
        Object.entries(error).forEach(([key, value]) => {
          errorForm[key] = value;// Mostrar el primer error asociado a cada campo
        });
      } else {
        console.error("Error desconocido:", error);
        alert("Ocurrió un error inesperado. Por favor, intente nuevamente.");
      }
    },
  });
};

const update = (hourEdit: Hour) => {
  resetErrorForm();
  Object.keys(hourEdit).forEach((key) => {
    hour[key] = hourEdit[key];
  });
  toggle();
};

const removeHour = (hourId: Number) => {
  toggleDelete();
  deleteid.value = hourId;
};

const deleteHour = () => {
  router.delete(route("hours.delete", deleteid.value), {
    onSuccess: () => {
      toggleDelete();
    },
    onError: (error) => {
      console.error("Error al eliminar las horas", error);
    },
  });
};

watch(
  search,
  async (newQuery) => {
    const url = route("hours.index");
    try {
      await router.get(
        url,
        { search: newQuery, page: props.hours.current_page }, // Mantener la página actual
        { preserveState: true }
      );
    } catch (error) {
      console.error("Error al filtrar:", error);
    }
  },
  { immediate: false }
);
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
          <TextInput v-model="search" type="text" class="mt-1 block w-[50%] mr-2 h-8" minlength="3" maxlength="300"
            required placeholder="Buscar..." />
        </div>
        <button @click="newHour"
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
              <th>CEDULA</th>
              <th class="text-left">NOMBRE</th>
              <th class="text-right pr-4">HORAS</th>
              <th class="w-1"></th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(hour, i) in props.hours.data" :key="hour.id" class="border-t [&>td]:py-2">
              <td>{{ i + 1 }}</td>
              <td>{{ hour.cuit }}</td>
              <td class="text-left">{{ hour.name }}</td>
              <td class="text-right pr-4">{{ hour.amount }}</td>

              <td class="flex justify-end">
                <div class="relative inline-flex gap-1">
                  <button class="rounded px-1 py-1 bg-danger hover:bg-dangerhover text-white"
                    @click="() => hour.id && removeHour(hour.id)">
                    <TrashIcon class="size-6 text-white" />
                  </button>
                  <button class="rounded px-2 py-1 bg-primary hover:bg-primaryhover text-white" @click="update(hour)">
                    <PencilIcon class="size-4 text-white" />
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </Table>
      </div>
    </div>
    <Paginate :page="props.hours" />
  </AdminLayout>

  <ModalHour :show="modal" :hour="hour" :error="errorForm" :employees="props.employees" :date="date" @close="toggle"
    @save="save" />

  <ConfirmationModal :show="modalDelete">
    <template #title> ELIMINAR HORAS EXTRAS</template>
    <template #content> Esta seguro de eliminar la hora extra? </template>
    <template #footer>
      <SecondaryButton @click="modalDelete = !modalDelete" class="mr-2">Cancelar</SecondaryButton>
      <PrimaryButton type="button" @click="deleteHour">Aceptar</PrimaryButton>
    </template>
  </ConfirmationModal>
</template>