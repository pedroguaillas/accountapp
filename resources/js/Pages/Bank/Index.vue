<script setup lang="ts">
// Imports
import { ref, reactive, watch } from "vue";
import AdminLayout from "@/Layouts/AdminLayout.vue";
import FormModal from "./FormModal.vue";
import { router, useForm, Link } from "@inertiajs/vue3";
import Table from "@/Components/Table.vue";
import axios from "axios";
import ConfirmationModal from "@/Components/ConfirmationModal.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import Paginate from "@/Components/Paginate.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import { TrashIcon, PencilIcon, ListBulletIcon } from "@heroicons/vue/24/solid";
import { ResponseBankProps,BankPaginate, Filters, Bank, Errors } from "@/types/bank";
import { PropType } from "vue";

// Props
const props = defineProps<{
  banks: BankPaginate; // Paginación de los bancos
  filters: Filters; // Filtros aplicados
}>();

// Refs
const modal = ref(false);
const modal1 = ref(false);
const deleteid = ref<Number>(0);
const search = ref(props.filters.search); // Término de búsqueda
const loading = ref(false); // Estado de carga

const toggle1 = () => {
  modal1.value = !modal1.value;
};

// Inicializador de objetos
const initialBank: Bank = {
  id: undefined,
  name: "",
  description: "",
};

// Reactives
const bank = useForm<Bank>({ ...initialBank });
const errorForm = reactive({ ...initialBank });

const newBank = () => {
  if (bank.id !== undefined) {
    delete bank.id;
  }
  Object.assign(bank, initialBank);
  toggle();
};

const resetErrorForm = () => {
  Object.assign(errorForm, initialBank);
};

const toggle = () => {
  modal.value = !modal.value;
};

const save = () => {
  // Determinar el método HTTP y la ruta correspondiente
  const isUpdate = Boolean(bank.id);
  const routeMethod = isUpdate ? "put" : "post";
  console.log(bank);
  const routeName = isUpdate
    ? route("banks.update", { id: bank.id }) // Ruta para actualización
    : route("banks.store"); // Ruta para creación
  console.log(routeName);
  // Preparar la solicitud
  bank[routeMethod](routeName, {
    onSuccess: () => {
      toggle(); // Cerrar modal o reiniciar estados
      resetErrorForm(); // Limpiar errores del formulario
      router.reload({ only: ["banks"] }); // Recargar datos
    },
    onError: (error: Errors) => {
      resetErrorForm(); // Asegurarte de limpiar los errores previos
      if (error.response?.data?.errors) {
        // Iterar sobre los errores recibidos del servidor
        // Object.entries(error.response.data.errors).forEach(([key, value]) => {
        //   errorForm[key] = value[0]; // Mostrar el primer error asociado a cada campo
        // });
      } else {
        console.error("Error desconocido:", error);
        alert("Ocurrió un error inesperado. Por favor, intente nuevamente.");
      }
    },
  });
};

const update = (bankEdit: Bank) => {
  resetErrorForm();
  // Iterar sobre las claves de bankEdit y actualizar los valores del formulario.
  Object.keys(bankEdit).forEach((key) => {
    if (key in bank) { // Accede directamente a las propiedades del formulario.
      bank[key] = bankEdit[key];
    }
  });
  toggle();
};

const removeBank = (bankId: Number) => {
  toggle1();
  deleteid.value = bankId;
};

const deletebank = () => {
  router.delete(route("banks.delete", deleteid.value), {
    onSuccess: () => {
      toggle1();
    },
    onError: (error) => {
      console.error("Error al eliminar el banco", error);
    },
  });
};

watch(
  search,

  async (newQuery) => {
    const url = route("banks.index");
    loading.value = true;
    try {
      await router.get(
        url,
        { search: newQuery, page: props.banks.current_page }, // Mantener la página actual
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

const toggleState = (bankId: Number, currentState: String) => {
  const newState = !currentState; // Inverse the current state (active to inactive and vice versa)

  axios
    .put(route("banks.updateState", { id: bankId }), {
      state: newState,
    })
    .then(() => {
      // On success, reload the page or update the local data
      router.visit(route("banks.index"));
    })
    .catch((error) => {
      console.error("Error al cambiar el estado del banco", error);
      alert("Ocurrió un error al cambiar el estado. Intenta de nuevo.");
    });
};
</script>

<template>
  <AdminLayout title="Bancos">
    <!-- Card -->
    <div class="p-4 bg-white rounded drop-shadow-md">
      <!-- Card Header -->
      <div class="flex flex-col sm:flex-row justify-between items-center">
        <h2 class="text-sm sm:text-lg font-bold w-full pb-2 sm:pb-0">Bancos</h2>
        <div class="w-full flex sm:justify-end">
          <TextInput v-model="search" type="search" class="block sm:mr-2 h-8 w-full" placeholder="Buscar ..." />
        </div>
        <button @click="newBank"
          class="mt-2 sm:mt-0 px-2 bg-success dark:bg-green-600 hover:bg-successhover text-2xl text-white rounded font-bold">
          +
        </button>
      </div>

      <Table>
        <thead>
          <tr class="[&>th]:py-2">
            <th class="w-1">N°</th>
            <th class="text-left">NOMBRE</th>
            <th class="text-left">DESCRIPCION</th>
            <th>ESTADO</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(bank, i) in props.banks.data" :key="bank.id" class="border-t [&>td]:py-2">
            <td>{{ i + 1 }}</td>
            <td class="text-left">{{ bank.name }}</td>
            <td class="text-left">{{ bank.description }}</td>

            <td>
              <span :class="bank.state ? 'bg-success' : 'bg-danger'" class="rounded px-2 py-1 m-0 text-white">
                {{ bank.state ? "Activo" : "Inactivo" }}
              </span>
            </td>
            <td class="flex justify-end">
              <div class="relative inline-flex gap-1">
                <Link class="rounded px-1 py-1 bg-green-400 hover:bg-green-500 text-white"
                  :href="route('bankaccounts.index', bank.id)">
                <ListBulletIcon class="size-6 text-white" />
                </Link>
                <button class="rounded px-1 py-1 bg-danger hover:bg-dangerhover text-white"
                  @click="() => bank.id && removeBank(bank.id)">
                  <TrashIcon class="size-6 text-white" />
                </button>
                <button class="rounded px-2 py-1 bg-primary hover:bg-primaryhover text-white" @click="update(bank)">
                  <PencilIcon class="size-4 text-white" />
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </Table>
    </div>
    <Paginate :page="props.banks" />
  </AdminLayout>

  <FormModal :show="modal" :bank="bank" :error="errorForm" @close="toggle" @save="save" />

  <ConfirmationModal :show="modal1">
    <template #title> ELIMINAR BANCOS</template>
    <template #content> Esta seguro de eliminar el banco? </template>
    <template #footer>
      <SecondaryButton @click="modal1 = !modal1" class="mr-2">Cancelar</SecondaryButton>
      <PrimaryButton type="button" @click="deletebank">Aceptar</PrimaryButton>
    </template>
  </ConfirmationModal>
</template>
