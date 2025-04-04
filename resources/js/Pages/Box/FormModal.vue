<script setup>
// Imports
import DialogModal from "@/Components/DialogModal.vue";
import InputError from "@/Components/InputError.vue";
import TextInput from "@/Components/TextInput.vue";
import InputLabel from "@/Components/InputLabel.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import DynamicSelect from "@/Components/DynamicSelect.vue";
import { useFocusNextField } from "@/composables/useFocusNextField";
import Checkbox from "@/Components/Checkbox.vue";
import { ref, computed, watch } from "vue";

// Props
const props = defineProps({
  box: { type: Object, default: () => ({}) },
  error: { type: Object, default: () => ({}) },
  show: { type: Boolean, default: false },
  employees: { type: Array, default: () => [] },
});

const { focusNextField } = useFocusNextField();

// Emits
defineEmits(["close", "save"]);

const EmployeesOptions = computed(() =>
  Array.isArray(props.employees)
    ? props.employees.map((employee) => ({
        value: employee.id,
        label: employee.name,
      }))
    : []
);

// Seleccionar automáticamente el único empleado si hay solo uno
const selectedEmployee = ref(props.box.owner_id || null);

const TypeOptions = [
  { value: "chica", label: "Chica" },
  { value: "otras", label: "Otras" },
];

</script>

<template>
  <DialogModal :show="show" maxWidth="lg" @close="$emit('close')">
    <template #title>
      {{ `${box.id === undefined ? "Añadir" : "Editar"} cajas` }}
    </template>
    <template #content>
      <div class="mt-4">
        <form
          class="w-2xl grid grid-cols-1 gap-3"
          @keydown.enter.prevent="focusNextField"
        >
          <div
            v-if="box.name === 'CAJA GENERAL'"
            class="col-span-6 sm:col-span-4 flex items-center mt-2"
          >
          </div>
          <div class="col-span-6 sm:col-span-4">
            <InputLabel for="name" value="Nombre de la caja" />
            <TextInput
              v-model="box.name"
              type="text"
              class="mt-1 block w-full"
              min="1"
              max="999"
            />
            <InputError :message="error.name" class="mt-2" />
          </div>

          <div class="col-span-6 sm:col-span-4">
            <InputLabel for="owner_id" value="Empleado a cargo" />
            <DynamicSelect
              class="mt-1 block w-full"
              v-model="box.owner_id"
              :options="EmployeesOptions"
              :value="selectedEmployee"
              autofocus
              :required="true"
            />
            <InputError :message="error.owner_id" class="mt-2" />
          </div>

          <div  v-if="box.name !== 'CAJA GENERAL'" class="col-span-6 sm:col-span-4">
            <InputLabel for="owner_id" value="Tipo" />
            <DynamicSelect
              class="mt-1 block w-full"
              v-model="box.type"
              :options="TypeOptions"
              autofocus
              :required="true"
            />
            <InputError :message="error.type" class="mt-2" />
          </div>
        </form>
      </div>
    </template>
    <template #footer>
      <SecondaryButton @click="$emit('close')" class="mr-2">
        Cancelar
      </SecondaryButton>
      <PrimaryButton
        @click="$emit('save')"
         :disabled="box.processing"
        class="px-6 py-2 ml-2 bg-blue-600 dark:bg-blue-600 text-blue-100 dark:text-blue-200 rounded"
      >
        Guardar
      </PrimaryButton>
    </template>
  </DialogModal>
</template>
