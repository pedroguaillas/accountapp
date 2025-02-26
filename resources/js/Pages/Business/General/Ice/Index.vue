<script setup>
import { ref } from "vue";
import { router } from "@inertiajs/vue3";
import GeneralSetting from "@/Layouts/GeneralSetting.vue";
import Table from "@/Components/Table.vue";
import InputLabel from "@/Components/InputLabel.vue";
import { TrashIcon, PencilIcon } from "@heroicons/vue/24/solid";

const props = defineProps({
  globalIces: { type: Array, default: () => [] },
  ices: { type: Array, default: () => [] },
});

// Variables para el formulario
const code = ref("");
const name = ref("");
const percentage = ref("");

const showForm = ref(false); // Inicialmente oculto

const toggleForm = () => {
  showForm.value = !showForm.value; // Alternar visibilidad
};

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
</script>

<template>
  <GeneralSetting title="Ices">
    <div class="p-4 bg-white rounded drop-shadow-md">
      <div class="w-full flex sm:justify-end">
        <button
          @click="toggleForm"
          class="mt-2 sm:mt-0 px-2 bg-success dark:bg-green-600 hover:bg-successhover text-2xl text-white rounded font-bold"
        >
          +
        </button>
      </div>

      <!-- Formulario solo visible si modal es true -->
      <div v-if="showForm" id="nuevo" class="mt-4">
        <InputLabel for="code" value="Código" />
        <input
          type="text"
          v-model="code"
          class="block w-full border border-gray-300 px-4 py-2 focus:outline-none"
        />

        <InputLabel for="name" value="Nombre" />
        <input
          type="text"
          v-model="name"
          class="block w-full border border-gray-300 px-4 py-2 focus:outline-none"
        />

        <InputLabel for="percentage" value="Porcentaje" />
        <input
          type="number"
          v-model="percentage"
          class="block w-full border border-gray-300 px-4 py-2 focus:outline-none"
        />

        <button
          @click="saveSelect"
          class="mt-4 px-4 py-2 bg-blue-500 hover:bg-blue-700 text-white font-bold rounded"
        >
          Guardar
        </button>
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
              <th>PORCENTAJE</th>
              <th class="w-1"></th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="(ice, i) in props.ices"
              :key="ice.id"
              class="border-t [&>td]:py-2"
            >
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
                  <button
                    class="rounded px-1 py-1 bg-danger hover:bg-dangerhover text-white"
                  >
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

