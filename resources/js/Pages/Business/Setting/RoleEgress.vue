  <script setup>
// Imports
import BusinessSettingLayout from "@/Layouts/BusinessSettingLayout.vue";
import ModalEgress from "./ModalEgress.vue";
import { router, useForm } from "@inertiajs/vue3";
import { ref, reactive, watch } from "vue";
import axios from "axios";
import Table from "@/Components/Table.vue";
import ConfirmationModal from "@/Components/ConfirmationModal.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import Paginate from "@/Components/Paginate.vue";
import { TrashIcon, PencilIcon } from "@heroicons/vue/24/solid";

const props = defineProps({
  egresses: { type: Object, default: () => ({ data: [] }) },
});

// Refs
const modalegreses = ref(false);
const deleteid = ref(0);

// Initial cost center object
const initialEgress = { name: "", code: "", type: "otro" };

// Reactives
const egress = useForm({ ...initialEgress });
const errorForm = reactive({});

const newEgress = () => {
  if (egress.id !== undefined) {
    delete egress.id;
  }
  Object.assign(egress, initialEgress);
  togglei();
};

const resetErrorFormE = () => {
  Object.assign(errorForm, initialEgress);
};

const togglee = () => {
  modalegreses.value = !modalegreses.value;
};

const savee = () => {
  // Validar los campos obligatorios
  if (!egress.name || !egress.code) {
    alert("Por favor, complete todos los campos requeridos.");
    return;
  }

  // Determinar el método HTTP y la ruta correspondiente
  const isUpdate = Boolean(ingress.id);
  const routeMethod = isUpdate ? "put" : "post";
  const routeName = isUpdate
    ? route("roleegress.update", { id: ingress.id }) // Asegurarte de que uses el formato correcto
    : route("roleegress.store");

  // Preparar la solicitud
  egress[routeMethod](routeName, {
    onSuccess: () => {
      togglee(); // Cerrar modal o reiniciar estados
      resetErrorFormE(); // Limpiar errores del formulario
      router.reload({ only: ["roleegresses"] }); // Recargar datos
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

const update = (egressEdit) => {
  resetErrorForm();
  Object.assign(egress, egressEdit);
  togglee();
};

const modal1 = ref(false);

const toggle1 = () => {
  modal1.value = !modal1.value;
};

const removeEgress = (egressId) => {
  toggle1();
  deleteid.value = ingressId;
};

const deleteegress = () => {
  axios
    .delete(route("rolaegress.delete", deleteid.value)) // Eliminar centro de costos
    .then(() => {
      // Después de eliminar el centro de costos, redirigir a la ruta deseada
      router.visit(route("roleegress.index"));
    })
    .catch((error) => {
      console.error("Error al eliminar egresos", error);
    });
};
</script>

<template>
    <div class="p-4 bg-white rounded drop-shadow-md">
      <!-- Card Header -->
      <div class="flex flex-col sm:flex-row justify-between items-center">
        <h2 class="text-sm sm:text-lg font-bold w-full pb-2 sm:pb-0">
          Egresos
        </h2>

        <button
          @click="newEgress"
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
              <th>CODIGO</th>
              <th>NOMBRE</th>
              <th class="w-1"></th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="(egress, i) in props.egresses.data"
              :key="egress.id"
              class="border-t [&>td]:py-2"
            >
              <td>{{ i + 1 }}</td>
              <td>{{ egress.code }}</td>
              <td>{{ eagress.name }}</td>

              <td class="flex justify-end">
                <div class="relative inline-flex gap-1">
                  <button
                    class="rounded px-1 py-1 bg-red-500 text-white"
                    @click="removeegress(egress.id)"
                  >
                    <TrashIcon class="size-6 text-white" />
                  </button>
                  <button
                    class="rounded px-2 py-1 bg-blue-500 text-white"
                    @click="update(egress)"
                  >
                    <PencilIcon class="size-4 text-white" />
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </Table>
      </div>
    </div>

  <!-- Modal Component for CostCenter -->
  <ModalEgress
    :show="modalegreses"
    :egress="egress"
    :error="errorForm"
    @close="togglee"
    @save="savee"
  />
  <ConfirmationModal :show="modal1">
    <template #title> ELIMINAR EGRESOS</template>
    <template #content> Esta seguro de eliminar el egreso? </template>
    <template #footer>
      <PrimaryButton type="button" @click="deleteegress"
        >Aceptar</PrimaryButton
      >
    </template>
  </ConfirmationModal>
</template>