<script setup>
// Imports
import DialogModal from "@/Components/DialogModal.vue";
import Table from "@/Components/Table.vue";
import { ref, computed, watch } from "vue";
import TextInput from "@/Components/TextInput.vue";

// Props
const props = defineProps({
  filteredAccountsFromMain: { type: Array, default: () => [] },
  show: { type: Boolean, default: () => true },
});

// Variables reactivas
const search = ref("");

// Computed para filtrar cuentas combinando filtro principal y búsqueda local
const filteredAccounts = computed(() => {
  const mainFiltered = props.filteredAccountsFromMain; // Filtro desde el principal
  if (!search.value) {
    return mainFiltered;
  }
  return mainFiltered.filter(
    (acc) =>
      acc.code.toLowerCase().includes(search.value.toLowerCase()) || // Filtrar por código
      acc.name.toLowerCase().includes(search.value.toLowerCase()) // Filtrar por nombre
  );
});

// Watch para reiniciar el buscador al abrir el modal
watch(
  () => props.show,
  (newValue) => {
    if (newValue) {
      search.value = ""; // Reinicia el buscador cuando el modal se abre
    }
  }
);

// Emits
defineEmits(["selectAccount", "close"]);
</script>

<template>
  <DialogModal :show="show" maxWidth="lg" @close="$emit('close')">
    <template #title>
      <h2 class="text-sm sm:text-lg font-bold w-full pb-2 sm:pb-0">
        Seleccionar cuenta
      </h2>
      <div class="w-full flex sm:justify-end">
        <TextInput
          v-model="search"
          type="search"
          class="block sm:mr-2 h-8 w-full"
          placeholder="Buscar ..."
        />
      </div>
    </template>
    <template #content>
      <Table>
        <thead>
          <tr class="border-b">
            <th class="text-left w-24">CUENTA</th>
            <th class="text-left">DESCRIPCION</th>
          </tr>
        </thead>
        <tbody>
          <tr
            @click="$emit('selectAccount', account.id)"
            v-for="(account, i) in filteredAccounts"
            :key="`account-${i}`"
            class="h-8 cursor-pointer"
          >
            <td class="text-left">{{ account.code }}</td>
            <td class="text-left">{{ account.name }}</td>
          </tr>
        </tbody>
      </Table>
    </template>
    <template #footer>
      <button
        @click="$emit('selectAccount')"
        class="px-6 py-2 ml-2 bg-blue-600 dark:bg-blue-600 text-blue-100 dark:text-blue-200 rounded"
      >
        Guardar
      </button>
    </template>
  </DialogModal>
</template>
