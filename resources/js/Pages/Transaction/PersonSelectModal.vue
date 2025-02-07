<script setup>
// Imports
import DialogModal from "@/Components/DialogModal.vue";

// Props
defineProps({
  people: { type: Array, default: () => [] },
  show: { type: Boolean, default: false },
});

// Emits
const emit = defineEmits(["close", "selectPerson"]);

// Método para seleccionar centro de costo
const handlePersonSelect = (person) => {
  emit("selectPerson", person); // Emitir la cuenta seleccionada
  emit("close"); // Cerrar el modal
};
</script>

<template>
  <DialogModal :show="show" maxWidth="lg" @close="emit('close')">
    <template #title>Seleccionar una persona</template>
    <template #content>
      <div class="mt-0 max-h-[300px] overflow-y-auto">
        <table
          class="mt-4 text-xs sm:text-sm table-auto w-full text-center text-gray-700"
        >
          <thead>
            <tr class="[&>th]:py-2">
              <th class="w-1">N°</th>
              <th class="text-left">IDENTIFICACION</th>
              <th class="text-left">NOMBRE</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="person in people"
              :key="person.id"
              class="border-t [&>td]:py-2 cursor-pointer"
              @click="handlePersonSelect(person)"
            >
              <td>{{ person.id }}</td>
              <td>{{ person.identification }}</td>
              <td>{{ person.name }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </template>
  </DialogModal>
</template>
