<script setup lang="ts">
import { ref } from "vue";
import { router } from "@inertiajs/vue3";
import GeneralSetting from "@/Layouts/GeneralSetting.vue";
import IceSearch from "./IceSearch.vue";
import { TrashIcon, PencilIcon } from "@heroicons/vue/24/solid";
import { Table } from "@/Components";
import { Ice } from "@/types";

const props = defineProps<{
  globalIces: Ice[];
  ices: Ice[];
}>();

// Variables para el formulario
const code = ref("");
const name = ref("");
const percentage = ref(0);

const showForm = ref(false); // Inicialmente oculto

const toggleForm = () => {
  showForm.value = !showForm.value; // Alternar visibilidad
};

// Método para guardar el ICE
// Método para guardar el ICE
const saveSelect = () => {
  const ice = {
    code: code.value,
    name: name.value,
    percentage: percentage.value,
  };

  router.post(route("busssines.setting.ices.store"), ice, {
    preserveState: true,
    onSuccess: () => {
      location.reload(); // Recarga la pantalla después de guardar correctamente
    },
  });
};

// Método para recibir los datos seleccionados desde SearchWithholding.vue
const handleAddIces = (ice : Ice) => {
  code.value = ice.code;
  name.value = ice.name;
  percentage.value = ice.percentage;

  saveSelect(); // Guarda automáticamente después de seleccionar
};
</script>

<template>
  <GeneralSetting title="Ices">
    <div class="p-4 bg-white rounded drop-shadow-md">
      <div class="w-full flex sm:justify-end">
        <button @click="toggleForm"
          class="mt-2 sm:mt-0 px-2 bg-success dark:bg-green-600 hover:bg-successhover text-2xl text-white rounded font-bold">
          +
        </button>
      </div>

      <!-- Formulario solo visible si modal es true -->
      <div v-if="showForm" id="nuevo" class="mt-4">
        <div v-if="showForm" id="nuevo" class="mt-4">
          <IceSearch :globalIces="props.globalIces" @addIces="handleAddIces" />
        </div>
      </div>

      <div class="w-full overflow-x-auto">
        <!-- Tabla -->
        <Table>
          <thead>
            <tr class="[&>th]:py-2">
              <th class="w-1">N°</th>
              <th>CODIGO</th>
              <th class="text-left">NOMBRE</th>
              <th>ESTADO</th>
              <th>%</th>
              <th class="w-1"></th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(ice, i) in props.ices" :key="ice.id" class="border-t [&>td]:py-2">
              <td>{{ i + 1 }}</td>
              <td>{{ ice.code }}</td>
              <td class="text-left">{{ ice.name }}</td>
              <td>
                <span class="rounded px-2 py-1 m-0 text-white bg-success">
                  Activo
                </span>
              </td>
              <td>{{ ice.percentage }}</td>

              <td class="flex justify-end">
                <div class="relative inline-flex gap-1">
                  <button class="rounded px-1 py-1 bg-danger hover:bg-dangerhover text-white">
                    <TrashIcon class="size-6 text-white" />
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </Table>
      </div>
    </div>
  </GeneralSetting>
</template>
