<script setup>
// Imports
import DialogModal from "@/Components/DialogModal.vue";
import InputError from "@/Components/InputError.vue";
import TextInput from "@/Components/TextInput.vue";
import DynamicSelect from "@/Components/DynamicSelect.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import { useFocusNextField } from "@/composables/useFocusNextField";

// Props
const props = defineProps({
  advance: { type: Object, default: () => ({}) },
  error: { type: Object, default: () => ({}) },
  show: { type: Boolean, default: false },
  employees: { type: Array, default: () => [] },
});

const { focusNextField } = useFocusNextField();

// Emits
defineEmits(["close", "save"]);

// Opciones de empleados y tipo de activo
const employeesOptions = props.employees.map((employee) => ({
  value: employee.id,
  label: employee.name,
}));

const typeOptions = [
  { value: "utilidad", label: "UTILIDAD" },
  { value: "salario", label: "SALARIO" },
];

const paymethodOptions = [
  { value: "banco", label: "CHEQUE O TRANSFERENCIA" },
  { value: "caja", label: "CAJA O EFECTIVO" },
];

// Establecer la fecha actual si no está definida en advance.date
if (!props.advance.date) {
  const currentDate = new Date().toISOString().split("T")[0]; // Obtener la fecha actual en formato YYYY-MM-DD
  props.advance.date = currentDate;
}
</script>

<template>
  <DialogModal :show="show" maxWidth="lg" @close="$emit('close')">
    <template #title> Ingreso de adelantos </template>
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
              v-model="advance.employee_id"
              :options="employeesOptions"
              autofocus
            />
            <InputError :message="error.employee_id" class="mt-2" />
          </div>

          <div class="col-span-6 sm:col-span-4">
            <InputLabel for="detail" value="Detalle" />
            <TextInput
              v-model="advance.detail"
              type="text"
              class="mt-1 block w-full"
              minlength="3"
              maxlength="300"
              required
            />
            <InputError :message="error.detail" class="mt-2" />
          </div>

          <div class="col-span-6 sm:col-span-4">
            <InputLabel for="amount" value="Monto" />
            <TextInput
              v-model="advance.amount"
              type="number"
              class="mt-1 block w-full"
              minlength="3"
              maxlength="300"
              required
            />
            <InputError :message="error.amount" class="mt-2" />
          </div>

          <div class="col-span-6 sm:col-span-4">
            <InputLabel for="active_type_id" value="Tipo de avance" />
            <DynamicSelect
              class="mt-1 block w-full"
              v-model="advance.type"
              :options="typeOptions"
              autofocus
            />
            <InputError :message="error.type" class="mt-2" />
          </div>

          <!-- Mostrar el campo de métodos de pago solo si el tipo es "salario" -->
          <div
            v-if="advance.type === 'salario'"
            class="col-span-6 sm:col-span-4"
          >
            <InputLabel for="payment_type" value="Tipo de pago" />
            <DynamicSelect
              class="mt-1 block w-full"
              v-model="advance.payment_type"
              :options="paymethodOptions"
            />
            <InputError :message="error.payment_type" class="mt-2" />
          </div>

          <div class="col-span-6 sm:col-span-4">
            <InputLabel for="date" value="Fecha" />
            <TextInput
              v-model="advance.date"
              type="date"
              class="mt-1 block w-full"
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
        class="px-6 py-2 ml-2 bg-blue-600 dark:bg-blue-600 text-blue-100 dark:text-blue-200 rounded"
      >
        Guardar
      </PrimaryButton>
    </template>
  </DialogModal>
</template>
