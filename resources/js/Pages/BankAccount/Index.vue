<script setup lang="ts">
// Imports
import { ref, reactive, watch } from "vue";
import AdminLayout from "@/Layouts/AdminLayout.vue";
import FormModal from "./FormModal.vue";
import { router, useForm } from "@inertiajs/vue3";
import axios from "axios";
import { TrashIcon, PencilIcon } from "@heroicons/vue/24/solid";
import { TextInput, SecondaryButton, PrimaryButton, ConfirmationModal, Paginate, Table } from "@/Components";
import { Bank, BankAccount, Errors, Filters, GeneralRequest } from "@/types";

// Props
const props = defineProps<{
  bankAccounts: GeneralRequest<BankAccount>; // Paginación de los bancos
  filters: Filters; // Filtros aplicados
  bank: Bank;
}>();

// Refs
const modal = ref(false);
const modalDelete = ref(false);
const deleteId = ref<Number>(0);
const search = ref(props.filters.search); // Término de búsqueda

const toggleDelete = () => {
  modalDelete.value = !modalDelete.value;
};

// Inicializador de objetos
const initialBankAccount = {
  id: undefined,
  account_number: "",
  account_type: "",
  bank_id: props.bank.id,
  current_balance: 0,
  state: false,
};

// Reactives
const bankAccount = useForm<BankAccount>({ ...initialBankAccount });
const errorForm = reactive<Errors>({});

const newBankAccount = () => {
  if (bankAccount.id !== undefined) {
    delete bankAccount.id;
  }
  Object.assign(bankAccount, initialBankAccount);
  toggle();
};

const resetErrorForm = () => {
  Object.assign(errorForm, initialBankAccount);
};

const toggle = () => {
  modal.value = !modal.value;
};

const save = () => {
  const isUpdate = Boolean(bankAccount.id);
  const routeMethod = isUpdate ? "put" : "post";
  const routeName = isUpdate
    ? route("bankaccounts.update", { id: bankAccount.id }) // Ruta para actualización
    : route("bankaccounts.store"); // Ruta para creación

  console.log(bankAccount.data);
  // Preparar la solicitud
  bankAccount[routeMethod](routeName, {
    onSuccess: () => {
      toggle(); // Cerrar modal o reiniciar estados
      resetErrorForm(); // Limpiar errores del formulario
      router.reload({ only: ["bankaccounts"] }); // Recargar datos
    },
    onError: (error: Errors) => {
      resetErrorForm(); // Asegurarte de limpiar los errores previos
      if (error.response?.data?.errors) {
        // Iterar sobre los errores recibidos del servidor
        Object.entries(error.response.data.errors).forEach(([key, value]) => {
          errorForm[key] = (value as string[])[0]; // Mostrar el primer error asociado a cada campo
        });
      } else {
        console.error("Error desconocido:", error);
        alert("Ocurrió un error inesperado. Por favor, intente nuevamente.");
      }
    },
  });
};

const update = (bankAccountEdit: BankAccount) => {
  resetErrorForm();
  Object.keys(bankAccountEdit).forEach((key) => {
    bankAccount[key] = bankAccountEdit[key];
  });
  toggle();
};

const removeBankAccount = (bankAccountId: Number) => {
  toggleDelete();
  deleteId.value = bankAccountId;
};

const deleteBankAccount = () => {
  router.delete(route("bankaccounts.delete", deleteId.value), {
    onSuccess: () => {
      toggleDelete();
    },
    onError: (error) => {
      console.error("Error al eliminar la cuenta bancaria", error);
    },
  });
};

watch(
  search,
  async (newQuery) => {
    const url = route("bankaccounts.index", props.bank.id);
    try {
      await router.get(
        url,
        { search: newQuery, page: props.bankAccounts.current_page }, // Mantener la página actual
        { preserveState: true }
      );
    } catch (error) {
      console.error("Error al filtrar:", error);
    } 
  },
  { immediate: false }
);

const toggleState = (bankAccountId: Number, currentState: Boolean) => {
  const newState = !currentState; // Inverse the current state (active to inactive and vice versa)

  axios
    .put(route("bankaccounts.updateState", { id: bankAccountId }), {
      state: newState,
    })
    .then(() => {
      // On success, reload the page or update the local data
      router.visit(route("bankaccounts.index", props.bank.id));
    })
    .catch((error) => {
      console.error("Error al cambiar el estado del cuenta bancaria", error);
      alert("Ocurrió un error al cambiar el estado. Intenta de nuevo.");
    });
};
</script>

<template>
  <AdminLayout title="Cuentas Bancarias">
    <!-- Card -->
    <div class="p-4 bg-white rounded drop-shadow-md">
      <!-- Card Header -->
      <div class="flex flex-col sm:flex-row justify-between items-center">
        <h2 class="text-sm sm:text-lg font-bold w-full pb-2 sm:pb-0">
          Cuentas Bancarias {{ props.bank.name }}
        </h2>
        <div class="w-full flex sm:justify-end">
          <TextInput v-model="search" type="search" class="block sm:mr-2 h-8 w-full" placeholder="Buscar ..." />
        </div>
        <button @click="newBankAccount"
          class="mt-2 sm:mt-0 px-2 bg-success dark:bg-green-600 hover:bg-successhover text-2xl text-white rounded font-bold">
          +
        </button>
      </div>

      <Table>
        <thead>
          <tr class="[&>th]:py-2">
            <th class="w-1">N°</th>
            <th>NUMERO DE CUENTA</th>
            <th>TIPO DE CUENTA</th>
            <th>SALDO</th>
            <th>ESTADO</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(bankAccount, i) in props.bankAccounts.data" :key="bankAccount.id" class="border-t [&>td]:py-2">
            <td>{{ i + 1 }}</td>
            <td>{{ bankAccount.account_number }}</td>
            <td>{{ bankAccount.account_type }}</td>
            <td>{{ bankAccount.current_balance }}</td>

            <td>
              <button :class="bankAccount.state ? 'bg-success' : 'bg-danger'"
                @click="() => bankAccount.id && toggleState(bankAccount.id, bankAccount.state)"
                class="rounded px-2 py-1 text-white">
                {{ bankAccount.state ? "Activo" : "Inactivo" }}
              </button>
            </td>

            <td class="flex justify-end">
              <div class="relative inline-flex gap-1">
                <button class="rounded px-1 py-1 bg-danger hover:bg-dangerhover text-white"
                  @click="()=> bankAccount.id && removeBankAccount(bankAccount.id)">
                  <TrashIcon class="size-6 text-white" />
                </button>
                <button class="rounded px-2 py-1 bg-primary hover:bg-primaryhover text-white"
                  @click="update(bankAccount)">
                  <PencilIcon class="size-4 text-white" />
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </Table>
    </div>
    <Paginate :page="props.bankAccounts" />
  </AdminLayout>

  <FormModal :show="modal" :bankAccount="bankAccount" :error="errorForm" @close="toggle" @save="save" />

  <ConfirmationModal :show="modalDelete">
    <template #title> ELIMINAR CUENTAS BANCARIAS </template>
    <template #content> Esta seguro de eliminar la cuenta bancaria? </template>
    <template #footer>
      <SecondaryButton @click="modalDelete = !modalDelete" class="mr-2">Cancelar</SecondaryButton>
      <PrimaryButton type="button" @click="deleteBankAccount">Aceptar</PrimaryButton>
    </template>
  </ConfirmationModal>
</template>
