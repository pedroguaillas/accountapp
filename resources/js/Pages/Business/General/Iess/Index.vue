<script setup>
import { ref, computed } from "vue";
import axios from "axios";
import GeneralSetting from "@/Layouts/GeneralSetting.vue";

// Recibes 'withholdings' desde Inertia
const props = defineProps({
  iess: {
    type: Array,
    default: () => [],
  },
});

// Inicializar selectedPayments con los métodos que tienen selected: true
const selectedIess = ref(
  props.iess
    .filter((method) => method.selected)
    .map((method) => method.id)
);

// Computed para verificar si todos están seleccionados
const allSelected = computed(
  () => selectedIess.value.length === props.iess.length
);

// Función para seleccionar/deseleccionar todos
const toggleAll = () => {
  if (allSelected.value) {
    selectedIess.value = [];
  } else {
    selectedIess.value = props.iess.map((pm) => pm.id);
  }
};

// Enviar la selección al backend
const saveSelection = () => {
  axios
    .post(route("busssines.setting.iess.store"), {
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
  <GeneralSetting title="IESS">
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
          v-for="method in props.iess"
          :key="method.id"
          class="mt-2"
        >
          <input
            type="checkbox"
            :value="method.id"
            v-model="selectedWithholdings"
          />
          <label class="ml-2">{{ method.type }}</label>
          <label class="ml-2">{{ method.name }}</label>
          <label class="ml-2">{{ method.percentaje}}</label>
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
