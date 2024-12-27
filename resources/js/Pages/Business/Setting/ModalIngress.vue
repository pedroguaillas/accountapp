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
  ingress: { type: Object, default: () => ({}) },
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
      {{
        `${ingress.id === undefined ? "Añadir" : "Editar"}Ingresos`
      }}
    </template>
    <template #content>
      <div class="mt-4">
        <form class="w-2xl grid grid-cols-1 gap-3" @keydown.enter.prevent="focusNextField">
          <div class="col-span-6 sm:col-span-4">
            <InputLabel for="code" value="Código" />
            <TextInput v-model="ingress.code" type="text" class="mt-1 block w-full" />
            <InputError :message="error.code" class="mt-2" />
          </div>

          <div class="col-span-6 sm:col-span-4">
            <InputLabel for="name" value="Nombre" />
            <TextInput v-model="ingress.name" type="text" class="mt-1 block w-full" minlength="3" maxlength="50" />
            <InputError :message="error.name" class="mt-2" />
          </div>

        </form>
      </div>
    </template>
    <template #footer>
      <button @click="$emit('save')" :disabled="ingress.processing"
        class="px-6 py-2 ml-2 bg-blue-600 dark:bg-blue-600 text-blue-100 dark:text-blue-200 rounded">
        Guardar
      </button>
    </template>
  </DialogModal>
</template>