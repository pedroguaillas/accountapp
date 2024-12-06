<script setup>
// Importaciones
import { Link, router } from "@inertiajs/vue3";
import { ref, watch } from "vue";
import Table from "@/Components/Table.vue";
import axios from "axios";
import ConfirmationModal from "@/Components/ConfirmationModal.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import AdminLayout from "@/Layouts/AdminLayout.vue";
import TextInput from "@/Components/TextInput.vue";
import Paginate from "@/Components/Paginate.vue";

// Props
const props = defineProps({
  fixedAssetss: { type: Object, default: () => ({}) },
  filters: { type: Object, default: () => ({}) },
});

const modal = ref(false);
const deleteid = ref(0);
const search = ref(props.filters.search); // Término de búsqueda
const loading = ref(false); // Estado de carga
const currentUrl = ref(window.location.href);

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

const reloadPage = async (url) => {

    await router.get(url, { search }, { preserveState: true });

};

watch(
  search,
  async (newQuery) => {
    const url = route("fixedassets.index"); // Ruta del índice de sucursales
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
  <AdminLayout title="Activos fijos">
    <!-- Tarjeta de Activos Fijos -->
    <div class="p-4 bg-white rounded drop-shadow-md">
      <div class="flex flex-col sm:flex-row justify-between items-center">
        <h2 class="text-sm sm:text-lg font-bold w-full pb-2 sm:pb-0">
          Activos fijos
        </h2>
        <div class="w-full flex sm:justify-end">
          <TextInput
            v-model="search"
            type="search"
            class="block sm:mr-2 h-8 w-full"
            placeholder="Buscar ..."
          />
        </div>

        <Link
          :href="route('fixedassets.create')"
          class="mt-2 sm:mt-0 px-2 bg-green-500 dark:bg-green-600 text-2xl text-white rounded font-bold"
        >
          +
        </Link>
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
        <Paginate :page="props.fixedAssetss" @reloadPage="reloadPage" />
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


