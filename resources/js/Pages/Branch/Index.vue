<script setup>
// Imports
import { ref, reactive, watch } from "vue";
import AdminLayout from "@/Layouts/AdminLayout.vue";
import FormModal from "./FormModal.vue";
import { router } from "@inertiajs/vue3";
import Table from "@/Components/Table.vue";
import axios from "axios";
import ConfirmationModal from "@/Components/ConfirmationModal.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import { PlusIcon, TrashIcon, PencilIcon } from "@heroicons/vue/24/solid";

// Props
const props = defineProps({
  branches: { type: Object, default: () => ({}) },
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
const initialBranch = {
  number: "",
  name: "",
  city: "",
  address: "",
  is_matriz: false,
  enviroment_type: "",
};

// Reactives
const branch = reactive({ ...initialBranch });
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

  const routeMethod = branch.id ? "put" : "post"; // Determinar el método HTTP
  const routeName = branch.id
    ? route("branch.update", branch.id) // Ruta para actualización
    : route("branch.store"); // Ruta para creación

  axios[routeMethod](routeName, branch)
    .then(() => {
      toggle(); // Cierra el modal
      resetErrorForm(); // Reinicia los errores del formulario
      router.reload({ only: ["branches"] }); // Recarga los datos de sucursales
    })
    .catch((error) => {
      resetErrorForm();
      if (error.response && error.response.data.errors) {
        Object.keys(error.response.data.errors).forEach((key) => {
          errorForm[key] = error.response.data.errors[key][0];
        });
      } else {
        console.error("Error desconocido", error);
        alert("Hubo un error al procesar la solicitud");
      }
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

const deletebranch = () => {
  axios
    .delete(route("branch.delete", deleteid.value)) // Eliminar centro de costos
    .then(() => {
      // Después de eliminar el centro de costos, redirigir a la ruta deseada
      router.visit(route("branch.index"));
    })
    .catch((error) => {
      console.error("Error al eliminar a sucursal", error);
    });
};

watch(
  search,
  async (newQuery) => {
    const url = route("branch.index"); // Ruta del índice de sucursales
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
  <AdminLayout title="Sucursales / establecimientos">
    <!-- Card -->
    <div class="p-4 bg-white rounded drop-shadow-md">
      <!-- Card Header -->
      <div class="flex flex-col sm:flex-row justify-between items-center">
        <h2 class="text-sm sm:text-lg font-bold w-full pb-2 sm:pb-0">
          Sucursales / establecimientos
        </h2>
        <div class="w-full flex sm:justify-end">
          <TextInput v-model="search" type="search" class="block sm:mr-2 h-8 w-full" placeholder="Buscar ..." />
        </div>
        <button @click="newBranch" class="mt-2 sm:mt-0 px-2 bg-green-500 dark:bg-green-600 text-2xl text-white rounded font-bold">
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
            <th class="w-1"></th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(branch, i) in props.branches.data" :key="branch.id" class="border-t [&>td]:py-2">
            <td>{{ i + 1 }}</td>
            <td>{{ branch.number }}</td>
            <td>{{ branch.name }}</td>
            <td>{{ branch.city }}</td>
            <td>{{ branch.address }}</td>
            <td>{{ branch.is_matriz ? "Matriz" : "Sucursal" }}</td>
            <td>
              {{ branch.enviroment_type === 1 ? "Prueba" : "Producción" }}
            </td>
            <td>
              <div class="relative inline-flex gap-1">
                <button class="rounded px-1 py-1 bg-red-500 text-white" @click="removeBranch(branch.id)">
                  <TrashIcon class="size-6 text-white" />
                </button>
                <button class="rounded px-2 py-1 bg-blue-500 text-white" @click="update(branch)">
                  <PencilIcon class="size-4 text-white" />
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </Table>
    </div>
  </AdminLayout>

  <FormModal :show="modal" :branch="branch" :error="errorForm" @close="toggle" @save="save" />

  <ConfirmationModal :show="modal1">
    <template #title> ELIMINAR ESTABLECIMIENTOS </template>
    <template #content> Esta seguro de eliminar el establecimiento? </template>
    <template #footer>
      <PrimaryButton type="button" @click="deletebranch">Aceptar</PrimaryButton>
    </template>
  </ConfirmationModal>
</template>
