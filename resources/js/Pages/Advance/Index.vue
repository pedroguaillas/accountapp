<script setup lang="ts">
// Imports
import { ref, watch } from "vue";
import AdminLayout from "@/Layouts/AdminLayout.vue";
import { router, Link } from "@inertiajs/vue3";
import axios from "axios";
import { ConfirmationModal, TextInput, SecondaryButton, PrimaryButton, Table, Paginate } from "@/Components";
import { TrashIcon } from "@heroicons/vue/24/solid";
import { Advance, Filters, GeneralRequest } from "@/types";

//Props
const props = defineProps<{
  advances: GeneralRequest<Advance>; // Paginación de los bancos
  filters: Filters; // Filtros aplicados
}>();

// Refs
const search = ref(props.filters.search); // Término de búsqueda
const modal1 = ref(false);

const deleteid = ref<Number>(0);

const toggleDelete = () => {
  modal1.value = !modal1.value;
};

const removeAdvance = (advanceId: Number) => {
  toggleDelete();
  deleteid.value = advanceId;
};

const deleteadvance = () => {
  axios
    .delete(route("advances.delete", deleteid.value))
    .then(() => {
      router.visit(route("advances.index")); // Redirige a la ruta deseada
    })
    .catch((error) => {
      console.error("Error al eliminar el adelanto", error);
    });
};

watch(
  search,
  async (newQuery) => {
    const url = route("advances.index");

    try {
      await router.get(
        url,
        { search: newQuery, page: props.advances.current_page }, // Mantener la página actual
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
  <AdminLayout title="Adelantos">
    <!-- Card -->
    <div class="p-4 bg-white rounded drop-shadow-md">
      <!-- Card Header -->
      <div class="flex flex-col sm:flex-row justify-between items-center">
        <h2 class="text-sm sm:text-lg font-bold w-full pb-2 sm:pb-0">
          Adelantos de sueldo
        </h2>
        <div class="w-full flex justify-end">
          <TextInput v-model="search" type="text" class="mt-1 block w-[50%] mr-2 h-8" minlength="3" maxlength="300"
            required placeholder="Buscar..." />
        </div>
        <Link :href="route('advances.create')"
          class="mt-2 sm:mt-0 px-2 bg-success dark:bg-green-600 hover:bg-successhover text-2xl text-white rounded font-bold">
        +
        </Link>
      </div>

      <!-- Resposive -->
      <div class="w-full overflow-x-auto">
        <!-- Tabla -->
        <Table>
          <thead>
            <tr class="[&>th]:py-2">
              <th class="w-1">N°</th>
              <th>CEDULA</th>
              <th class="text-left">NOMBRE</th>
              <th class="text-right pr-4">ADELANTO</th>
              <th class="w-1"></th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(advance, i) in props.advances.data" :key="advance.id" class="border-t [&>td]:py-2">
              <td>{{ i + 1 }}</td>
              <td>{{ advance.cuit }}</td>
              <td class="text-left">{{ advance.name }}</td>
              <td class="text-right pr-4">{{ advance.amount }}</td>

              <td class="flex justify-end">
                <div class="relative inline-flex gap-1">
                  <button class="rounded px-1 py-1 bg-danger hover:bg-dangerhover text-white"
                    @click="() => advance.id && removeAdvance(advance.id)">
                    <TrashIcon class="size-6 text-white" />
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </Table>
      </div>
    </div>
    <Paginate :page="props.advances" />
  </AdminLayout>

  <ConfirmationModal :show="modal1">
    <template #title> ELIMINAR ADELANTOS</template>
    <template #content> Esta seguro de eliminar el adelanto? </template>
    <template #footer>
      <SecondaryButton @click="modal1 = !modal1" class="mr-2">Cancelar</SecondaryButton>
      <PrimaryButton type="button" @click="deleteadvance">Aceptar</PrimaryButton>
    </template>
  </ConfirmationModal>
</template>