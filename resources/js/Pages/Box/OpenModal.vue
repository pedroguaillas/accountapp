<script setup lang="ts">
// Imports
import { TextInput, SecondaryButton, PrimaryButton, InputLabel, InputError, DialogModal } from "@/Components";
import { useFocusNextField } from "@/composables/useFocusNextField";
import { computed } from "vue";

// Props
const props = defineProps({
  openForm: { type: Object, default: () => ({}) },
  error: { type: Object, default: () => ({}) },
  show: { type: Boolean, default: false },
});

const errors = computed(() => props.error);

const { focusNextField } = useFocusNextField();

// Emits
defineEmits(["close", "save"]);
</script>

<template>
  <DialogModal :show="show" maxWidth="lg" @close="$emit('close')">
    <template #title>
      {{ openForm.name ? "Abrir Caja - " + openForm.name : "Abrir Caja" }}
    </template>
    <template #content>
      <div class="mt-4">
        <form class="w-2xl grid grid-cols-1 gap-3" @keydown.enter.prevent="focusNextField">
          <!-- Campo para mostrar el nombre (solo de lectura, opcional) -->
          <div class="col-span-6 sm:col-span-4">
            <InputLabel for="name" value="Nombre de la caja" />
            <TextInput v-model="openForm.name" type="text" class="mt-1 block w-full" disabled />
            <InputError :message="errors.name" class="mt-2" />
          </div>
          <!-- Campo Monto Inicial -->
          <div class="col-span-6 sm:col-span-4">
            <InputLabel for="initial_value" value="Monto Inicial" />
            <TextInput v-model="openForm.initial_value" type="number" class="mt-1 block w-full" min="0" step="0.01" />
            <InputError :message="errors.initial_value" class="mt-2" />
          </div>
        </form>
      </div>
    </template>
    <template #footer>
      <SecondaryButton @click="$emit('close')" class="mr-2">
        Cancelar
      </SecondaryButton>
      <PrimaryButton @click="$emit('save')" :disabled="openForm.processing"
        class="px-6 py-2 ml-2 bg-blue-600 dark:bg-blue-600 text-blue-100 dark:text-blue-200 rounded">
        Guardar
      </PrimaryButton>
    </template>
  </DialogModal>
</template>
