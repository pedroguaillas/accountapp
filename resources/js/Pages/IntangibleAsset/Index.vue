<script setup>
// Importaciones
import { Link } from "@inertiajs/vue3";
import Table from "@/Components/Table.vue";

// Props
defineProps({
  intangibleAssetss: { type: Array, default: () => [] },
});
</script>

<template>
  <!-- Tarjeta de Activos Fijos -->
  <div class="p-4 bg-white rounded drop-shadow-md">
    <div class="flex justify-between items-center">
      <h2 class="text-sm sm:text-lg font-bold">Activos Intangibles</h2>
      <div class="flex justify-end mb-3">
        <Link
          :href="route('intangibleassets.create')"
          class="px-2 bg-green-500 dark:bg-green-600 text-2xl text-white rounded font-bold"
        >
          +
        </Link>
      </div>
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
            v-for="(inta, i) in intangibleAssetss"
            :key="inta.id"
            class="border-t [&>td]:py-2"
          >
            <td>{{ i + 1 }}</td>
            <td>{{ inta.code ?? "" }}</td>
            <td>
              {{ inta.date_acquisition ?? "" }}
            </td>
            <td>{{ inta.detail ?? "" }}</td>
            <td>
              {{ inta.value }}
            </td>
            <td>
              <div class="relative inline-flex">
                <Link
                  :href="route('intangibleassets.edit', inta.id)"
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
