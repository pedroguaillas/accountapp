<script setup lang="ts">
// Imports
import { TextInput, SecondaryButton, PrimaryButton, DynamicSelect, InputLabel, InputError, DialogModal } from "@/Components";
import { useFocusNextField } from "@/composables/useFocusNextField";
import { Employee, Errors, Hour } from "@/types";

// Props
const props = defineProps<{
  hour: Hour;
  error: Errors;
  show: boolean;
  employees: Employee[];
  date:string;
}>();

const { focusNextField } = useFocusNextField();

// Emits
defineEmits(["close", "save"]);

// Opciones de empleados y tipo de activo
const employeesOptions = props.employees.map((employee) => ({
  value: employee.id !== undefined ? employee.id : 0,
  label: employee.name,
}));

const typeOptions = [
  { value: "extra", label: "EXTRA" },
  { value: "normal", label: "NORMAL" },
];
</script>

<template>
  <DialogModal :show="show" maxWidth="lg" @close="$emit('close')">
    <template #title> Ingreso de horas </template>
    <template #content>
      <div class="mt-4">
        <form
          class="w-2xl grid grid-cols-1 gap-3"
          @keydown.enter.prevent="focusNextField"
        >
          <div class="col-span-6 sm:col-span-4">
            <InputLabel for="employee_id" value="Empleado" />
            <DynamicSelect
              class="mt-1 block w-full"
              v-model="hour.employee_id"
              :options="employeesOptions"
              autofocus
            />
            <InputError :message="error.employee_id" class="mt-2" />
          </div>

          <div class="col-span-6 sm:col-span-4">
            <InputLabel for="detail" value="Detalle" />
            <TextInput
              v-model="hour.detail"
              type="text"
              class="mt-1 block w-full"
              minlength="3"
              maxlength="300"
              required
            />
            <InputError :message="error.detail" class="mt-2" />
          </div>

          <div class="col-span-6 sm:col-span-4">
            <InputLabel for="amount" value="Cantidad" />
            <TextInput
              v-model="hour.amount"
              type="number"
              class="mt-1 block w-full"
              minlength="3"
              maxlength="300"
              required
            />
            <InputError :message="error.amount" class="mt-2" />
          </div>

          <div class="col-span-6 sm:col-span-4">
            <InputLabel for="active_type_id" value="Tipo de horas" />
            <DynamicSelect
              class="mt-1 block w-full"
              v-model="hour.type"
              :options="typeOptions"
              autofocus
            />
            <InputError :message="error.type" class="mt-2" />
          </div>

          <div class="col-span-6 sm:col-span-4">
            <InputLabel for="date" value="Fecha" />
            <TextInput
              v-model="hour.date"
              type="date"
              class="mt-1 block w-full"
              :max="props.date"
            />
            <InputError :message="error.date" class="mt-2" />
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
        :disabled="hour.processing"
        class="px-6 py-2 ml-2 bg-blue-600 dark:bg-blue-600 text-blue-100 dark:text-blue-200 rounded"
      >
        Guardar
      </PrimaryButton>
    </template>
  </DialogModal>
</template>