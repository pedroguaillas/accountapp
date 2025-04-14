<script setup lang="ts">
// Imports
import { InputLabel,PrimaryButton,SecondaryButton,TextInput,InputError,DialogModal    } from "@/Components";
import { useFocusNextField } from "@/composables/useFocusNextField";
import { Errors, Expense } from "@/types";


// Props
const props = defineProps<{
  expense: Expense;
  error: Errors;
  show: boolean;
}>();

const { focusNextField } = useFocusNextField();

// Emits
defineEmits(["close", "save"]);
</script>


<template>
  <DialogModal :show="show" maxWidth="lg" @close="$emit('close')">
    <template #title> Ingreso de gastos </template>
    <template #content>
      <div class="mt-4">
        <form
          class="w-2xl grid grid-cols-1 gap-3"
          @keydown.enter.prevent="focusNextField"
        >
          <div class="col-span-6 sm:col-span-4">
            <InputLabel for="name" value="Nombre" />
            <TextInput
              v-model="expense.name"
              type="text"
              class="mt-1 block w-full"
              minlength="3"
              maxlength="300"
              required
            />
            <InputError :message="error.name" class="mt-2" />
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
        :disabled="expense.processing"
        class="px-6 py-2 ml-2 bg-blue-600 dark:bg-blue-600 text-blue-100 dark:text-blue-200 rounded"
      >
        Guardar
      </PrimaryButton>
    </template>
  </DialogModal>
</template>