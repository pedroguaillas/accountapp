<script setup lang="ts">
// Imports
import { useFocusNextField } from "@/composables/useFocusNextField";
import { TextInput, SecondaryButton, PrimaryButton, InputLabel, InputError, DialogModal } from "@/Components";
import { CostCenter } from "@/types/cost-center";
import { Errors } from "@/types";
import { computed } from "vue";

// Props
const props = defineProps<{
  costCenter: CostCenter;
  error: Errors;
  show: boolean;
}>();

const errors = computed(() => props.error);

const { focusNextField } = useFocusNextField();

// Emits
defineEmits(["close", "save"]);
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
            <TextInput v-model="costCenter.code" type="text" class="mt-1 block w-full" minlength="3" maxlength="50" />
            <InputError :message="errors.code" class="mt-2" />
          </div>

          <div class="col-span-6 sm:col-span-4">
            <InputLabel for="name" value="Nombre" />
            <TextInput v-model="costCenter.name" type="text" class="mt-1 block w-full" minlength="3" maxlength="50" />
            <InputError :message="errors.name" class="mt-2" />
          </div>
        </form>
      </div>
    </template>
    <template #footer>
      <SecondaryButton @click="$emit('close')" class="mr-2">Cancelar</SecondaryButton>
      <PrimaryButton @click="$emit('save')" :disabled="costCenter.processing"
        class="px-6 py-2 ml-2 bg-blue-600 dark:bg-blue-600 text-blue-100 dark:text-blue-200 rounded">
        Guardar
      </PrimaryButton>
    </template>
  </DialogModal>
</template>