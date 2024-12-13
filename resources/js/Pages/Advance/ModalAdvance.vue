<script setup>
// Imports
import DialogModal from "@/Components/DialogModal.vue";
import InputError from "@/Components/InputError.vue";
import TextInput from "@/Components/TextInput.vue";
import DynamicSelect from "@/Components/DynamicSelect.vue";
import InputLabel from "@/Components/InputLabel.vue";
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

const employeesOptions = props.employees.map(
  (employee) => ({
    value:employee.id,
    label:employee.name,
  })
);

</script>

<template>
  <DialogModal :show="show" maxWidth="lg" @close="$emit('close')">
    <template #title>
      Ingreso de adelantos
    </template>
    <template #content>
      <div class="mt-4">
        <form class="w-2xl grid grid-cols-1 gap-3"  @keydown.enter.prevent="focusNextField">
          <div class="col-span-6 sm:col-span-4">
            <InputLabel
              for="employee_id"
              value="Empleado"
            />
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