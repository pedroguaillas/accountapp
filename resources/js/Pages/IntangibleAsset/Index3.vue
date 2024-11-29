<script setup>
import { ref, computed, watch } from "vue";
import { Inertia } from "@inertiajs/inertia";
import Index from "@/Pages/FixedAsset/Index.vue";
import Depreciation from "@/Pages/FixedAsset/Depreciation.vue";
import AdminLayout from "@/Layouts/AdminLayout.vue";

// Props para recibir datos desde el servidor
const props = defineProps({
  fixedAssetss: { type: Object, default: () => {} }, // Activos fijos paginados
  depreciations: { type: Object, default: () => {} }, // Depreciaciones paginadas
  filters: { type: String, default: () => "" },
});

// Controlador de pestañas activas
const activeTab = ref("fixedAssets");

// Variables de paginación
const fixedAssetsPage = ref(props.fixedAssetss.current_page);
const totalFixedAssetsPages = ref(props.fixedAssetss.last_page);

const depreciationPage = ref(props.depreciations.current_page);
const totalDepreciationPages = ref(props.depreciations.last_page);

// Propiedad reactiva para el filtro
const search = ref("");

// Computed para filtrar activos fijos por el detalle
const filteredFixedAssets = computed(() => {
  //if (!search.value) return props.fixedAssetss.data;
  return props.fixedAssetss.data;

  // return props.fixedAssetss.data.filter((fixedAssets) =>
  // fixedAssets.detail.toLowerCase().includes(search.value.toLowerCase())
  // );
});

// Computed para filtrar depreciaciones por el detalle
const filteredDepreciations = computed(() => {
  if (!search.value) return props.depreciations.data;
  // return props.depreciations.data.filter((depreciation) =>
  //   depreciation.description.toLowerCase().includes(search.value.toLowerCase())
  // );
});

// Función para navegar entre páginas
function nextPage(tab) {
  const pageUrl =
    tab === "fixedAssets"
      ? props.fixedAssetss.next_page_url
      : props.depreciations.next_page_url;
  if (pageUrl) {
    Inertia.get(
      pageUrl,
      {},
      { preserveState: true, onSuccess: updatePagination }
    );
  }
}

function prevPage(tab) {
  const pageUrl =
    tab === "fixedAssets"
      ? props.fixedAssetss.prev_page_url
      : props.depreciations.prev_page_url;
  if (pageUrl) {
    Inertia.get(
      pageUrl,
      {},
      { preserveState: true, onSuccess: updatePagination }
    );
  }
}

// Actualizar las variables reactivas de paginación al cambiar de página
function updatePagination() {
  fixedAssetsPage.value = props.fixedAssetss.current_page;
  totalFixedAssetsPages.value = props.fixedAssetss.last_page;

  depreciationPage.value = props.depreciations.current_page;
  totalDepreciationPages.value = props.depreciations.last_page;

  fixedAssetss.value = props.fixedAssetss.data;
}

// Watcher para buscar
watch(search, (newQuery) => {
  const currentTab = activeTab.value;
  const url =
    currentTab === "fixedAssets"
      ? "/activos-depreciacion"
      : "/activos-depreciacion";
  Inertia.get(
    url,
    { search: newQuery },
    { preserveState: true, onSuccess: updatePagination }
  );
});
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

      <div class="mb-4">
        <input
          type="text"
          v-model="search"
          placeholder="Buscar por detalle..."
          class="px-4 py-2 border rounded w-full"
        />
      </div>

      <div class="mt-4">
        <Index
          v-if="activeTab === 'fixedAssets'"
          :fixedAssetss="filteredFixedAssets"
        />
        <Depreciation
          v-if="activeTab === 'depreciation'"
          :depreciations="filteredDepreciations"
        />
      </div>

      <div class="flex justify-between mt-4">
        <div v-if="activeTab === 'fixedAssets'" class="flex space-x-4">
          <button
            class="px-4 py-2 bg-gray-300 text-gray-700 rounded"
            :disabled="!props.fixedAssetss.prev_page_url"
            @click="prevPage('fixedAssets')"
          >
            Anterior
          </button>
          <span
            >Página {{ fixedAssetsPage }} de {{ totalFixedAssetsPages }}</span
          >
          <button
            class="px-4 py-2 bg-gray-300 text-gray-700 rounded"
            :disabled="!props.fixedAssetss.next_page_url"
            @click="nextPage('fixedAssets')"
          >
            Siguiente
          </button>
        </div>

        <div v-if="activeTab === 'depreciation'" class="flex space-x-4">
          <button
            class="px-4 py-2 bg-gray-300 text-gray-700 rounded"
            :disabled="!props.depreciations.prev_page_url"
            @click="prevPage('depreciation')"
          >
            Anterior
          </button>
          <span
            >Página {{ depreciationPage }} de {{ totalDepreciationPages }}</span
          >
          <button
            class="px-4 py-2 bg-gray-300 text-gray-700 rounded"
            :disabled="!props.depreciations.next_page_url"
            @click="nextPage('depreciation')"
          >
            Siguiente
          </button>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>
