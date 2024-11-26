<script setup>
import { ref } from "vue";
import Index from "@/Pages/FixedAsset/Index.vue"; // Vista de Activos Fijos
import Depreciation from "@/Pages/FixedAsset/Depreciation.vue"; // Vista de Depreciación
import AdminLayout from "@/Layouts/AdminLayout.vue";

// Props para recibir datos desde el servidor
const proms = defineProps({
  fixedAssetss: { type: Object, default: () => {} }, // Datos de activos fijos (paginados)
  depreciations: { type: Object, default: () => {} }, // Datos de depreciaciones (paginados)
});

// Controlador de pestañas activas
const activeTab = ref("fixedAssets"); // Pestaña por defecto

// Paginación para activos fijos
const fixedAssetsPage = ref(proms.fixedAssetss.current_page);
const totalFixedAssetsPages = ref(proms.fixedAssetss.last_page);

// Paginación para depreciaciones
const depreciationPage = ref(proms.depreciations.current_page);
const totalDepreciationPages = ref(proms.depreciations.last_page);

// Funciones para navegar entre las páginas
function nextPage(tab) {
  if (tab === "fixedAssets" && proms.fixedAssetss.next_page_url) {
    window.location.href = proms.fixedAssetss.next_page_url;
  } else if (tab === "depreciation" && proms.depreciations.next_page_url) {
    window.location.href = proms.depreciations.next_page_url;
  }
}

function prevPage(tab) {
  if (tab === "fixedAssets" && proms.fixedAssetss.prev_page_url) {
    window.location.href = proms.fixedAssetss.prev_page_url;
  } else if (tab === "depreciation" && proms.depreciations.prev_page_url) {
    window.location.href = proms.depreciations.prev_page_url;
  }
}
</script>

<template>
  <AdminLayout title="Gestión de Activos y Depreciación">
    <div class="p-4">
      <h1 class="text-xl font-bold mb-4">Gestión de Activos y Depreciación</h1>

      <div class="flex space-x-4 border-b mb-4">
        <button
          class="px-4 py-2 border-b-2 transition"
          :class="{
            'border-blue-500 text-blue-500 font-bold':
              activeTab === 'fixedAssets',
            'border-transparent text-gray-500': activeTab !== 'fixedAssets',
          }"
          @click="activeTab = 'fixedAssets'"
        >
          Activos Fijos
        </button>
        <button
          class="px-4 py-2 border-b-2 transition"
          :class="{
            'border-blue-500 text-blue-500 font-bold':
              activeTab === 'depreciation',
            'border-transparent text-gray-500': activeTab !== 'depreciation',
          }"
          @click="activeTab = 'depreciation'"
        >
          Depreciación
        </button>
      </div>

      <!-- Contenido dinámico basado en la pestaña activa -->
      <div class="mt-4">
        <Index
          v-if="activeTab === 'fixedAssets'"
          :fixedAssetss="proms.fixedAssetss.data"
        />
        <Depreciation
          v-if="activeTab === 'depreciation'"
          :depreciations="proms.depreciations.data"
        />
      </div>

      <!-- Paginación -->
      <div class="flex justify-between mt-4">
        <div v-if="activeTab === 'fixedAssets'" class="flex space-x-4">
          <button
            class="px-4 py-2 bg-gray-300 text-gray-700 rounded"
            :disabled="!proms.fixedAssetss.prev_page_url"
            @click="prevPage('fixedAssets')"
          >
            Anterior
          </button>
          <span
            >Página {{ fixedAssetsPage }} de {{ totalFixedAssetsPages }}</span
          >
          <button
            class="px-4 py-2 bg-gray-300 text-gray-700 rounded"
            :disabled="!proms.fixedAssetss.next_page_url"
            @click="nextPage('fixedAssets')"
          >
            Siguiente
          </button>
        </div>

        <div v-if="activeTab === 'depreciation'" class="flex space-x-4">
          <button
            class="px-4 py-2 bg-gray-300 text-gray-700 rounded"
            :disabled="!proms.depreciations.prev_page_url"
            @click="prevPage('depreciation')"
          >
            Anterior
          </button>
          <span
            >Página {{ depreciationPage }} de {{ totalDepreciationPages }}</span
          >
          <button
            class="px-4 py-2 bg-gray-300 text-gray-700 rounded"
            :disabled="!proms.depreciations.next_page_url"
            @click="nextPage('depreciation')"
          >
            Siguiente
          </button>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>
