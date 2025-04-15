<script setup lang="ts">
// Imports
import { DialogModal } from "@/Components";
import { BankAccount } from "@/types";

// Props
defineProps<{
  bankaccounts: BankAccount[]; // Paginación de los bancos
  show: boolean;
}>();

// Emits
const emit = defineEmits(["close", "selectBankAccount"]);

// Método para seleccionar centro de costo
const handleSelectBankAccount = (bankaccount: BankAccount) => {
  emit("selectBankAccount", bankaccount); // Emitir la cuenta seleccionada
  emit("close"); // Cerrar el modal
};
</script>

<template>
  <DialogModal :show="show" maxWidth="lg" @close="emit('close')">
    <template #title>Seleccionar la cuenta de banco</template>
    <template #content>
      <div class="mt-0 max-h-[300px] overflow-y-auto">
        <table class="mt-4 text-xs sm:text-sm table-auto w-full text-center text-gray-700">
          <thead>
            <tr class="[&>th]:py-2">
              <th class="w-1">N°</th>
              <th class="text-left">Banco</th>
              <th class="text-left">Numero de cuenta</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(bankaccount, i) in bankaccounts" :key="bankaccount.id"
              class="border-t [&>td]:py-2 cursor-pointer" @click="handleSelectBankAccount(bankaccount)">
              <td>{{ bankaccount.id }}</td>
              <td>{{ bankaccount.name }}</td>
              <td class="text-left">{{ bankaccount.account_number }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </template>
  </DialogModal>
</template>
