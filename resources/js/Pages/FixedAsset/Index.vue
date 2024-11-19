<script setup>
// Imports

import AdminLayout from "@/Layouts/AdminLayout.vue";
import {  Link } from "@inertiajs/vue3";

// Props
defineProps({
  fixedAssets: { type: Array, default: () => [] },

});
</script>

<template>
  <AdminLayout title="Activos Fijos">
    <!-- Card -->
    <div class="p-4 bg-white rounded drop-shadow-md">
      <!-- Card Header -->
      <div class="flex justify-between items-center">
        <h2 class="text-sm sm:text-lg font-bold">Activos fijos</h2>
        <div class="flex justify-end mb-3">
      <Link
        :href="route('fixedassets.create')"
        class="px-2 bg-green-500 dark:bg-green-600 text-2xl text-white rounded font-bold"
      >
        +
      </Link>
    </div>
      </div>

      <!-- Responsive -->
      <div class="w-full overflow-x-auto">
        <!-- Tabla -->
        <table
          class="mt-4 text-xs sm:text-sm table-auto w-full text-center text-gray-700"
        >
          <thead>
            <tr class="[&>th]:py-2">
              <th class="w-1">N°</th>

              <th>Codigo</th>
              <th>Fecha</th>
              <th>Detalle</th>
              <th>Valor</th>
              <th class="w-1"></th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="(fixedAsset, i) in fixedAssets"
              :key="fixedAsset.id"
              class="border-t [&>td]:py-2"
            >
              <td>{{ i + 1 }}</td>
              <td>{{ fixedAsset.code }}</td>
              <td>{{ fixedAsset.date }}</td>
              <td>{{ fixedAsset.detail }}</td>
              <td>{{ fixedAsset.value }}</td>

              <td>
                <div class="relative inline-flex">
                  <button class="rounded px-2 py-1 bg-blue-500 text-white">
                    <i class="fa fa-edit"></i> Modificar
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </AdminLayout>
</template>
