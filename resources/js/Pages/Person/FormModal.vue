<script setup>
// Imports
import DialogModal from "@/Components/DialogModal.vue";
import InputError from "@/Components/InputError.vue";
import TextInput from "@/Components/TextInput.vue";
import InputLabel from "@/Components/InputLabel.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import DynamicSelect from "@/Components/DynamicSelect.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { useFocusNextField } from "@/composables/useFocusNextField";
import { ref } from "vue";

// Props
defineProps({
  person: { type: Object, default: () => ({}) },
  error: { type: Object, default: () => ({}) },
  show: { type: Boolean, default: false },
});

const { focusNextField } = useFocusNextField();

// Emits
defineEmits(["close", "save"]);

const stateGenderOptions = [
  { value: "1", label: "Femenino" },
  { value: "0", label: "Masculino" },
];
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
            <InputLabel for="identification" value="Identificación" />
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
            <InputLabel for="first_name" value="Nombre" />
            <TextInput
              v-model="person.first_name"
              type="text"
              class="mt-1 block w-full"
              min="1"
              max="999"
            />
            <InputError :message="error.first_name" class="mt-2" />
          </div>

          <div class="col-span-6 sm:col-span-4">
            <InputLabel for="last_name" value="Apellido" />
            <TextInput
              v-model="person.last_name"
              type="text"
              class="mt-1 block w-full"
              minlength="3"
              maxlength="300"
            />
            <InputError :message="error.last_name" class="mt-2" />
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
            <InputLabel for="phone_number" value="Celular" />
            <TextInput
              v-model="person.phone_number"
              type="text"
              class="mt-1 block w-full"
              minlength="3"
              maxlength="300"
            />
            <InputError :message="error.phone_number" class="mt-2" />
          </div>

          <div class="col-span-6 sm:col-span-4">
            <InputLabel for="gender" value="Genero" />
            <DynamicSelect
              v-model="person.gender"
              :options="stateGenderOptions"
              class="mt-1 block w-full"
              id="account_type"
            />
            <InputError :message="error.gender" class="mt-2" />
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
        class="px-6 py-2 ml-2 bg-blue-600 dark:bg-blue-600 text-blue-100 dark:text-blue-200 rounded"
      >
        Guardar
      </PrimaryButton>
    </template>
  </DialogModal>
</template>