<script setup lang="ts">
// Imports
import { TextInput, DialogModal, InputError, InputLabel, SecondaryButton, PrimaryButton } from "@/Components";
import { useFocusNextField } from "@/composables/useFocusNextField";
import { Bank, Errors } from "@/types";

// Props
const props = defineProps<{
  bank: Bank;
  error: Errors;
  show: boolean;
}>();

const { focusNextField } = useFocusNextField();

const stateTypeOptions = [
  { value: "1", label: "Activo" },
  { value: "0", label: "Inactivo" },
];

// Emits
defineEmits(["close", "save"]);
</script>

<template>
  <DialogModal :show="show" maxWidth="lg" @close="$emit('close')">
    <template #title>
      {{ `${bank.id === undefined ? "Añadir" : "Editar"} bancos` }}
    </template>
    <template #content>
      <div class="mt-4">
        <form class="w-2xl grid grid-cols-1 gap-3" @keydown.enter.prevent="focusNextField">
          <div class="col-span-6 sm:col-span-4">
            <InputLabel for="name" value="Nombre del banco" />
            <TextInput v-model="bank.name" type="text" class="mt-1 block w-full" min="1" max="999" />
            <InputError :message="error.name" class="mt-2" />
          </div>

          <div class="col-span-6 sm:col-span-4">
            <InputLabel for="description" value="Descripción" />
            <TextInput v-model="bank.description" type="text" class="mt-1 block w-full" minlength="3" maxlength="300" />
            <InputError :message="error.description" class="mt-2" />
          </div>
        </form>
      </div>
    </template>
    <template #footer>
      <SecondaryButton @click="$emit('close')" class="mr-2">Cancelar</SecondaryButton>
      <PrimaryButton @click="$emit('save')" :disabled="bank.processing"
        class="px-6 py-2 ml-2 bg-blue-600 dark:bg-blue-600 text-blue-100 dark:text-blue-200 rounded">
        Guardar
      </PrimaryButton>
    </template>
  </DialogModal>
</template>