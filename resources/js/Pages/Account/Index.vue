<script setup>
// Imports
import AdminLayout from "@/Layouts/AdminLayout.vue";
import ImportExcel from "./ImportExcel.vue";
import Table from "@/Components/Table.vue";
import ModalAccount from "./ModalAccount.vue";
import { useForm, router } from "@inertiajs/vue3";
import TextInput from "@/Components/TextInput.vue";
import axios from "axios";
import { ref, reactive, computed } from "vue";
import { PencilIcon, TrashIcon } from "@heroicons/vue/24/solid";
import ConfirmationModal from "@/Components/ConfirmationModal.vue";
//Props
const props = defineProps({
  accounts: { type: Array, default: () => [] },
});

// Refs
const modal = ref(false);
const modalDelete = ref(false);
const errorForm = reactive({});
const search = ref(""); // Término de búsqueda
const hoveredRow = ref(null);
const accountaux = ref({});
const initialAccount = { code: "", name: "", parent_id: 0 };
const account = useForm({ ...initialAccount });

const toggle = () => {
  modal.value = !modal.value;
};

const toggleDelete = () => {
  modalDelete.value = !modalDelete.value;
};


const resetErrorForm = () => {
  Object.assign(errorForm, initialAccount);
}

// Aquí solo actualizamos el método `save` para que reciba los datos del hijo
const save = () => {
   // Determinar el método HTTP y la ruta correspondiente
   const isUpdate = Boolean(account.id);
  const routeMethod = isUpdate ? "put" : "post";
  const routeName = isUpdate
    ? route("accounts.update", { id:account.id}) // Ruta para actualización
    : route("accounts.store"); // Ruta para creación

  // Enviar datos con POST
  account[routeMethod](routeName, {
    onSuccess: () => {
      toggle(); // Cerrar el modal
      resetErrorForm(); // Limpiar errores del formulario
      router.reload({ only: ["plan-de-cuentas"] }); // Recargar datos relacionados con cuentas
    },
    onError: (error) => {
      resetErrorForm(); // Limpiar los errores previos
      // Manejar los errores y asignarlos a `errorForm`
      Object.entries(error).forEach(([key, value]) => {
        errorForm[key] = value; // Guardar el error asociado a cada campo
      });
    },
  });
};

const edit = (accountEdit) => {
  Object.assign(account, accountEdit);
  toggle();
};


const removeAccount = (accountId) => {
  toggleDelete();
  Object.assign(account, accountId);
};

const deleteAccount = () => {
  axios
    .delete(route("accounts.destroy", account)) // Eliminar centro de costos
    .then(() => {
      // Después de eliminar el centro de costos, redirigir a la ruta deseada
      router.visit(route("accounts"));
    })
    .catch((error) => {
      alert("La cuenta esta siendo utilizada. No se puede eliminar");
      toggleDelete();
    });
};


// Función para calcular la clase de estilo
const styleAccount = (code) => {
  let result = "";
  if (code.length === 1) {
    result = "text-black";
  } else if (code.length < 4) {
    result = "text-stone-600 indent-2";
  } else {
    result = "text-stone-400 indent-4";
  }
  return result + " text-left";
};

const newAccount = (accountId) => {

  if (account.id !== undefined) {
    delete account.id;
  }
  Object.assign(account, initialAccount);
  accountaux.value = accountId;
  axios
    .get(route("accounts.create", accountId))
    .then((response) => {
      account.parent_id = accountId.id;
      account.code = response.data.code;
      // Muestro el modal
      toggle();
    })
    .catch((error) => {
      console.error("Error al mostrar el code", error);
    });
};

const filteredAccounts = computed(() => {
  return props.accounts.filter((account) => {
    const searchTerm = search.value.toLowerCase();
    const codeMatch = account.code.toLowerCase().includes(searchTerm);
    const nameMatch = account.name.toLowerCase().includes(searchTerm);
    return codeMatch || nameMatch;
  });
});
</script>

<template>
  <AdminLayout title="Plan de Cuentas">
    <!-- Card -->
    <div class="p-4 bg-white rounded drop-shadow-md">
      <!-- Card Header -->
      <div class="flex justify-between items-center">
        <h2 class="text-sm sm:text-lg font-bold w-full pb-2 sm:pb-0">
          Plan de cuentas
        </h2>
        <ImportExcel v-if="accounts.length === 0" />

        <div class="w-full flex justify-end">
          <TextInput
            v-model="search"
            type="text"
            class="mt-1 block w-[50%]  h-8"
            minlength="3"
            maxlength="300"
            required
            placeholder="Buscar..."
          />
        </div>
      </div>

      <!-- Resposive -->
      <div class="w-full overflow-x-auto">
        <!-- Tabla -->
        <Table>
          <thead>
            <tr>
              <th class="w-12 text-left">CUENTA</th>
              <th class="text-left">DESCRIPCION</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="account in filteredAccounts"
              :key="account.id"
              class="border-t relative [&>td]:py-2 group hover:bg-slate-200"
              @mouseenter="hoveredRow = account.id"
              @mouseleave="hoveredRow = null"
              :class="account.is_detail ? 'italic' : 'font-bold'"
            >
              <td :class="styleAccount(account.code)">
                {{ account.code }}
              </td>
              <td :class="styleAccount(account.code)">
                {{ account.name }}
              </td>

              <td
                v-if="hoveredRow === account.id"
                class="flex justify-center absolute h-12 top-0 right-1/2 bg-white items-center shadow-md border rounded z-10 group-hover:block"
              >
                <ul class="px-4 flex gap-4 rounded items-center justify-center">
                  <li
                    @click="newAccount(account)"
                    class="mt-2 sm:mt-0 px-2 bg-green-500 dark:bg-green-600 text-2xl text-white rounded font-bold"
                  >
                    +
                  </li>
                  <li @click="edit(account)">
                    <PencilIcon class="size-5 text-red" />
                  </li>
                  <li @click="removeAccount(account)">
                    <TrashIcon class="size-5 text-red" />
                  </li>
                </ul>
              </td>
            </tr>
          </tbody>
        </Table>
      </div>
    </div>
  </AdminLayout>

  <ModalAccount
    :show="modal"
    :account="account"
    :error="errorForm"
    :parent="accountaux"
    @close="toggle"
    @save="save"
  />

  <ConfirmationModal :show="modalDelete">
    <template #title> ELIMINAR CUENTA DEL PLAN DE CUENTAS </template>
    <template #content> Esta seguro de eliminar la cuenta <span class="font-bold">{{account.code}} {{account.name}} </span>?</template>
    <template #footer>
      <PrimaryButton type="button" @click="deleteAccount">Aceptar</PrimaryButton>
    </template>
  </ConfirmationModal>
</template>