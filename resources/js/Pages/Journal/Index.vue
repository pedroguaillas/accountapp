<script setup>

// Imports
import Table from "@/Components/Table.vue";
import { ref, watch } from "vue";
import AdminLayout from "@/Layouts/AdminLayout.vue";
import { Link, router } from "@inertiajs/vue3";
import ConfirmationModal from "@/Components/ConfirmationModal.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import Paginate from "@/Components/Paginate.vue";
import TextInput from "@/Components/TextInput.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";

// Props
const props = defineProps({
  journals: { type: Object, default: () => ({ data: [] }) },
  filters: { type: Object, default: () => ({}) },
});

// Refs
const deleteId = ref(0);
const modal = ref(false);
const search = ref(props.filters.search); // Término de búsqueda
const loading = ref(false); // Estado de carga

const toggle = () => {
  modal.value = !modal.value;
};

const removeJournals = (journalId) => {
  toggle();
  deleteId.value = journalId;
};

const deletejournarls = () => {
  axios
    .delete(route("journal.delete", deleteId.value))
    .then(() => {
      router.visit(route("journal.index")); // Redirige a la ruta deseada
    })
    .catch((error) => {
      console.error("Error al eliminar el asiento contable", error);
    });
};

watch(
  search,
  async (newQuery) => {
    if (loading.value) return
    loading.value = true; // Activa el indicador de carga
    const url = route("journal.index"); // Ruta del índice de sucursales
    try {
      await router.get(
        url,
        { search: newQuery, page: props.journals.current_page }, // Sin parámetros de búsqueda
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

// Función para manejar el cambio de página
const handlePageChange = async (page) => {
  if (loading.value) return
  loading.value = true;
  const url = route("journal.index"); // Ruta hacia el backend

  try {
    await router.get(
      url,
      { page, search: search.value }, // Incluye tanto la página como el término de búsqueda
      { preserveState: true }
    );
  } catch (error) {
    console.error("Error al paginar:", error);
  } finally {
    loading.value = false;
  }
};
</script>

<template>
  <AdminLayout title="Asientos contables">
    <div class="flex justify-end mb-3">
      <div class="w-full flex sm:justify-end">
        <TextInput v-model="search" type="search" class="block sm:mr-2 h-8 w-full" placeholder="Buscar ..." />
      </div>
      <Link :href="route('journal.create')"
        class="px-2 bg-green-500 dark:bg-green-600 text-2xl text-white rounded font-bold">
      +
      </Link>
    </div>

    <!-- Card -->
    <div v-for="journal in props.journals.data" :key="journal.id" class="p-4 bg-white rounded drop-shadow-md mb-3">
      <!-- Card Header -->
      <div class="items-center">
        <h2 class="text-sm sm:text-lg">
          <strong class="font-bold">Descripción:</strong>
          {{ journal.description }}
        </h2>

        <p>
          <strong class="font-bold">Fecha:</strong>
          {{ journal.date }}
        </p>
      </div>

      <Table>
        <thead>
          <tr>
            <th class="text-left w-24">CODIGO</th>
            <th class="text-left">NOMBRE DE LA CUENTA</th>
            <th class="text-right w-24">DEBITO</th>
            <th class="text-right w-24">CREDITO</th>
          </tr>
        </thead>
        <tbody class="h-6">
          <tr v-for="(entry, j) in journal.journal_entries" :key="`journal-${i}-entry-${j}`">
            <td class="text-left">{{ entry.code }}</td>
            <td class="text-left">{{ entry.name }}</td>
            <td class="text-right">{{ entry.debit.toFixed(2) }}</td>
            <td class="text-right">{{ entry.have.toFixed(2) }}</td>
          </tr>
        </tbody>
        <tfoot>
          <tr>
            <th class="text-left w-24"></th>
            <th class="text-left">TOTAL</th>
            <th class="text-right w-24">{{ journal.total.toFixed(2) }}</th>
            <th class="text-right w-24">{{ journal.total.toFixed(2) }}</th>
          </tr>
        </tfoot>
      </Table>
      <div class="flex flex-row justify-end gap-2">
        <Link :href="route('journal.edit', journal.id)" class="rounded px-2 py-1 bg-blue-500 text-white">
        <i class="fa fa-edit"> </i> Modificar
        </Link>
        <button class="rounded px-2 py-1 bg-red-500 text-white" @click="removeJournals(journal.id)">
          <i class="fa fa-trash"></i> Eliminar
        </button>
      </div>
    </div>
    <Paginate :page="props.journals" @page-change="handlePageChange" />
  </AdminLayout>

  <ConfirmationModal :show="modal" maxWidth="lg">
    <template #title>Eliminar asiento contable</template>
    <template #content> Esta seguro de eliminar el asiento contable? </template>
    <template #footer>
      <SecondaryButton @click="modal=!modal" class="mr-2">Cancelar</SecondaryButton>
      <PrimaryButton type="button" @click="deletejournarls">Aceptar</PrimaryButton>
    </template>
  </ConfirmationModal>
</template>
