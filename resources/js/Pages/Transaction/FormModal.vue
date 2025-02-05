<script setup>
// Imports
import DialogModal from "@/Components/DialogModal.vue";
import InputError from "@/Components/InputError.vue";
import TextInput from "@/Components/TextInput.vue";
import InputLabel from "@/Components/InputLabel.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";

import { useFocusNextField } from "@/composables/useFocusNextField";
import { ref } from "vue";

// Props
const props = defineProps({
  transaction: { type: Object, default: () => ({}) },
  bankaccounts: { type: Array, default: () => [] },
  movementtypes: { type: Array, default: () => [] },
  error: { type: Object, default: () => ({}) },
  show: { type: Boolean, default: false },
});

const { focusNextField } = useFocusNextField();

// Opciones para el estado de la transacción
const stateOptions = [
  { value: "in_process", label: "En proceso" },
  { value: "completed", label: "Completada" },
  { value: "rejected", label: "Rechazada" },
];

// Emits
const emit = defineEmits(["close", "save"]);
</script>

<template>
  <DialogModal :show="show" maxWidth="lg" @close="$emit('close')">
    <template #title>
      {{ `${transaction.id === undefined ? "Añadir" : "Editar"} transacción` }}
    </template>
    <template #content>
      <div class="mt-4">
        <form
          class="w-2xl grid grid-cols-1 gap-3"
          @keydown.enter.prevent="focusNextField"
        >
          <!-- Campo de Fecha -->
          <div class="col-span-6 sm:col-span-4">
            <InputLabel for="date" value="Fecha de la transacción" />
            <TextInput
              v-model="transaction.date"
              type="date"
              class="mt-1 block w-full"
            />
            <InputError :message="error.date" class="mt-2" />
          </div>

          <!-- Campo de Tipo de Movimiento -->
          <div class="col-span-6 sm:col-span-4">
            <InputLabel for="movement_type" value="Tipo de Movimiento" />
            <select
              v-model="transaction.movement_type_id"
              class="mt-1 block w-full"
            >
              <option value="" disabled>Seleccione un tipo de movimiento</option>
              <option
                v-for="type in movementtypes"
                :key="type.id"
                :value="type.id"
              >
                {{ type.name }}
              </option>
            </select>
            <InputError :message="error.movement_type_id" class="mt-2" />
          </div>

          <!-- Campo de Cuenta Bancaria -->
          <div class="col-span-6 sm:col-span-4">
            <InputLabel for="bank_account" value="Cuenta Bancaria" />
            <select
              v-model="transaction.bank_account_id"
              class="mt-1 block w-full"
            >
              <option value="" disabled>Seleccione una cuenta bancaria</option>
              <option
                v-for="account in bankaccounts"
                :key="account.id"
                :value="account.id"
              >
                {{ account.account_number }} - {{ account.bank_name }}
              </option>
            </select>
            <InputError :message="error.bank_account_id" class="mt-2" />
          </div>

          <!-- Campo de Monto -->
          <div class="col-span-6 sm:col-span-4">
            <InputLabel for="amount" value="Monto" />
            <TextInput
              v-model="transaction.amount"
              type="number"
              class="mt-1 block w-full"
              min="0"
              step="0.01"
            />
            <InputError :message="error.amount" class="mt-2" />
          </div>

          <!-- Campo de Descripción -->
          <div class="col-span-6 sm:col-span-4">
            <InputLabel for="description" value="Descripción" />
            <TextInput
              v-model="transaction.description"
              type="text"
              class="mt-1 block w-full"
              minlength="3"
              maxlength="300"
            />
            <InputError :message="error.description" class="mt-2" />
          </div>

          <!-- Campo de Estado -->
          <div class="col-span-6 sm:col-span-4">
            <InputLabel for="status" value="Estado" />
            <select v-model="transaction.status" class="mt-1 block w-full">
              <option value="" disabled>Seleccione un estado</option>
              <option
                v-for="state in stateOptions"
                :key="state.value"
                :value="state.value"
              >
                {{ state.label }}
              </option>
            </select>
            <InputError :message="error.status" class="mt-2" />
          </div>
        </form>
      </div>
    </template>
    <template #footer>
      <SecondaryButton @click="$emit('close')" class="mr-2">
        Cancelar
      </SecondaryButton>
      <PrimaryButton @click="$emit('save')" class="px-6 py-2 ml-2">
        Guardar
      </PrimaryButton>
    </template>
  </DialogModal>
</template>
