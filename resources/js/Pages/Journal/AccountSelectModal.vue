<script setup lang="ts">
// Imports
import { ref, computed } from "vue";
import { SecondaryButton, TextInput, Table, DialogModal } from "@/Components";
import { Account } from "@/types";

// Props
const props = defineProps<{
  accounts: Account[]; // Paginación de los bancos
  show: boolean;
}>();

// Variables reactivas
const search = ref("");
const close = () => emit("close");

// Computed para filtrar cuentas combinando filtro principal y búsqueda local
const filteredAccounts = computed(() => {
  const mainFiltered = props.accounts; // Filtro desde el principal
  if (!search.value) {
    return mainFiltered;
  }
  return mainFiltered.filter(
    (acc) =>
      acc.code.toLowerCase().includes(search.value.toLowerCase()) || // Filtrar por código
      acc.name.toLowerCase().includes(search.value.toLowerCase()) // Filtrar por nombre
  );
});

// Método para seleccionar cuenta
const handleSelectAccount = (account: Account) => {
  emit("selectAccount", { ...account });
  emit("close");
};

// Emits
const emit = defineEmits(["close", "selectAccount"]);
</script>

<template>
  <DialogModal :show="show" maxWidth="lg" @close="close">
    <template #title>
      <h2 class="text-sm sm:text-lg font-bold w-full pb-2 sm:pb-0">
        Seleccionar cuenta
      </h2>
      <div class="w-full flex sm:justify-end">
        <TextInput v-model="search" type="search" class="block sm:mr-2 h-8 w-full" placeholder="Buscar ..." />
      </div>
    </template>
    <template #content>
      <div class="max-h-[300px] overflow-y-auto">
        <Table class="text-xs sm:text-sm table-auto w-full text-center text-gray-700">
          <thead>
            <tr class="[&>th]:py-2">
              <th class="w-1">N°</th>
              <th>CUENTA</th>
              <th class="text-left">DESCRIPCION</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(account, i) in filteredAccounts" :key="account.id" class="border-t [&>td]:py-2 cursor-pointer"
              @click="handleSelectAccount(account)">
              <td>{{ i + 1 }}</td>
              <td>{{ account.code }}</td>
              <td class="text-left">{{ account.name }}</td>
            </tr>
          </tbody>
        </Table>
      </div>
    </template>

    <template #footer>
      <SecondaryButton class="mr-2" type="button" @click="$emit('close')">Cancelar</SecondaryButton>
    </template>
  </DialogModal>
</template>
