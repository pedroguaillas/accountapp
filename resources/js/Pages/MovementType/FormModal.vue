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
  movementtype: { type: Object, default: () => ({}) },
  error: { type: Object, default: () => ({}) },
  show: { type: Boolean, default: false },
});

const { focusNextField } = useFocusNextField();

// Emits
defineEmits(["close", "save"]);

const TypeOptions = [
  { value: "Ingreso", label: "Ingreso" },
  { value: "Egreso", label: "Egreso" },
];
</script>

<template>
  <DialogModal :show="show" maxWidth="lg" @close="$emit('close')">
    <template #title>
      {{
        `${
          movementtype.id === undefined ? "Añadir" : "Editar"
        } movimiento bancario`
      }}
    </template>
    <template #content>
      <div class="mt-4">
        <form
          class="w-2xl grid grid-cols-1 gap-3"
          @keydown.enter.prevent="focusNextField"
        >
          <div class="col-span-6 sm:col-span-4">
            <InputLabel for="name" value="Nombre" />
            <TextInput
              v-model="movementtype.name"
              type="text"
              class="mt-1 block w-full"
              min="1"
              max="999"
            />
            <InputError :message="error.name" class="mt-2" />
          </div>

          <div class="col-span-6 sm:col-span-4">
            <InputLabel for="type" value="Tipo" />
            <DynamicSelect
              v-model="movementtype.type"
              :options="TypeOptions"
              placeholder="Seleccione un tipo"
              class="mt-2 block w-full"
            />
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