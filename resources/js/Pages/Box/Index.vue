<script setup>
// Imports
import AdminLayout from "@/Layouts/AdminLayout.vue";
import FormModal from "./FormModal.vue";
import OpenModal from "./OpenModal.vue";
import { useForm, router } from "@inertiajs/vue3";
import Table from "@/Components/Table.vue";
import axios from "axios";
import ConfirmationModal from "@/Components/ConfirmationModal.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import Paginate from "@/Components/Paginate.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import { ref, reactive, watch } from "vue";
import { TrashIcon, PencilIcon } from "@heroicons/vue/24/solid";

// Props
const props = defineProps({
  boxes: { type: Object, default: () => ({}) },
  filters: { type: Object, default: () => ({}) },
  employees: { type: Array, default: () => [] },
});

// Refs y estados
const modal = ref(false);
const modalDeleteBox = ref(false);
const modalOpenBox = ref(false);
const deleteid = ref(0);
const search = ref(props.filters.search);

// Para el formulario de crear/editar caja
const initialBox = { name: "", owner_id: 0, type: "", isCajaChica: false };
const box = useForm({ ...initialBox });
const errorForm = reactive({ ...initialBox });

// Para el modal de apertura de caja (cash_session)
// Usamos openForm para almacenar el monto inicial
const openForm = useForm({
  initial_value: "",
  ingress: 0,
  egress: 0,
  balance: 0,
  real_balance: 0,
  cash_difference: 0,
});
// Variable para almacenar el id de la caja que se va a abrir
const currentBoxId = ref(null);

// Funciones para el modal de crear/editar caja
const newBox = () => {
  if (box.id !== undefined) {
    delete box.id;
  }
  Object.assign(box, initialBox);
  if (props.boxes.data.length === 0) {
    box.name = "CAJA GENERAL";
    box.type = "general";
  }
  toggle();
};

const resetErrorForm = () => {
  Object.assign(errorForm, initialBox);
};

const toggle = () => {
  modal.value = !modal.value;
};

const save = () => {
  // Asigna "Caja General" si el nombre está vacío
  const isUpdate = Boolean(box.id);
  const routeMethod = isUpdate ? "put" : "post";
  const routeName = isUpdate
    ? route("boxes.update", { id: box.id })
    : route("boxes.store");

  box[routeMethod](routeName, {
    onSuccess: () => {
      toggle();
      resetErrorForm();
      router.reload({ only: ["boxes"] });
    },
    onError: (error) => {
      resetErrorForm();
      if (error.response?.data?.errors) {
        Object.entries(error.response.data.errors).forEach(([key, value]) => {
          errorForm[key] = value[0];
        });
      } else {
        console.error("Error desconocido:", error);
        alert("Ocurrió un error inesperado. Por favor, intente nuevamente.");
      }
    },
  });
};

const update = (boxEdit) => {
  resetErrorForm();
  Object.keys(boxEdit).forEach((key) => {
    box[key] = boxEdit[key];
  });
  toggle();
};

const removeBox = (boxId) => {
  toggleDeleteBox();
  deleteid.value = boxId;
};

const toggleDeleteBox = () => {
  modalDeleteBox.value = !modalDeleteBox.value;
};

const deletebox = () => {
  router.delete(route("boxes.delete", deleteid.value), {
    onSuccess: () => {
      toggle1();
    },
    onError: (error) => {
      console.error("Error al eliminar la sucursal", error);
    },
  });
};

watch(
  search,
  async (newQuery) => {
    const url = route("boxes.index");
    try {
      await router.get(
        url,
        { search: newQuery, page: props.boxes.current_page },
        { preserveState: true }
      );
    } catch (error) {
      console.error("Error al filtrar:", error);
    }
  },
  { immediate: false }
);

const handlePageChange = async (page) => {
  const url = route("boxes.index");
  try {
    await router.get(
      url,
      { page, search: search.value },
      { preserveState: true }
    );
  } catch (error) {
    console.error("Error al paginar:", error);
  }
};

// Funciones para abrir y cerrar caja

// Función para mostrar el modal de apertura de caja.
// Se asigna el id de la caja que se va a abrir y se reinicia el campo initial_value.
const showOpenModal = (boxItem) => {
  currentBoxId.value = boxItem.id;
  openForm.initial_value = "";
  openForm.name = boxItem.name; // Si deseas mostrar el nombre en el modal
  modalOpenBox.value = true;
};

// Función que se ejecuta al enviar el formulario de apertura.
// Envía el monto (desde openForm.initial_value) al endpoint para abrir la caja.
const submitOpen = () => {
  openForm.post(route("boxes.open", currentBoxId.value), {
    onSuccess: () => {
      modalOpenBox.value = false;
    },
  });
};

// Función para invocar el modal de apertura (se usa en el botón "Abrir Caja")
const openBox = (box) => {
  showOpenModal(box);
};

// Nuevas referencias para el modal de cierre y los datos
const closeModal = ref(false);
const boxid = ref("");

watch(
  () => openForm.real_balance,
  (newValue) => {
    openForm.cash_difference = newValue - openForm.balance;
  }
);
// Función para cerrar caja y obtener los datos
const closeBox = (boxId) => {
  boxid.value = boxId;
  axios
    .get(route("boxes.closeinformation", boxId))
    .then((response) => {
      Object.assign(openForm, response.data);
      openForm.real_balance = response.data.balance;
      openForm.cash_difference = 0;
      closeModal.value = true; // Mostrar el modal después de actualizar los datos
    })
    .catch((error) => {
      console.error("Error al cerrar la caja", error);
    });
};

const closeboxfinally = () => {
  openForm.post(route("boxes.close", boxid.value), {
    onSuccess: () => {
      closeModal.value = false;
    },
  });
};
</script>

<template>
  <AdminLayout title="Cajas">
    <!-- Card -->
    <div class="p-4 bg-white rounded drop-shadow-md">
      <!-- Card Header -->
      <div class="flex flex-col sm:flex-row justify-between items-center">
        <h2 class="text-sm sm:text-lg font-bold w-full pb-2 sm:pb-0">Cajas</h2>
        <div class="w-full flex sm:justify-end">
          <TextInput
            v-model="search"
            type="search"
            class="block sm:mr-2 h-8 w-full"
            placeholder="Buscar ..."
          />
        </div>
        <button
          @click="newBox"
          class="mt-2 sm:mt-0 px-2 bg-success dark:bg-green-600 hover:bg-successhover text-2xl text-white rounded font-bold"
        >
          +
        </button>
      </div>

      <Table>
        <thead>
          <tr class="[&>th]:py-2">
            <th class="w-1">N°</th>
            <th class="text-left">NOMBRE</th>
            <th class="text-left">RESPONSABLE</th>
            <th class="text-left">SALDO</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="(box, i) in props.boxes.data"
            :key="box.id"
            class="border-t [&>td]:py-2"
          >
            <td>{{ i + 1 }}</td>
            <td class="text-left">{{ box.name }}</td>
            <td class="text-left ml-2">{{ box.employee_name }}</td>
            <td class="text-left ml-2">{{ box.balance || 0 }}</td>

            <td class="flex justify-end">
              <div class="relative inline-flex gap-1">
                <button
                  v-if="box.state_box !== undefined && box.state_box !== 'open'"
                  class="rounded px-2 py-1 bg-green-600 hover:bg-green-700 text-white"
                  @click="openBox(box)"
                >
                  Abrir Caja
                </button>
                <button
                  v-else
                  class="rounded px-2 py-1 bg-yellow-600 hover:bg-yellow-700 text-white"
                  @click="closeBox(box.id)"
                >
                  Cerrar Caja
                </button>
                <button
                  class="rounded px-1 py-1 bg-danger hover:bg-dangerhover text-white"
                  @click="removeBox(box.id)"
                >
                  <TrashIcon class="size-6 text-white" />
                </button>
                <button
                  class="rounded px-2 py-1 bg-primary hover:bg-primaryhover text-white"
                  @click="update(box)"
                >
                  <PencilIcon class="size-4 text-white" />
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </Table>
    </div>
    <Paginate :page="props.boxes" @page-change="handlePageChange" />
  </AdminLayout>

  <!-- Modal para crear/editar caja -->
  <FormModal
    :show="modal"
    :box="box"
    :employees="employees"
    :error="errorForm"
    @close="toggle"
    @save="save"
  />

  <!-- Modal para abrir caja (cash_session) -->
  <OpenModal
    :show="modalOpenBox"
    :openForm="openForm"
    :error="errorForm"
    @close="modalOpenBox = false"
    @save="submitOpen"
  />

  <!-- Modal de confirmación para eliminar caja -->
  <ConfirmationModal :show="modalDeleteBox">
    <template #title>ELIMINAR CAJAS</template>
    <template #content>¿Está seguro de eliminar la caja?</template>
    <template #footer>
      <SecondaryButton @click="modalDeleteBox = !modalDeleteBox" class="mr-2">
        Cancelar
      </SecondaryButton>
      <PrimaryButton type="button" @click="deletebox"> Aceptar </PrimaryButton>
    </template>
  </ConfirmationModal>

  <ConfirmationModal :show="closeModal">
    <template #title>Resumen de Cierre</template>
    <template #content>
      <p class="text-justify">
        <strong>Valor Inicial:</strong> {{ openForm.initial_value.toFixed(2) }}
      </p>
      <p class="text-justify">
        <strong>Ingresos:</strong> {{ openForm.ingress.toFixed(2) }}
      </p>
      <p class="text-justify">
        <strong>Egresos:</strong> {{ openForm.egress.toFixed(2) }}
      </p>
      <p class="text-justify">
        <strong>Saldo de caja(sistema):</strong>
        {{ openForm.balance.toFixed(2) }}
      </p>
      <p class="text-justify">
        <strong>Saldo de caja(real):</strong>
        <input type="number" v-model="openForm.real_balance" />
      </p>
      <p class="text-justify">
        <strong>Diferencia:</strong> {{ openForm.cash_difference.toFixed(2) }}
      </p>
    </template>
    <template #footer>
      <SecondaryButton @click="closeModal = false">Cancelar</SecondaryButton>
      <PrimaryButton @click="closeboxfinally" :disabled="openForm.processing"
        >Cerrar Caja</PrimaryButton
      >
    </template>
  </ConfirmationModal>
</template>
