<script setup lang="ts">
// Imports
import { ref, watch } from "vue";
import AdminLayout from "@/Layouts/AdminLayout.vue";
import { router, Link } from "@inertiajs/vue3";
import axios from "axios";
import { TextInput, Paginate, Table } from "@/Components";
import { Filters, GeneralRequest, TransactionBank } from "@/types";

// Props
const props = defineProps<{
  transactions: GeneralRequest<TransactionBank>;
  filters: Filters;
}>();

// Refs
const deleteId = ref(0);
const search = ref(props.filters.search); // Término de búsqueda

const deleteTransaction = () => {
  axios
    .delete(route("transactions.delete", deleteId.value)) // Eliminar centro de costos
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
    try {
      await router.get(
        url,
        { search: newQuery, page: props.transactions.current_page }, // Mantener la página actual
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
  <AdminLayout title="Movimientos bancarios">
    <!-- Card -->
    <div class="p-4 bg-white rounded drop-shadow-md">
      <!-- Card Header -->
      <div class="flex flex-col sm:flex-row justify-between items-center">
        <h2 class="text-sm sm:text-lg font-bold w-full pb-2 sm:pb-0">
          Movimientos bancarios
        </h2>
        <div class="w-full flex sm:justify-end">
          <TextInput v-model="search" type="search" class="block sm:mr-2 h-8 w-full" placeholder="Buscar ..." />
        </div>
        <Link :href="route('transactions.create')"
          class="mt-2 sm:mt-0 px-2 bg-success dark:bg-green-600 hover:bg-successhover text-2xl text-white rounded font-bold">
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
          <tr v-for="(transaction, i) in props.transactions.data" :key="transaction.id" class="border-t [&>td]:py-2">
            <td>{{ i + 1 }}</td>
            <td class="text-left p-1">
              {{ transaction.name }}
            </td>
            <td class="text-left p-1">
              {{ transaction.account_number }}
            </td>
            <td class="text-left p-1">{{ transaction.description }}</td>
            <td class="text-right p-1">{{ transaction.amount }}</td>
          </tr>
        </tbody>
      </Table>
    </div>
    <Paginate :page="props.transactions" />
  </AdminLayout>
</template>
