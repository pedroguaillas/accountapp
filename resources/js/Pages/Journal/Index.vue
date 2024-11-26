<script setup>
// Imports
import Table from "@/Components/Table.vue";
import AdminLayout from "@/Layouts/AdminLayout.vue";
import { Link } from "@inertiajs/vue3";

// Props
defineProps({
  journals: { type: Array, default: () => [] },
});
</script>

<template>
  <AdminLayout title="Asientos Contables">
    <div class="flex justify-end mb-3">
      <Link
        :href="route('journal.create')"
        class="px-2 bg-green-500 dark:bg-green-600 text-2xl text-white rounded font-bold"
      >
        +
      </Link>
    </div>

    <!-- Card -->
    <div
      v-for="journal in journals"
      :key="journal.id"
      class="p-4 bg-white rounded drop-shadow-md mb-3"
    >
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
          <tr
            v-for="(entry, j) in journal.journal_entries"
            :key="`journal-${i}-entry-${j}`"
          >
            <td class="text-left">{{ entry.code }}</td>
            <td class="text-left">{{ entry.name }}</td>
            <td class="text-right">{{ entry.debit }}</td>
            <td class="text-right">{{ entry.have }}</td>
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
    </div>
  </AdminLayout>
</template>
