<script setup lang="ts">
// Imports
import { useFocusNextField } from "@/composables/useFocusNextField";
import { PrimaryButton, DialogModal,SecondaryButton,InputError,TextInput,InputLabel } from "@/Components";
import { Errors, Person } from "@/types";

// Props
defineProps<{
  person: Person; // Paginación de los bancos
  error: Errors;
  show: boolean;
}>();

const { focusNextField } = useFocusNextField();

// Emits
defineEmits(["close", "save"]);

</script>

<template>
  <DialogModal :show="show" maxWidth="lg" @close="$emit('close')">
    <template #title>
      {{ `${person.id === undefined ? "Añadir" : "Editar"} personas` }}
    </template>
    <template #content>
      <div class="mt-4">
        <form
          class="w-2xl grid grid-cols-1 gap-3"
          @keydown.enter.prevent="focusNextField"
        >
          <div class="col-span-6 sm:col-span-4">
            <InputLabel for="identification" value="Cedula/" />
            <TextInput
              v-model="person.identification"
              type="text"
              class="mt-1 block w-full"
              min="1"
              max="999"
            />
            <InputError :message="error.identification" class="mt-2" />
          </div>
          <div class="col-span-6 sm:col-span-4">
            <InputLabel for="name" value="Nombre" />
            <TextInput
              v-model="person.name"
              type="text"
              class="mt-1 block w-full"
              min="1"
              max="999"
            />
            <InputError :message="error.first_name" class="mt-2" />
          </div>

          <div class="col-span-6 sm:col-span-4">
            <InputLabel for="email" value="Email" />
            <TextInput
              v-model="person.email"
              type="text"
              class="mt-1 block w-full"
              minlength="3"
              maxlength="300"
            />
            <InputError :message="error.email" class="mt-2" />
          </div>

          <div class="col-span-6 sm:col-span-4">
            <InputLabel for="phone" value="Celular" />
            <TextInput
              v-model="person.phone"
              type="text"
              class="mt-1 block w-full"
              minlength="3"
              maxlength="300"
            />
            <InputError :message="error.phone_number" class="mt-2" />
          </div>

          
          <div class="col-span-6 sm:col-span-4">
            <InputLabel for="address" value="Dirección" />
            <TextInput
              v-model="person.address"
              type="text"
              class="mt-1 block w-full"
              minlength="3"
              maxlength="300"
            />
            <InputError :message="error.address" class="mt-2" />
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
        :disabled="person.processing"
        class="px-6 py-2 ml-2 bg-blue-600 dark:bg-blue-600 text-blue-100 dark:text-blue-200 rounded"
      >
        Guardar
      </PrimaryButton>
    </template>
  </DialogModal>
</template>