<script setup>
import { ref, watch } from "vue";
import Index from "@/Pages/FixedAsset/Index.vue"; // Vista de Activos Fijos
import Depreciation from "@/Pages/FixedAsset/Depreciation.vue"; // Vista de Depreciación
import AdminLayout from "@/Layouts/AdminLayout.vue";
import TextInput from "@/Components/TextInput.vue";
import { router } from '@inertiajs/vue3';
import Paginate from "@/Components/Paginate.vue";
// Props para recibir datos desde el servidor
const proms = defineProps({
  fixedAssetss: { type: Object, default: () => ({}) },
  depreciations: { type: Object, default: () => ({}) },
  filters: { type: Object, default: () => ({}) },
});

// Controlador de pestañas activas
const activeTab = ref("fixedAssets");

// Estado de búsqueda y carga
const search = ref(proms.filters.search || ""); // Valor inicial desde filtros
const loading = ref(false);

// Función de navegación
async function fetchData(params = {}) {
  const url = route("assetsdepreciation.index");
  loading.value = true;
  try {
    await router.get(url, params, { preserveState: true });
  } catch (error) {
    console.error("Error al realizar la búsqueda:", error);
  } finally {
    loading.value = false;
  }
}

// Observa cambios en `search` para realizar búsquedas dinámicas
watch(
  search,
  async (newQuery) => {
    if (newQuery.length === 0) {
      // Cargar todos los datos si no hay término de búsqueda
      await fetchData();
    } else if (newQuery.length >= 1) {
      // Realizar búsqueda con el término
      await fetchData({ search: newQuery });
    }
  },
  { immediate: true }
);

// Paginación con `router.get`
function nextPage(tab) {
  let nextPageUrl = null;
  
  if (tab === "fixedAssets") {
    nextPageUrl = proms.fixedAssetss.next_page_url;
  } else if (tab === "depreciation") {
    nextPageUrl = proms.depreciations.next_page_url;
  }

  if (nextPageUrl) {
    router.get(nextPageUrl, {}, { preserveState: true });
  }
}

function prevPage(tab) {
  let prevPageUrl = null;

  if (tab === "fixedAssets") {
    prevPageUrl = proms.fixedAssetss.prev_page_url;
  } else if (tab === "depreciation") {
    prevPageUrl = proms.depreciations.prev_page_url;
  }

  if (prevPageUrl) {
    router.get(prevPageUrl, {}, { preserveState: true });
  }
}
</script>

<template>
  <AdminLayout title="Gestión de Activos y Depreciación">
    <div class="p-4">
      <h1 class="text-xl font-bold mb-4">Gestión de Activos y Depreciación</h1>

      <!-- Campo de búsqueda -->
      <div class="w-full flex justify-start">
        <TextInput
          v-model="search"
          type="text"
          class="mt-1 block w-[50%] mr-2 h-8"
          minlength="3"
          maxlength="300"
          required
          placeholder="BUSCAR"
        />
      </div>

      <!-- Navegación de pestañas -->
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

      <!-- Contenido dinámico -->
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

      <!-- Indicador de carga -->
      <div v-if="loading" class="mt-4 text-blue-500">Cargando...</div>

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
          <span>Página {{ proms.fixedAssetss.current_page }} de {{ proms.fixedAssetss.last_page }}</span>
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
          <span>Página {{ proms.depreciations.current_page }} de {{ proms.depreciations.last_page }}</span>
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
