<script setup>
import { ref, computed } from "vue";
import axios from "axios";
import GeneralSetting from "@/Layouts/GeneralSetting.vue";

// Recibes 'withholdings' desde Inertia
const props = defineProps({
  withholdings: {
    type: Array,
    default: () => [],
  },
});

// Inicializar selectedPayments con los métodos que tienen selected: true
const selectedWithholdings = ref(
  props.withholdings
    .filter((method) => method.selected)
    .map((method) => method.code)
);

// Computed para verificar si todos están seleccionados
const allSelected = computed(
  () => selectedWithholdings.value.length === props.withholdings.length
);

// Función para seleccionar/deseleccionar todos
const toggleAll = () => {
  if (allSelected.value) {
    selectedWithholdings.value = [];
  } else {
    selectedWithholdings.value = props.withholdings.map((pm) => pm.code);
  }
};

// Enviar la selección al backend
const saveSelection = () => {
  axios
    .post(route("busssines.setting.withholding.store"), {
      selected: selectedWithholdings.value,
    })
    .then((response) => {
      console.log(response.data.message);
      alert("Selección guardada correctamente");
    })
    .catch((error) => {
      console.error("Error al guardar la selección", error);
      alert("Ocurrió un error al guardar");
    });
};
</script>

<template>
  <GeneralSetting title="Retenciones">
    <div class="p-4 bg-white rounded drop-shadow-md">
      <!-- Header -->

      <div>
        <!-- Checkbox para seleccionar todos -->
        <div>
          <input type="checkbox" :checked="allSelected" @change="toggleAll" />
          <label class="ml-2">Seleccionar todos</label>
        </div>

        <!-- Lista de checkboxes -->
        <div
          v-for="method in props.withholdings"
          :key="method.code"
          class="mt-2"
        >
          <input
            type="checkbox"
            :value="method.code"
            v-model="selectedWithholdings"
          />
          <label class="ml-2">{{ method.name }}</label>
        </div>

        <!-- Botón para guardar -->
        <button
          @click="saveSelection"
          class="mt-4 px-4 py-2 bg-blue-500 text-white rounded"
        >
          Guardar selección
        </button>
      </div>
    </div>
  </GeneralSetting>
</template>
