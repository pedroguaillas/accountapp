<script setup lang="ts">
// Importaciones
import { Link, router } from "@inertiajs/vue3";
import { ref, watch } from "vue";
import AdminLayout from "@/Layouts/AdminLayout.vue";
import { SecondaryButton, TextInput, Paginate, PrimaryButton, ConfirmationModal, Table } from "@/Components";
import { TrashIcon, PencilIcon } from "@heroicons/vue/24/solid";
import { IntangibleAsset } from "@/types/intangible-asset";
import { Filters, GeneralRequest } from "@/types";

// Props
const props = defineProps<{
  intangibleAssetss: GeneralRequest<IntangibleAsset>; // Paginación de los bancos
  filters: Filters; // Filtros aplicados
}>();

const modal = ref(false);
const deleteID = ref<Number>(0);
const search = ref(props.filters.search); // Término de búsqueda
const loading = ref(false); // Estado de carga

const toggle = () => {
  modal.value = !modal.value;
};

const removeIntangibleAsset = (intangibleId: number) => {
  toggle();
  deleteID.value = intangibleId;
};

const deleteintangible = () => {
  router.delete(route("intangibleassets.delete", deleteID.value), {
  });
};

watch(
  search,

  async (newQuery) => {
    const url = route("intangibleassets.index");
    loading.value = true;

    try {
      await router.get(
        url,
        { search: newQuery, page: props.intangibleAssetss.current_page }, // Mantener la página actual
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
</script>

<template>
  <AdminLayout title="Activos intangibles">
    <!-- Tarjeta de Activos Fijos -->

    <div class="p-4 bg-white rounded drop-shadow-md">
      <div class="flex flex-col sm:flex-row justify-between items-center">
        <h2 class="text-sm sm:text-lg font-bold w-full pb-2 sm:pb-0">
          Activos Intangibles
        </h2>
        <div class="w-full flex sm:justify-end">
          <TextInput v-model="search" type="search" class="block sm:mr-2 h-8 w-full" placeholder="Buscar ..." />
        </div>

        <Link :href="route('intangibleassets.create')"
          class="mt-2 sm:mt-0 px-2 bg-success dark:bg-green-600 hover:bg-successhover text-2xl text-white rounded font-bold">
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
            <tr v-for="(inta, i) in props.intangibleAssetss.data" :key="inta.id" class="border-t [&>td]:py-2">
              <td>{{ i + 1 }}</td>
              <td>{{ inta.code ?? "" }}</td>
              <td>
                {{ inta.date_acquisition ?? "" }}
              </td>
              <td>{{ inta.detail ?? "" }}</td>
              <td>
                {{ inta.value }}
              </td>

              <td class="flex justify-end">
                <div class="relative inline-flex gap-1">
                  <button class="rounded px-1 py-1 bg-danger hover:bg-dangerhover text-white"
                    @click="() => inta.id && removeIntangibleAsset(inta.id)">
                    <TrashIcon class="size-6 text-white" />
                  </button>
                  <Link class="rounded px-2 py-1 bg-primary hover:bg-primaryhover text-white"
                    :href="route('intangibleassets.edit', inta.id)">
                  <PencilIcon class="size-4 text-white" />
                  </Link>
                </div>
              </td>
            </tr>
          </tbody>
        </Table>
      </div>
    </div>
    <Paginate :page="props.intangibleAssetss" />
  </AdminLayout>

  <ConfirmationModal :show="modal">
    <template #title> ELIMINAR ACTIVOS FIJOS </template>
    <template #content> Esta seguro de eliminar el activo fijo? </template>
    <template #footer>
      <SecondaryButton @click="modal = !modal" class="mr-2">Cancelar</SecondaryButton>
      <PrimaryButton type="button" @click="deleteintangible">Aceptar</PrimaryButton>
    </template>
  </ConfirmationModal>
</template>
