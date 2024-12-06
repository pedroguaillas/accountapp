<script setup>
// Imports
import { ref, reactive, watch } from "vue";
import AdminLayout from "@/Layouts/AdminLayout.vue";
import ModalCompany from "./ModalCompany.vue";
import { router } from "@inertiajs/vue3";
import axios from "axios";
import Table from "@/Components/Table.vue";
import TextInput from "@/Components/TextInput.vue";
import Paginate from "@/Components/Paginate.vue";
import ConfirmationModal from "@/Components/ConfirmationModal.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";

//Props
const props = defineProps({
  companies: { type: Array, default: () => [] },
  economyActivities: { type: Array, default: () => [] },
  contributorTypes: { type: Array, default: () => [] },
});

// Refs
const modal = ref(false);
const search = ref(""); // Término de búsqueda
const loading = ref(false); // Estado de carga
const modal1 = ref(false);

//El inicializador de objetos
const initialCompany = {
  ruc: "",
  company: "",
  economic_activity_id: "",
  contributor_type_id: "",
};

// Reactives
const company = reactive({ ...initialCompany });
const errorForm = reactive({});
const deleteid = ref(0);

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

const toggle1 = () => {
  modal1.value = !modal1.value;
};

const save = () => {
  if (company.id === undefined) {
    // const data = { ...club, category_id: props.category.id, group_id: club.group_id > 0 ? club.group_id : null, extra_points: club.extra_points === '' ? 0 : club.extra_points }
    axios
      .post(route("company.store"), company)
      .then(() => {
        toggle();
        resetErrorForm();

        router.reload({ only: ["companies"] });
      })
      .catch((error) => {
        resetErrorForm();

        Object.keys(error.response.data.errors).forEach((key) => {
          errorForm[key] = error.response.data.errors[key][0];
        });
      });
  } else {
    axios
      .put(route("company.update", company.id), company)
      .then(() => {
        toggle();
        resetErrorForm();

        router.reload({ only: ["companies"] });
      })
      .catch((error) => {
        resetErrorForm();

        Object.keys(error.response.data.errors).forEach((key) => {
          errorForm[key] = error.response.data.errors[key][0];
        });
      });
  }
};

const update = (companyEdit) => {
  resetErrorForm();
  Object.keys(companyEdit).forEach((key) => {
    company[key] = companyEdit[key];
  });
  toggle();
};

const removeCompany = (companyId) => {
  toggle1();
  deleteid.value = companyId;
};

const deletecompany = () => {
  axios
    .delete(route("company.delete", deleteid.value))
    .then(() => {
      router.visit(route("rucs.index")); // Redirige a la ruta deseada
    })
    .catch((error) => {
      console.error("Error al eliminar la compania", error);
    });
};

watch(
  search,
  async (newQuery) => {
    const url = route("rucs.index"); // Ruta del índice de sucursales
    loading.value = true; // Activa el indicador de carga

    try {
      if (newQuery.length === 0) {
        // Si el término de búsqueda está vacío, recarga todos los datos
        await router.get(
          url,
          {}, // Sin parámetros de búsqueda
          { preserveState: true }
        );
      } else if (newQuery.length >= 1) {
        // Realizar búsqueda con el término
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
          <TextInput
            v-model="search"
            type="text"
            class="mt-1 block w-[50%] mr-2 h-8"
            minlength="3"
            maxlength="300"
            required
            placeholder="BUSCAR"
          />
        </div>
        <button
          @click="newCompany"
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
              <th>RUC</th>
              <th>RAZON SOCIAL</th>
              <th class="w-1"></th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="(company, i) in props.companies"
              :key="company.id"
              class="border-t [&>td]:py-2"
            >
              <td>{{ i + 1 }}</td>
              <td>{{ company.ruc }}</td>
              <td>{{ company.company }}</td>
              <td>
                <div
                  class="relative inline-flex [&>a>i]:text-white [&>button>i]:text-white"
                >
                  <button
                    class="rounded px-2 py-1 bg-blue-500 text-white"
                    @click="update(company)"
                  >
                    <i class="fa fa-trash"></i> Modificar
                  </button>
                  <button
                    class="rounded px-2 py-1 bg-red-500 text-white"
                    @click="removeCompany(company.id)"
                  >
                    <i class="fa fa-trash"></i> Eliminar
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </Table>
        <Paginate :page="props.companies" />
      </div>
    </div>
  </AdminLayout>

  <ModalCompany
    :show="modal"
    :company="company"
    :error="errorForm"
    :economyActivities="economyActivities"
    :contributorTypes="contributorTypes"
    @close="toggle"
    @save="save"
  />

  <ConfirmationModal :show="modal1">
    <template #title> ELIMINAR LAS COMPANIAS</template>
    <template #content> Esta seguro de eliminar la compania? </template>
    <template #footer>
      <PrimaryButton type="button" @click="deletecompany"
        >Aceptar</PrimaryButton
      >
    </template>
  </ConfirmationModal>
</template>