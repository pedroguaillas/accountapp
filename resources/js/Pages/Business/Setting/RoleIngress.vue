<script setup lang="ts">
// Imports
import BusinessSettingLayout from "@/Layouts/BusinessSettingLayout.vue";
import ModalIngress from "./ModalIngress.vue";
import { router, useForm } from "@inertiajs/vue3";
import { ref, watch } from "vue";
import axios from "axios";
import { Table, TextInput, SecondaryButton, PrimaryButton, ConfirmationModal } from "@/Components";
import { TrashIcon, PencilIcon } from "@heroicons/vue/24/solid";
import { Errors, Filters, GeneralRequest, RoleIngress } from "@/types";

const props = defineProps<{
  ingresses: GeneralRequest<RoleIngress>;
  filters: Filters[];
}>();

// Refs
const modalingreses = ref(false);
const deleteId = ref(0);
const search = ref(props.filters[0].search);

// Initial cost center object
const initialIngress = { name: "", code: "", type: "otro" };

// Reactives
const ingress = useForm<RoleIngress>({ ...initialIngress });
const errorForm: Record<string, string> = {};
const modalDelete = ref(false);

const toggleDelete = () => {
  modalDelete.value = !modalDelete.value;
};

const newIngress = () => {
  if (ingress.id !== undefined) {
    delete ingress.id;
  }
  Object.assign(ingress, initialIngress);
  toggeIngress();
};

const resetErrorForm = () => {
  Object.assign(errorForm, initialIngress);
};

const toggeIngress = () => {
  modalingreses.value = !modalingreses.value;
};

const save = () => {
  // Validar los campos obligatorios
  if (!ingress.name || !ingress.code) {
    alert("Por favor, complete todos los campos requeridos.");
    return;
  }

  // Determinar el método HTTP y la ruta correspondiente
  const isUpdate = Boolean(ingress.id);
  const routeMethod = isUpdate ? "put" : "post";
  const routeName = isUpdate
    ? route("roleingresses.update", { id: ingress.id }) // Asegurarte de que uses el formato correcto
    : route("roleingresses.store");

  // Preparar la solicitud
  ingress[routeMethod](routeName, {
    onSuccess: () => {
      toggeIngress(); // Cerrar modal o reiniciar estados
      resetErrorForm(); // Limpiar errores del formulario
      router.reload({ only: ["ingresses"] }); // Recargar datos
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

const update = (ingressEdit: RoleIngress) => {
  resetErrorForm();
  Object.assign(ingress, ingressEdit);
  toggeIngress();
};

const removeIngress = (ingressId: number) => {
  toggleDelete();
  deleteId.value = ingressId;
};

const deleteingress = () => {
  axios
    .delete(route("roleingress.delete", deleteId.value)) // Eliminar centro de costos
    .then(() => {
      // Después de eliminar el centro de costos, redirigir a la ruta deseada
      router.visit(route("roleingress.index"));
    })
    .catch((error) => {
      console.error("Error al eliminar ingresos", error);
    });
};

watch(
  search,
  async (newQuery) => {
    const url = route("roleingress.index");
    try {
      await router.get(
        url,
        { search: newQuery }, // Mantener la página actual
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
  <BusinessSettingLayout title="Negocio ajustes de roles">
    <!-- Card Header -->
    <div class="flex flex-col sm:flex-row justify-end items-center">
      <div class="w-full flex sm:justify-end">
        <TextInput v-model="search" type="text" class="block sm:mr-2 h-8 w-full" placeholder="Buscar" />
      </div>
      <button @click="newIngress"
        class="sm:mt-0 px-2 bg-green-500 dark:bg-green-600 text-2xl text-white rounded font-bold">
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
            <th class="text-left">NOMBRE</th>
            <th class="w-1"></th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(ingress, i) in props.ingresses.data" :key="ingress.id" class="border-t [&>td]:py-2">
            <td>{{ i + 1 }}</td>
            <td>{{ ingress.code }}</td>
            <td class="text-left">{{ ingress.name }}</td>

            <td class="flex justify-end">
              <div class="relative inline-flex gap-1">
                <button v-if="ingress.type === 'otro'" class="rounded px-1 py-1 bg-red-500 text-white"
                  @click="()=> ingress.id && removeIngress(ingress.id)">
                  <TrashIcon class="size-6 text-white" />
                </button>
                <button v-if="ingress.type === 'otro'" class="rounded px-2 py-1 bg-blue-500 text-white"
                  @click="update(ingress)">
                  <PencilIcon class="size-4 text-white" />
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </Table>
    </div>
  </BusinessSettingLayout>

  <!-- Modal Component for CostCenter -->
  <ModalIngress :show="modalingreses" :ingress="ingress" :error="errorForm" @close="toggeIngress" @save="save" />
  <ConfirmationModal :show="modalDelete">
    <template #title> ELIMINAR INGRESOS</template>
    <template #content> Esta seguro de eliminar el ingreso? </template>
    <template #footer>
      <SecondaryButton @click="modalDelete = !modalDelete" class="mr-2">Cancelar</SecondaryButton>
      <PrimaryButton type="button" @click="deleteingress">Aceptar</PrimaryButton>
    </template>
  </ConfirmationModal>
</template>