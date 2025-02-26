<script setup>
import { ref } from "vue";
import { router } from "@inertiajs/vue3";
import GeneralSetting from "@/Layouts/GeneralSetting.vue";
import Table from "@/Components/Table.vue";
import SearchWithholding from "./SearchWithholding.vue";
import { TrashIcon, PencilIcon } from "@heroicons/vue/24/solid";

const props = defineProps({
  globalWithholdings: { type: Array, default: () => [] },
  withholdings: { type: Array, default: () => [] },
});

// Variables para el formulario
const code = ref("");
const name = ref("");
const percentage = ref("");
const type = ref("");

const showForm = ref(false); // Inicialmente oculto

const toggleForm = () => {
  showForm.value = !showForm.value; // Alternar visibilidad
};

// Método para guardar el ICE
const saveSelect = () => {
  const withholding = {
    code: code.value,
    name: name.value,
    percentage: percentage.value,
    type: type.value,
  };

  router.post(route("busssines.setting.withholding.store"), withholding, {
    preserveState: true,
    onSuccess: () => {
      location.reload(); // Recarga la pantalla después de guardar correctamente
    },
  });
};

// Método para recibir los datos seleccionados desde SearchWithholding.vue
const handleAddWithholdings = (withholding) => {
  console.log("Retención agregada desde modal:", withholding);

  code.value = withholding.code;
  name.value = withholding.name;
  percentage.value = withholding.percentage;
  type.value = withholding.type;

  saveSelect(); // Guarda automáticamente después de seleccionar
};
</script>

<template>
  <GeneralSetting title="Retenciones">
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
        <SearchWithholding
          :globalWithholdings="props.globalWithholdings"
          @addWithholdings="handleAddWithholdings"
        />
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
              <th>(%)</th>
              <th class="w-1"></th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="(withholding, i) in props.withholdings"
              :key="withholding.id"
              class="border-t [&>td]:py-2"
            >
              <td>{{ i + 1 }}</td>
              <td>{{ withholding.code }}</td>
              <td class="text-left">{{ withholding.name }}</td>
              <td>
                <span class="rounded px-2 py-1 m-0 text-white bg-success">
                  Activo
                </span>
              </td>
              <td>{{ withholding.percentage }}</td>

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

