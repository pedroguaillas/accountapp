<script setup>
// Imports
import DialogModal from "@/Components/DialogModal.vue";
import InputError from "@/Components/InputError.vue";
import TextInput from "@/Components/TextInput.vue";
import DynamicSelect from "@/Components/DynamicSelect.vue";
import InputLabel from "@/Components/InputLabel.vue";
import { useFocusNextField } from "@/composables/useFocusNextField";
// Props
defineProps({
  costCenter: { type: Object, default: () => ({}) },
  error: { type: Object, default: () => ({}) },
  show: { type: Boolean, default: false },
});

const { focusNextField } = useFocusNextField();

// Emits
defineEmits(["close", "save"]);

const stateOptions = [
  { value: "A", label: "Activo" },
  { value: "I", label: "Inactivo" },
];

const typeOptions = [
  { value: "T", label: "Transaccional" },
  { value: "G", label: "Global" },
];
</script>

<template>
  <DialogModal :show="show" maxWidth="lg" @close="$emit('close')">
    <template #title>
      {{
        `${costCenter.id === undefined ? "Añadir" : "Editar"} centro de costo`
      }}
    </template>
    <template #content>
      <div class="mt-4">
        <form class="w-2xl grid grid-cols-1 gap-3" @keydown.enter.prevent="focusNextField">
          <div class="col-span-6 sm:col-span-4">
            <InputLabel for="code" value="Código" />
            <TextInput
              v-model="costCenter.code"
              type="text"
              class="mt-1 block w-full"
              minlength="3"
              maxlength="50"
            />
            <InputError :message="error.code" class="mt-2" />
          </div>

          <div class="col-span-6 sm:col-span-4">
            <InputLabel for="name" value="Nombre" />
            <TextInput
              v-model="costCenter.name"
              type="text"
              class="mt-1 block w-full"
              minlength="3"
              maxlength="50"
            />
            <InputError :message="error.name" class="mt-2" />
          </div>

          <div class="col-span-6 sm:col-span-4">
            <InputLabel for="type" value="Tipo" />
            <DynamicSelect
              class="mt-1 block w-full"
              v-model="costCenter.type"
              :options="typeOptions"
              autofocus
            />
            <InputError :message="error.type" class="mt-2" />
          </div>

          <div class="col-span-6 sm:col-span-4">
            <InputLabel for="state" value="Estado" />
            <DynamicSelect
              class="mt-1 block w-full"
              v-model="costCenter.state"
              :options="stateOptions"
              autofocus
            />
            <InputError :message="error.state" class="mt-2" />
          </div>
        </form>
      </div>
    </template>
    <template #footer>
      <button
        @click="$emit('save')"
        class="px-6 py-2 ml-2 bg-blue-600 dark:bg-blue-600 text-blue-100 dark:text-blue-200 rounded"
      >
        Guardar
      </button>
    </template>
  </DialogModal>
</template>