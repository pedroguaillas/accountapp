<script setup>
// Imports
import { ref, reactive,watch } from "vue";
import AdminLayout from "@/Layouts/AdminLayout.vue";
import ModalCompany from "./ModalCompany.vue";
import { router } from "@inertiajs/vue3";
import axios from "axios";
import Table from "@/Components/Table.vue";
import TextInput from "@/Components/TextInput.vue";


//Props
defineProps({
  companies: { type: Array, default: () => [] },
  economyActivities: { type: Array, default: () => [] },
  contributorTypes: { type: Array, default: () => [] },
});

// Refs
const modal = ref(false);
const search = ref(""); // Término de búsqueda
const loading = ref(false); // Estado de carga

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
      <div class="flex justify-between items-center">
        <h2 class="text-sm sm:text-lg font-bold">Negocios / RUCs</h2>
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
          class="px-2 bg-green-500 dark:bg-green-600 text-2xl text-white rounded font-bold"
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
              v-for="(company, i) in companies"
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
                </div>
              </td>
            </tr>
          </tbody>
        </Table>
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
</template>