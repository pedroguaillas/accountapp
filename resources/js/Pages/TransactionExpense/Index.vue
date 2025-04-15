<script setup lang="ts">
// Imports
import { ref, watch } from "vue";
import AdminLayout from "@/Layouts/AdminLayout.vue";
import { router, Link } from "@inertiajs/vue3";
import { TextInput, Table } from "@/Components";
import { Expense, Filters, GeneralRequest } from "@/types";

//Props
const props = defineProps<{
  expenses: GeneralRequest<Expense>; // Paginación de los bancos
  filters: Filters; // Filtros aplicados
}>();


const search = ref(props.filters.search); // Término de búsqueda

watch(
  search,

  async (newQuery) => {
    const url = route("transaction.expenses.index");

    try {
      await router.get(
        url,
        { search: newQuery, page: props.expenses.current_page }, // Mantener la página actual
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
  <AdminLayout title="Gastos">
    <!-- Card -->
    <div class="p-4 bg-white rounded drop-shadow-md">
      <!-- Card Header -->
      <div class="flex flex-col sm:flex-row justify-between items-center">
        <h2 class="text-sm sm:text-lg font-bold w-full pb-2 sm:pb-0">
          Gastos
        </h2>
        <div class="w-full flex justify-end">
          <TextInput v-model="search" type="text" class="mt-1 block w-[50%] mr-2 h-8" minlength="3" maxlength="300"
            required placeholder="Buscar..." />
        </div>
        <Link :href="route('transaction.expenses.create')"
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
              <th class="text-left">GASTO</th>
              <th class="text-left">DESCRIPCION</th>
              <th class="text-right pr-4">MONTO</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(expense, i) in props.expenses.data" :key="expense.id" class="border-t [&>td]:py-2">
              <td>{{ i + 1 }}</td>
              <td class="text-left">{{ expense.name }}</td>
              <td class="text-left">{{ expense.description }}</td>
              <td class="text-right pr-4">{{ expense.amount }}</td>
            </tr>
          </tbody>
        </Table>
      </div>
    </div>
    <!-- <Paginate :page="props.expenses" @page-change="handlePageChange" /> -->
  </AdminLayout>
</template>