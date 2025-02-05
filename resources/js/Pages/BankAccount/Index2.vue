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
  bankaccounts: { type: Object, default: () => ({}) },
  filters: { type: String, default: "" },
  banks: { type: Array, default: () => [] },
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
const initialBankAccount = {
  account_number: "",
  account_type: "",
  banck_id: "",
  current_balance: "",
  state: "",
};

// Reactives
const bankaccount = useForm({ ...initialBankAccount });
const errorForm = reactive({ ...initialBankAccount });

const newBankAccount = () => {
  if (bankaccount.id !== undefined) {
    delete bankaccount.id;
  }
  Object.assign(bankaccount, initialBankAccount);
  toggle();
};

const resetErrorForm = () => {
  Object.assign(errorForm, initialBankAccount);
};

const toggle = () => {
  modal.value = !modal.value;
};

const save = () => {
  const isUpdate = Boolean(bankaccount.id);
  const routeMethod = isUpdate ? "put" : "post";
  const routeName = isUpdate
    ? route("bankaccounts.update", { id: bankaccount.id }) // Ruta para actualización
    : route("bankaccounts.store"); // Ruta para creación

  // Preparar la solicitud
  bankaccount[routeMethod](routeName, {
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

const update = (bankaccountEdit) => {
  resetErrorForm();
  Object.keys(bankaccountEdit).forEach((key) => {
    bankaccount[key] = bankaccountEdit[key];
  });
  toggle();
};

const removeBankAccount = (bankaccountId) => {
  toggle1();
  deleteid.value = bankaccountId;
};

const deleteBankAccount = () => {
  axios
    .delete(route("bankaccounts.delete", deleteid.value)) // Eliminar centro de costos
    .then(() => {
      // Después de eliminar el centro de costos, redirigir a la ruta deseada
      router.visit(route("bankaccounts.index"));
    })
    .catch((error) => {
      console.error("Error al eliminar a sucursal", error);
    });
};

watch(
  search,

  async (newQuery) => {
    const url = route("bankaccounts.index");
    loading.value = true;
    console.log("si entra");

    try {
      await router.get(
        url,
        { search: newQuery, page: props.bankaccounts.current_page }, // Mantener la página actual
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
  const url = route("bankaccounts.index"); // Ruta hacia el backend
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
  <AdminLayout title="Cuentas Bancarias">
    <!-- Card -->
    <div class="p-4 bg-white rounded drop-shadow-md">
      <!-- Card Header -->
      <div class="flex flex-col sm:flex-row justify-between items-center">
        <h2 class="text-sm sm:text-lg font-bold w-full pb-2 sm:pb-0">
          Cuentas Bancarias
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
          @click="newBankAccount"
          class="mt-2 sm:mt-0 px-2 bg-success dark:bg-green-600 hover:bg-successhover text-2xl text-white rounded font-bold"
        >
          +
        </button>
      </div>

      <Table>
        <thead>
          <tr class="[&>th]:py-2">
            <th class="w-1">N°</th>
            <th>BANCO</th>
            <th>NUMERO DE CUENTA</th>
            <th>TIPO DE CUENTA</th>
            <th>SALDO</th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="(bankaccount, i) in props.bankaccounts.data"
            :key="bankaccount.id"
            class="border-t [&>td]:py-2"
          >
            <td>{{ i + 1 }}</td>
            <td>{{ bankaccount.banck_id }}</td>
            <td>{{ bankaccount.account_number }}</td>
            <td>{{ bankaccount.account_type }}</td>
            <td>{{ bankaccount.current_balance }}</td>

            <td class="flex justify-end">
              <div class="relative inline-flex gap-1">
                <button
                  class="rounded px-1 py-1 bg-danger hover:bg-dangerhover text-white"
                  @click="removeBankAccount(BankAccount.id)"
                >
                  <TrashIcon class="size-6 text-white" />
                </button>
                <button
                  class="rounded px-2 py-1 bg-primary hover:bg-primaryhover text-white"
                  @click="update(BankAccount)"
                >
                  <PencilIcon class="size-4 text-white" />
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </Table>
    </div>
    <Paginate :page="props.bankaccounts" @page-change="handlePageChange" />
  </AdminLayout>

  <FormModal
    :show="modal"
    :bankaccount="bankaccount"
    :banks="props.banks"
    :error="errorForm"
    @close="toggle"
    @save="save"
  />

  <ConfirmationModal :show="modal1">
    <template #title> ELIMINAR CUENTAS BANCARIAS </template>
    <template #content> Esta seguro de eliminar la cuenta bancaria? </template>
    <template #footer>
      <SecondaryButton @click="modal1 = !modal1" class="mr-2"
        >Cancelar</SecondaryButton
      >
      <PrimaryButton type="button" @click="deleteBankAccount"
        >Aceptar</PrimaryButton
      >
    </template>
  </ConfirmationModal>
</template>
