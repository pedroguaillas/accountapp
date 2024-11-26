<script setup>
// Importaciones
import { Link } from "@inertiajs/vue3";
import Table from "@/Components/Table.vue";

// Props
defineProps({
  depreciations: { type: Array, default: () => [] },
});
</script>

<template>
  <!-- Tarjeta de Depreciación -->
  <div class="p-4 bg-white rounded drop-shadow-md">
    <div class="flex justify-between items-center">
      <h2 class="text-sm sm:text-lg font-bold">Depreciación</h2>
      <div class="flex justify-end mb-3">
        <Link
         
          class="px-2 bg-green-500 dark:bg-green-600 text-2xl text-white rounded font-bold"
        >
          +
        </Link>
      </div>
    </div>

    <!-- Tabla de Depreciación -->
    <div class="w-full overflow-x-auto">
      <Table>
        <thead>
          <tr class="[&>th]:py-2">
            <th class="w-1">N°</th>
            <th>ACTIVO</th>
            <th>FECHA</th>
            <th>VALOR</th>
            <th>DEPRECIACIÓN</th>
            <th class="w-1">ACCIONES</th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="(fixe, i) in  depreciations"
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
            </td>
          </tr>
        </tbody>
      </Table>
    </div>
  </div>
</template>
