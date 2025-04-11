<script setup lang="ts">
// Imports
import { TextInput, SecondaryButton, PrimaryButton, DynamicSelect, InputLabel, InputError, DialogModal } from "@/Components";
import { useFocusNextField } from "@/composables/useFocusNextField";
import { BankAccount, Errors } from "@/types";

// Props
defineProps<{
  bankAccount: BankAccount;
  error: Errors;
  show: boolean;
}>();

// Composable para cambiar de campo al presionar "Enter"
const { focusNextField } = useFocusNextField();

// Opciones para el tipo de cuenta bancaria
const accountTypeOptions = [
  { value: "ahorro", label: "Ahorros" },
  { value: "corriente", label: "Corriente" },
];

// Emits
defineEmits(["close", "save"]);
</script>

<template>
  <DialogModal :show="show" maxWidth="lg" @close="$emit('close')">
    <template #title>
      {{ bankAccount.id === undefined ? "Añadir" : "Editar" }} cuenta bancaria
    </template>

    <template #content>
      <div class="mt-4">
        <form
          class="w-2xl grid grid-cols-1 gap-3"
          @keydown.enter.prevent="focusNextField"
        >
          <!-- Número de cuenta bancaria -->
          <div class="col-span-6 sm:col-span-4">
            <InputLabel
              for="account_number"
              value="Número de cuenta bancaria"
            />
            <TextInput
              v-model="bankAccount.account_number"
              type="text"
              class="mt-1 block w-full"
              id="account_number"
            />
            <InputError :message="error.account_number" class="mt-2" />
          </div>

          <!-- Seleccionar Tipo de Cuenta -->
          <div class="col-span-6 sm:col-span-4">
            <InputLabel for="account_type" value="Tipo de Cuenta" />
            <DynamicSelect
              v-model="bankAccount.account_type"
              :options="accountTypeOptions"
              class="mt-1 block w-full"
              id="account_type"
            />
            <InputError :message="error.account_type" class="mt-2" />
          </div>

          <div class="col-span-6 sm:col-span-4">
            <InputLabel for="current_balance" value="Saldo" />
            <TextInput
              v-model="bankAccount.current_balance"
              type="number"
              min="0"
              class="mt-1 block w-full"
              id="current_balance"
            />
            <InputError :message="error.current_balance" class="mt-2" />
          </div>
        </form>
      </div>
    </template>

    <template #footer>
      <SecondaryButton @click="$emit('close')" class="mr-2"
        >Cancelar</SecondaryButton
      >
      <PrimaryButton
        @click="$emit('save')"
         :disabled="bankAccount.processing"
        class="px-6 py-2 ml-2 bg-blue-600 dark:bg-blue-600 text-blue-100 dark:text-blue-200 rounded"
      >
        Guardar
      </PrimaryButton>
    </template>
  </DialogModal>
</template>