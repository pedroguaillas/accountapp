<script setup>
// Importaciones
import { Link, router } from "@inertiajs/vue3";
import { ref } from "vue";
import Table from "@/Components/Table.vue";
import axios from "axios";
import ConfirmationModal from "@/Components/ConfirmationModal.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import AdminLayout from "@/Layouts/AdminLayout.vue";
// Props
const props=defineProps({
  fixedAssetss: { type: Object, default: () => ({}) },
  filters: { type: Object, default: () => ({}) },
});

const modal = ref(false);
const deleteid = ref(0);

const toggle = () => {
  modal.value = !modal.value;
};

const removeFixedAsset = (fixedAssetId) => {
  toggle();
  deleteid.value = fixedAssetId;
};

const deletefixed = () => {
  axios
    .delete(route("fixedassets.delete", deleteid.value))
    .then(() => {
      router.visit(route("fixedassets.index")); // Redirige a la ruta deseada
    })
    .catch((error) => {
      console.error("Error al eliminar el activo fijo", error);
    });
};
</script>


<template>
    <AdminLayout title="Activos fijos">
  <!-- Tarjeta de Activos Fijos -->
  <div class="p-4 bg-white rounded drop-shadow-md">
    <div class="flex justify-between items-center">
      <h2 class="text-sm sm:text-lg font-bold">Activos fijos</h2>
      <div class="flex justify-end mb-3">
        <Link
          :href="route('fixedassets.create')"
          class="px-2 bg-green-500 dark:bg-green-600 text-2xl text-white rounded font-bold"
        >
          +
        </Link>
      </div>
    </div>

    <!-- Tabla de Activos Fijos -->
    <div class="w-full overflow-x-auto">
      <Table>
        <thead>
          <tr class="[&>th]:py-2">
            <th class="w-1">N°</th>
            <th>CODIGO</th>
            <th>FECHA</th>
            <th>DETALLE</th>
            <th>VALOR</th>
            <th class="w-1">ACCIONES</th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="(fixe, i) in props.fixedAssetss.data"
            :key="fixe.id"
            class="border-t [&>td]:py-2"
          >
            <td>{{ i + 1 }}</td>
            <td>{{ fixe.code ?? "" }}</td>
            <td>
              {{ fixe.date_acquisition ?? "" }}
            </td>
            <td>{{ fixe.detail ?? "" }}</td>
            <td>
              {{ fixe.value }}
            </td>
            <td>
              <div class="relative inline-flex">
                <Link
                  :href="route('fixedassets.edit', fixe.id)"
                  class="rounded px-2 py-1 bg-blue-500 text-white"
                >
                  <i class="fa fa-edit"> </i> Modificar
                </Link>
              </div>
              <button
                class="rounded px-2 py-1 bg-red-500 text-white"
                @click="removeFixedAsset(fixe.id)"
              >
                <i class="fa fa-trash"></i> Eliminar
              </button>
            </td>
          </tr>
        </tbody>
      </Table>
    </div>
  </div>
</AdminLayout>
  <ConfirmationModal :show="modal">
    <template #title> ELIMINAR ACTIVOS FIJOS </template>
    <template #content> Esta seguro de eliminar el activo fijo? </template>
    <template #footer>
      <PrimaryButton type="button" @click="deletefixed">Aceptar</PrimaryButton>
    </template>
  </ConfirmationModal>
</template>


