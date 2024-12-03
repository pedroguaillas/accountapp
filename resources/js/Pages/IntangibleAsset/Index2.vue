<script setup>
import { ref } from "vue";
import AdminLayout from "@/Layouts/AdminLayout.vue";
import Index from "@/Pages/IntangibleAsset/Index.vue";
import Amortizacion from "@/Pages/IntangibleAsset/Amortizacion.vue";

const props = defineProps({
  intangibleAssetss: { type: Object, default: () => {} },
  amortizations: { type: Object, default: () => {} },
});

// Controlador de pestañas activas
const activeTab = ref("intangibleAssets");
</script>

<template>
  <AdminLayout title="Gestión de Activos Intangibles y Amortizaciones">
    <div class="p-4">
      <h1 class="text-xl font-bold mb-4">
        Gestión de Activos Intangibles y Amortizaciones
      </h1>

      <!-- Pestañas para cambiar entre Activos Intangibles y Amortizaciones -->
      <div class="flex space-x-4 border-b mb-4">
        <button
          class="px-4 py-2 border-b-2 transition"
          :class="{
            'border-blue-500 text-blue-500 font-bold':
              activeTab === 'intangibleAssets',
            'border-transparent text-gray-500':
              activeTab !== 'intangibleAssets',
          }"
          @click="activeTab = 'intangibleAssets'"
        >
          Activos Intangibles
        </button>
        <button
          class="px-4 py-2 border-b-2 transition"
          :class="{
            'border-blue-500 text-blue-500 font-bold':
              activeTab === 'amortizations',
            'border-transparent text-gray-500': activeTab !== 'amortizations',
          }"
          @click="activeTab = 'amortizations'"
        >
          Amortizaciones
        </button>
      </div>

      <!-- Contenido dinámico basado en la pestaña activa -->
      <div class="mt-4">
        <Index
          v-if="activeTab === 'intangibleAssets'"
          :intangibleAssetss="props.intangibleAssetss.data"
        />
        <Amortizacion v-if="activeTab === 'amortizations'" />
      </div>
    </div>
  </AdminLayout>
</template>
