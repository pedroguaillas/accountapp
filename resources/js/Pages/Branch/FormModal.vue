<script setup>
// Imports
import DialogModal from "@/Components/DialogModal.vue";
import InputError from "@/Components/InputError.vue";
import TextInput from "@/Components/TextInput.vue";
import InputLabel from "@/Components/InputLabel.vue";
import DynamicSelect from "@/Components/DynamicSelect.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import Checkbox from "@/Components/Checkbox.vue";
import { useFocusNextField } from "@/composables/useFocusNextField";
import { ref } from "vue";

// Props
defineProps({
  branch: { type: Object, default: () => ({}) },
  error: { type: Object, default: () => ({}) },
  show: { type: Boolean, default: false },
});

const { focusNextField } = useFocusNextField();

const enviromentTypeOptions = [
  { value: "1", label: "Prueba" },
  { value: "2", label: "Produccion" },
];

const isChecked = ref(false);
// Emits
defineEmits(["close", "save"]);
</script>

<template>
  <DialogModal :show="show" maxWidth="lg" @close="$emit('close')">
    <template #title>
      {{ `${branch.id === undefined ? "Añadir" : "Editar"} Establecimientos` }}
    </template>
    <template #content>
      <div class="mt-4">
        <form class="w-2xl grid grid-cols-1 gap-3"  @keydown.enter.prevent="focusNextField">
          <div class="col-span-6 sm:col-span-4">
            <InputLabel for="number" value="Número de establecimiento" />
            <TextInput
              v-model="branch.number"
              type="number"
              class="mt-1 block w-full"
              min="1"
              max="999"
            />
            <InputError :message="error.number" class="mt-2" />
          </div>

          <div class="col-span-6 sm:col-span-4">
            <InputLabel for="name" value="Nombre comercial" />
            <TextInput
              v-model="branch.name"
              type="text"
              class="mt-1 block w-full"
              minlength="3"
              maxlength="300"
            />
            <InputError :message="error.name" class="mt-2" />
          </div>
          <div class="col-span-6 sm:col-span-4">
            <InputLabel for="city" value="Ciudad" />
            <TextInput
              v-model="branch.city"
              type="text"
              class="mt-1 block w-full"
              minlength="3"
              maxlength="300"
            />
            <InputError :message="error.city" class="mt-2" />
          </div>
          <div class="col-span-6 sm:col-span-4">
            <InputLabel for="address" value="Dirección" />
            <TextInput
              v-model="branch.address"
              type="text"
              class="mt-1 block w-full"
              minlength="3"
              maxlength="300"
            />
            <InputError :message="error.address" class="mt-2" />
          </div>

          <Checkbox v-model:checked="branch.is_matriz" label="Matriz" />

          <div class="col-span-6 sm:col-span-4">
            <InputLabel for="enviroment_type" value="Ambiente" />
            <DynamicSelect
              class="mt-1 block w-full"
              v-model="branch.enviroment_type"
              :options="enviromentTypeOptions"
              autofocus
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
         :disabled="branch.processing"
        class="px-6 py-2 ml-2 bg-blue-600 dark:bg-blue-600 text-blue-100 dark:text-blue-200 rounded"
      >
        Guardar
      </PrimaryButton>
    </template>
  </DialogModal>
</template>