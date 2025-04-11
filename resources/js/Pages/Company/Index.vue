<script setup lang="ts">
// Imports
import { ref, reactive, watch } from "vue";
import AdminLayout from "@/Layouts/AdminLayout.vue";
import ModalCompany from "./ModalCompany.vue";
import { router, useForm } from "@inertiajs/vue3";
import { ConfirmationModal, TextInput, SecondaryButton, PrimaryButton, Table, Paginate } from "@/Components";
import { TrashIcon, PencilIcon } from "@heroicons/vue/24/solid";
import { Company, ContributorType, EconomiActivity, Errors, Filters, GeneralRequest } from "@/types";

//Props
const props = defineProps<{
  companies: GeneralRequest<Company>; // Paginación de los bancos
  filters: Filters; // Filtros aplicados
  economyActivities: EconomiActivity[];
  contributorTypes: ContributorType[];
}>();

// Refs
const modal = ref(false);
const search = ref(props.filters.search); // Término de búsqueda
const modalDelete = ref(false);

//El inicializador de objetos
const initialCompany = {
  id: undefined,
  ruc: "",
  company: "",
  economic_activity_id: "",
  contributor_type_id: "",
  restart_activities: "",
  special: "",
  accounting: false,
  retention_agent: 0,
  declaration_type: "",
  certificate_path: "",
  certificate_pass: "",
  sign_valid_from: "",
  sign_valid_to: "",
  date: "",
};

// Reactives
const company = useForm<Company>({ ...initialCompany });
const errorForm: Record<string, string> = {};
const deleteId = ref<Number>(0);

const newCompany = () => {
  // Reinicio el formularios con valores vacios
  if (company.id !== undefined) {
    delete company.id;
  }
  Object.assign(company, initialCompany);
  // Muestro el modal
  toggle();
};

const resetErrorForm = () => {
  Object.assign(errorForm, initialCompany);
};

const toggle = () => {
  modal.value = !modal.value;
};

const toggleDelete = () => {
  modalDelete.value = !modalDelete.value;
};

const save = () => {
  // Determinar el método HTTP y la ruta correspondiente
  const isUpdate = Boolean(company.id);
  const routeMethod = isUpdate ? "put" : "post";
  const routeName = isUpdate
    ? route("company.update", { id: company.id }) // Ruta para actualización
    : route("company.store"); // Ruta para creación

  // Preparar la solicitud
  company[routeMethod](routeName, {
    onSuccess: () => {
      toggle(); // Cerrar modal o reiniciar estados
      resetErrorForm(); // Limpiar errores del formulario
      router.reload({ only: ["rucs"] }); // Recargar datos
    },
    onError: (error: Errors) => {
      resetErrorForm(); // Asegurarte de limpiar los errores previos

      // Iterar sobre los errores recibidos del servidor
      Object.entries(error).forEach(([key, value]) => {
        errorForm[key] = value; // Mostrar el primer error asociado a cada campo
      });
    },
  });
};

const update = (companyEdit: Company) => {
  resetErrorForm();
  Object.keys(companyEdit).forEach((key) => {
    company[key] = companyEdit[key];
  });
  toggle();
};

const removeCompany = (companyId: Number) => {
  toggleDelete();
  deleteId.value = companyId;
};

const deletecompany = () => {
  router.delete(route("company.delete", deleteId.value), {
    onSuccess: () => {
      toggleDelete();
    },
    onError: (error) => {
      console.error("Error al eliminar la compania", error);
    },
  });
};

watch(
  search,
  async (newQuery) => {
    const url = route("rucs.index");

    try {
      await router.get(
        url,
        { search: newQuery, page: props.companies.current_page }, // Mantener la página actual
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
  <AdminLayout title="Negocios / RUCs">
    <!-- Card -->
    <div class="p-4 bg-white rounded drop-shadow-md">
      <!-- Card Header -->
      <div class="flex flex-col sm:flex-row justify-between items-center">
        <h2 class="text-sm sm:text-lg font-bold w-full pb-2 sm:pb-0">
          Negocios / RUCs
        </h2>
        <div class="w-full flex justify-end">
          <TextInput v-model="search" type="text" class="mt-1 block w-[50%] mr-2 h-8" minlength="3" maxlength="300"
            required placeholder="Buscar..." />
        </div>
        <button @click="newCompany"
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
              <th>RUC</th>
              <th class="text-left">RAZON SOCIAL</th>
              <th class="w-1"></th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(company, i) in props.companies.data" :key="company.id" class="border-t [&>td]:py-2">
              <td>{{ i + 1 }}</td>
              <td>{{ company.ruc }}</td>
              <td class="text-left">{{ company.company }}</td>

              <td class="flex justify-end">
                <div class="relative inline-flex gap-1">
                  <button class="rounded px-1 py-1 bg-danger hover:bg-dangerhover text-white"
                    @click="() => company.id && removeCompany(company.id)">
                    <TrashIcon class="size-6 text-white" />
                  </button>
                  <button class="rounded px-2 py-1 bg-primary hover:bg-primaryhover text-white"
                    @click="update(company)">
                    <PencilIcon class="size-4 text-white" />
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </Table>
      </div>
    </div>
    <Paginate :page="props.companies" />
  </AdminLayout>

  <ModalCompany :show="modal" :company="company" :error="errorForm" :economyActivities="economyActivities"
    :contributorTypes="contributorTypes" @close="toggle" @save="save" />

  <ConfirmationModal :show="modalDelete">
    <template #title> ELIMINAR LAS COMPANIAS</template>
    <template #content> Esta seguro de eliminar la compania? </template>
    <template #footer>
      <SecondaryButton @click="modalDelete = !modalDelete" class="mr-2">Cancelar</SecondaryButton>
      <PrimaryButton type="button" @click="deletecompany">Aceptar</PrimaryButton>
    </template>
  </ConfirmationModal>
</template>