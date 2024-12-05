<script setup>
// Imports
import DialogModal from "@/Components/DialogModal.vue";
import InputError from "@/Components/InputError.vue";
import TextInput from "@/Components/TextInput.vue";
import InputLabel from "@/Components/InputLabel.vue";
import DynamicSelect from "@/Components/DynamicSelect.vue";
import Checkbox from "@/Components/Checkbox.vue";
import { useFocusNextField } from "@/composables/useFocusNextField";
import { ref } from "vue";

// Props
defineProps({
  account: { type: Object, default: () => ({}) },
  error: { type: Object, default: () => ({}) },
  show: { type: Boolean, default: false },
});

const { focusNextField } = useFocusNextField();

// Emits
defineEmits(["close", "save"]);
</script>

<template>
  <DialogModal :show="show" maxWidth="lg" @close="$emit('close')">
    <template #title>
      {{ `${account.id === undefined ? "Añadir" : "Editar"} cuenta` }}
    </template>
    <template #content>
      <div class="mt-4">
        <form class="w-2xl grid grid-cols-1 gap-3" @keydown.enter.prevent="focusNextField">

          <div v-if="!account.id" class="col-span-6 sm:col-span-4">
            <InputLabel for="code" value="Cuenta" />
            <TextInput v-model="account.code" type="text" class="mt-1 block w-full" minlength="3" maxlength="20"
              required />
            <InputError :message="error.code" class="mt-2" />
          </div>

          <div class="col-span-6 sm:col-span-4">
            <InputLabel for="name" value="Descripción" />
            <TextInput v-model="account.name" type="text" class="mt-1 block w-full" minlength="3" maxlength="300"
              required />
            <InputError :message="error.name" class="mt-2" />
          </div>

        </form>
      </div>
    </template>
    <template #footer>
      <button @click="$emit('save')"
        class="px-6 py-2 ml-2 bg-blue-600 dark:bg-blue-600 text-blue-100 dark:text-blue-200 rounded">
        Guardar
      </button>
    </template>
  </DialogModal>
</template>