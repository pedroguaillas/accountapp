<script setup>
import { ref, watch } from "vue";
import AdminLayout from "@/Layouts/AdminLayout.vue";
import Index from "@/Pages/IntangibleAsset/Index.vue";
import Amortizacion from "@/Pages/IntangibleAsset/Amortizacion.vue";
import TextInput from "@/Components/TextInput.vue";
import { router } from "@inertiajs/vue3";

// Props del servidor
const props = defineProps({
  intangibleAssetss: { type: Object, default: () => ({}) },
  amortizations: { type: Object, default: () => ({}) },
  filters: { type: Object, default: () => ({}) },
});

// Controlador de pestañas activas
const activeTab = ref("intangibleAssets");

// Estado de búsqueda
const search = ref(props.filters.search || ""); // Inicializa con el valor del filtro
const loading = ref(false);

// Función para cargar datos desde el servidor
async function fetchData(params = {}) {
  loading.value = true;
  try {
    await router.get(route("intangibleamortization.index"), params, { preserveState: true });
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
      // Si la búsqueda está vacía, carga todos los datos
      await fetchData();
    } else if (newQuery.length >= 1) {
      // Realiza la búsqueda con el término ingresado
      await fetchData({ search: newQuery });
    }
  },
  { immediate: true }
);

// Función para manejar la paginación
async function changePage(tab, url) {
  if (!url) return;
  const params = { search: search.value }; // Mantener el término de búsqueda
  await router.get(url, params, { preserveState: true });
}
</script>

<template>
  <AdminLayout title="Gestión de Activos Intangibles y Amortizaciones">
    <div class="p-4">
      <h1 class="text-xl font-bold mb-4">
        Gestión de Activos Intangibles y Amortizaciones
      </h1>

      <!-- Campo de búsqueda -->
      <div class="w-full flex justify-start mb-4">
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

      <!-- Pestañas -->
      <div class="flex space-x-4 border-b mb-4">
        <button
          class="px-4 py-2 border-b-2 transition"
          :class="{
            'border-blue-500 text-blue-500 font-bold': activeTab === 'intangibleAssets',
            'border-transparent text-gray-500': activeTab !== 'intangibleAssets',
          }"
          @click="activeTab = 'intangibleAssets'"
        >
          Activos Intangibles
        </button>
        <button
          class="px-4 py-2 border-b-2 transition"
          :class="{
            'border-blue-500 text-blue-500 font-bold': activeTab === 'amortizations',
            'border-transparent text-gray-500': activeTab !== 'amortizations',
          }"
          @click="activeTab = 'amortizations'"
        >
          Amortizaciones
        </button>
      </div>

      <!-- Contenido dinámico -->
      <div>
        <!-- Activos Intangibles -->
        <Index
          v-if="activeTab === 'intangibleAssets'"
          :intangibleAssetss="props.intangibleAssetss.data"
        />

        <!-- Amortizaciones -->
        <Amortizacion
          v-if="activeTab === 'amortizations'"
          :amortizations="props.amortizations.data"
        />
      </div>

      <!-- Paginación -->
      <div v-if="activeTab === 'intangibleAssets'" class="flex justify-between mt-4">
        <button
          :disabled="!props.intangibleAssetss.prev_page_url"
          @click="changePage(activeTab, props.intangibleAssetss.prev_page_url)"
        >
          Página Anterior
        </button>
        <button
          :disabled="!props.intangibleAssetss.next_page_url"
          @click="changePage(activeTab, props.intangibleAssetss.next_page_url)"
        >
          Página Siguiente
        </button>
      </div>
      <div v-if="activeTab === 'amortizations'" class="flex justify-between mt-4">
        <button
          :disabled="!props.amortizations.prev_page_url"
          @click="changePage(activeTab, props.amortizations.prev_page_url)"
        >
          Página Anterior
        </button>
        <button
          :disabled="!props.amortizations.next_page_url"
          @click="changePage(activeTab, props.amortizations.next_page_url)"
        >
          Página Siguiente
        </button>
      </div>
    </div>
  </AdminLayout>
</template>
