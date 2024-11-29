<script setup>
import { ref, reactive, watch, onMounted } from "vue";
import { Inertia } from "@inertiajs/inertia";
import AdminLayout from "@/Layouts/AdminLayout.vue";
import Index from "@/Pages/FixedAsset/Index.vue";
import Depreciation from "@/Pages/FixedAsset/Depreciation.vue";

// Props para recibir datos desde el servidor
const props = defineProps({
  fixedAssetss: { type: Object, default: () => {} }, // Activos fijos paginados
  depreciations: { type: Object, default: () => {} }, // Depreciaciones paginadas
  filters: { type: String, default: () => "" },
});

const fixedAssetsss = reactive(props.fixedAssetss); // Si necesitas reactividad
const depreciationss = reactive(props.depreciations); // Si necesitas reactividad

// Controlador de pestañas activas
const activeTab = ref("fixedAssets");
const search = ref("");

// Función para manejar la paginación de las pestañas
function nextPage(tab) {
  const pageUrl =
    tab === "fixedAssets"
      ? fixedAssetsss.next_page_url
      : depreciationss.next_page_url;

  if (pageUrl) {
    Inertia.get(pageUrl, {}, { preserveState: true });
  }
}

function prevPage(tab) {
  const pageUrl =
    tab === "fixedAssets"
      ? fixedAssetsss.prev_page_url
      : depreciationss.prev_page_url;

  if (pageUrl) {
    Inertia.get(pageUrl, {}, { preserveState: true });
  }
}

// Función para obtener los datos con el filtro y la pestaña activa
function getData(searchQuery = "") {
  const currentTab = activeTab.value;
  const url =
    currentTab === "fixedAssets"
      ? "/activos-depreciacion"
      : "/activos-depreciacion";

  // Realiza la petición para obtener los datos con el filtro
  Inertia.get(
    url,
    { search: searchQuery },
    {
      preserveState: true,
      onSuccess: (page) => {
        // Actualiza los datos según la pestaña activa
        if (currentTab === "fixedAssets") {
          fixedAssetsss.data = page.fixedAssetss.data;
          fixedAssetsss.current_page = page.fixedAssetss.current_page;
          fixedAssetsss.last_page = page.fixedAssetss.last_page;
          fixedAssetsss.prev_page_url = page.fixedAssetss.prev_page_url;
          fixedAssetsss.next_page_url = page.fixedAssetss.next_page_url;
        } else {
          depreciationss.data = page.depreciations.data;
          depreciationss.current_page = page.depreciations.current_page;
          depreciationss.last_page = page.depreciations.last_page;
          depreciationss.prev_page_url = page.depreciations.prev_page_url;
          depreciationss.next_page_url = page.depreciations.next_page_url;
        }
      },
      onError: (error) => {
        console.log(error);
      },
    }
  );
}

// Se ejecuta al montar el componente para cargar los datos iniciales
onMounted(() => {
  getData(search.value);
});

// Watcher para aplicar el filtro cuando cambie la búsqueda
watch(search, (newQuery) => {
  getData(newQuery); // Vuelve a hacer la petición con el filtro actualizado
});

// Watcher para cambiar los datos cuando cambia la pestaña activa
watch(activeTab, () => {
  getData(search.value); // Vuelve a cargar los datos al cambiar la pestaña
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
        <!-- Muestra los datos de Activos Fijos -->
        <Index
          v-if="activeTab === 'fixedAssets'"
          :fixedAssetss="fixedAssetsss.data"
        />

        <!-- Muestra los datos de Depreciación -->
        <Depreciation
          v-if="activeTab === 'depreciation'"
          :depreciations="depreciationss.data"
        />
      </div>

      <div class="flex justify-between mt-4">
        <div v-if="activeTab === 'fixedAssets'" class="flex space-x-4">
          <button
            class="px-4 py-2 bg-gray-300 text-gray-700 rounded"
            :disabled="!fixedAssetsss.prev_page_url"
            @click="prevPage('fixedAssets')"
          >
            Anterior
          </button>
          <span
            >Página {{ fixedAssetsss.current_page }} de
            {{ fixedAssetsss.last_page }}</span
          >
          <button
            class="px-4 py-2 bg-gray-300 text-gray-700 rounded"
            :disabled="!fixedAssetsss.next_page_url"
            @click="nextPage('fixedAssets')"
          >
            Siguiente
          </button>
        </div>

        <div v-if="activeTab === 'depreciation'" class="flex space-x-4">
          <button
            class="px-4 py-2 bg-gray-300 text-gray-700 rounded"
            :disabled="!depreciationss.prev_page_url"
            @click="prevPage('depreciation')"
          >
            Anterior
          </button>

          <button
            class="px-4 py-2 bg-gray-300 text-gray-700 rounded"
            :disabled="!depreciationss.next_page_url"
            @click="nextPage('depreciation')"
          >
            Siguiente
          </button>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>
