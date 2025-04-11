<script setup lang="ts">
// Imports
import { Link, router } from "@inertiajs/vue3";
import { ref, watch } from "vue";
import AdminLayout from "@/Layouts/AdminLayout.vue";
import { ConfirmationModal, TextInput, SecondaryButton, PrimaryButton, Table, Paginate } from "@/Components";
import { TrashIcon, PencilIcon, ArrowTrendingDownIcon, } from "@heroicons/vue/24/solid";
import { Filters, FixedAsset, GeneralRequest } from "@/types";

// Props
const props = defineProps<{
  fixedAssetss: GeneralRequest<FixedAsset>; // Paginación de los bancos
  filters: Filters; // Filtros aplicados
}>();

const modal = ref(false);
const deleteId = ref<Number>(0);
const search = ref(props.filters.search); // Término de búsqueda

const toggle = () => {
  modal.value = !modal.value;
};

const removeFixedAsset = (fixedAssetId: Number) => {
  toggle();
  deleteId.value = fixedAssetId;
};

const deleteFixed = () => {
  router.delete(route("fixedassets.delete", deleteId.value), {
    onSuccess: () => {
      toggle();
    },
    onError: (error) => {
      console.error("Error al eliminar el activo fijo", error);
    },
  });
};

watch(
  search,

  async (newQuery) => {
    const url = route("fixedassets.index");
    try {
      await router.get(
        url,
        { search: newQuery, page: props.fixedAssetss.current_page }, // Mantener la página actual
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
  <AdminLayout title="Activos fijos">
    <!-- Tarjeta de Activos Fijos -->
    <div class="p-4 bg-white rounded drop-shadow-md">
      <div class="flex flex-col sm:flex-row justify-between items-center">
        <h2 class="text-sm sm:text-lg font-bold w-full pb-2 sm:pb-0">
          Activos fijos
        </h2>
        <div class="w-full flex sm:justify-end">
          <TextInput v-model="search" type="search" class="block sm:mr-2 h-8 w-full" placeholder="Buscar ..." />
        </div>

        <Link :href="route('fixedassets.create')"
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
              <th class="text-left">DETALLE</th>
              <th>VALOR</th>
              <th class="w-1">ACCIONES</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(fixe, i) in props.fixedAssetss.data" :key="fixe.id" class="border-t [&>td]:py-2">
              <td>{{ i + 1 }}</td>
              <td>{{ fixe.code }}</td>
              <td>
                {{ fixe.date_acquisition }}
              </td>
              <td class="text-left">{{ fixe.detail }}</td>
              <td>
                {{ fixe.value }}
              </td>

              <td class="flex justify-end">
                <div class="relative inline-flex gap-1">
                  <button class="rounded px-1 py-1 bg-danger hover:bg-dangerhover text-white"
                    @click="() => fixe.id && removeFixedAsset(fixe.id)">
                    <TrashIcon class="size-6 text-white" />
                  </button>
                  <Link class="rounded px-2 py-1 bg-primary hover:bg-primaryhover text-white"
                    :href="route('fixedassets.edit', fixe.id)">
                  <PencilIcon class="size-4 text-white" />
                  </Link>
                  <Link class="rounded px-2 pt-1 pb-0 bg-blue-800 text-white"
                    :href="route('depreciations.index', fixe.id)">
                  <ArrowTrendingDownIcon class="size-4 text-white" />
                  </Link>
                </div>
              </td>
            </tr>
          </tbody>
        </Table>
      </div>
    </div>
    <Paginate :page="props.fixedAssetss" />
  </AdminLayout>
  <ConfirmationModal :show="modal">
    <template #title> ELIMINAR ACTIVOS FIJOS </template>
    <template #content> Esta seguro de eliminar el activo fijo? </template>
    <template #footer>
      <SecondaryButton @click="modal = !modal" class="mr-2">Cancelar</SecondaryButton>
      <PrimaryButton type="button" @click="deleteFixed">Aceptar</PrimaryButton>
    </template>
  </ConfirmationModal>
</template>