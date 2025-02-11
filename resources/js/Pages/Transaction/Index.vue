<script setup>
// Imports
import { ref, watch } from "vue";
import AdminLayout from "@/Layouts/AdminLayout.vue";
import { router, Link } from "@inertiajs/vue3";
import Table from "@/Components/Table.vue";
import axios from "axios";
import ConfirmationModal from "@/Components/ConfirmationModal.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import Paginate from "@/Components/Paginate.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import { TrashIcon, PencilIcon } from "@heroicons/vue/24/solid";

// Props
const props = defineProps({
  transactions: { type: Object, default: () => ({}) },
  filters: { type: Object, default: () => ({}) },
});

// Refs
const modal1 = ref(false);
const deleteid = ref(0);
const search = ref(props.filters.search); // Término de búsqueda
const loading = ref(false); // Estado de carga

const toggle1 = () => {
  modal1.value = !modal1.value;
};

const removeTransactions = (transactionId) => {
  toggle1();
  deleteid.value = transactionId;
};

const deleteTransaction = () => {
  axios
    .delete(route("transactions.delete", deleteid.value)) // Eliminar centro de costos
    .then(() => {
      // Después de eliminar el centro de costos, redirigir a la ruta deseada
      router.visit(route("transactions.index"));
    })
    .catch((error) => {
      if (error.response) {
        // Si el backend envía un error con status code
        console.error("Error del backend:", error.response.data);

        // Mostrar el mensaje de error del backend
        alert(
          error.response.data.error ||
            "Ocurrió un error al eliminar la transacción."
        );
      }
    });
};

watch(
  search,

  async (newQuery) => {
    const url = route("transactions.index");
    loading.value = true;
    try {
      await router.get(
        url,
        { search: newQuery, page: props.transactions.current_page }, // Mantener la página actual
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
  const url = route("transactions.index"); // Ruta hacia el backend
  loading.value = true;
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
  <AdminLayout title="Movimientos bancarios">
    <!-- Card -->
    <div class="p-4 bg-white rounded drop-shadow-md">
      <!-- Card Header -->
      <div class="flex flex-col sm:flex-row justify-between items-center">
        <h2 class="text-sm sm:text-lg font-bold w-full pb-2 sm:pb-0">
          Movimientos bancarios
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
          :href="route('transactions.create')"
          class="mt-2 sm:mt-0 px-2 bg-success dark:bg-green-600 hover:bg-successhover text-2xl text-white rounded font-bold"
        >
          +
        </Link>
      </div>

      <Table>
        <thead>
          <tr class="[&>th]:py-2">
            <th class="w-1">N°</th>
            <th class="text-left p-1">BANCO</th>
            <th class="text-left p-1">CUENTA</th>
            <th class="text-left p-1">DESCRIPCION</th>
            <th class="text-right p-1">MONTO</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="(transaction, i) in props.transactions.data"
            :key="transaction.id"
            class="border-t [&>td]:py-2"
          >
            <td>{{ i + 1 }}</td>
            <td class="text-left p-1">
              {{ transaction.name }}
            </td>
            <td class="text-left p-1">
              {{ transaction.account_number }}
            </td>
            <td class="text-left p-1">{{ transaction.description }}</td>
            <td class="text-right p-1">{{ transaction.amount }}</td>
            <td class="flex justify-end">
              <div class="relative inline-flex gap-1">
                <button
                  class="rounded px-1 py-1 bg-danger hover:bg-dangerhover text-white"
                  @click="removeTransactions(transaction.id)"
                >
                  <TrashIcon class="size-6 text-white" />
                </button>
                <Link
                  :href="route('transactions.edit', transaction.id)"
                  class="rounded px-2 py-1 bg-primary hover:bg-primaryhover text-white"
                >
                  <PencilIcon class="size-4 text-white" />
                </Link>
              </div>
            </td>
          </tr>
        </tbody>
      </Table>
    </div>
    <Paginate :page="props.transactions" @page-change="handlePageChange" />
  </AdminLayout>

  <ConfirmationModal :show="modal1">
    <template #title> ELIMINAR BANCOS</template>
    <template #content>
      Esta seguro de eliminar el movimiento bancario?
    </template>
    <template #footer>
      <SecondaryButton @click="modal1 = !modal1" class="mr-2"
        >Cancelar</SecondaryButton
      >
      <PrimaryButton type="button" @click="deleteTransaction"
        >Aceptar</PrimaryButton
      >
    </template>
  </ConfirmationModal>
</template>
