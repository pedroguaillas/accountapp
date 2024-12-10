<script setup>
// Imports
import DialogModal from "@/Components/DialogModal.vue";
import InputError from "@/Components/InputError.vue";
import TextInput from "@/Components/TextInput.vue";
import InputLabel from "@/Components/InputLabel.vue";
import Checkbox from "@/Components/Checkbox.vue";
import { useFocusNextField } from "@/composables/useFocusNextField";

// Props
defineProps({
  employee: { type: Object, default: () => ({}) },
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
      {{ `${employee.id === undefined ? "Añadir" : "Editar"} Empleados` }}
    </template>
    <template #content>
      <div class="mt-4">
        <form
          class="w-2xl grid grid-cols-1 gap-3"
          @keydown.enter.prevent="focusNextField"
        >
          <div class="col-span-6 sm:col-span-4">
            <InputLabel for="cuit" value="Cedula" />
            <TextInput
              v-model="employee.cuit"
              type="text"
              class="mt-1 block w-full"
              minlegth="10"
              maxlength="13"
            />
            <InputError :message="error.cuit" class="mt-2" />
          </div>

          <div class="col-span-6 sm:col-span-4">
            <InputLabel for="name" value="Nombre y apellido" />
            <TextInput
              v-model="employee.name"
              type="text"
              class="mt-1 block w-full"
              minlength="3"
              maxlength="300"
            />
            <InputError :message="error.name" class="mt-2" />
          </div>
          <div class="col-span-6 sm:col-span-4">
            <InputLabel for="city" value="Código sectorial" />
            <TextInput
              v-model="employee.sector_code"
              type="text"
              class="mt-1 block w-full"
              minlength="3"
              maxlength="300"
            />
            <InputError :message="error.sector_code" class="mt-2" />
          </div>
          <div class="col-span-6 sm:col-span-4">
            <InputLabel for="post" value="Cargo" />
            <TextInput
              v-model="employee.post"
              type="text"
              class="mt-1 block w-full"
              minlength="3"
              maxlength="300"
            />
            <InputError :message="error.post" class="mt-2" />
          </div>

          <div class="col-span-6 sm:col-span-4">
            <InputLabel for="days" value="Dias" />
            <TextInput
              v-model="employee.days"
              type="number"
              class="mt-1 block w-full"
              min="0"
            />
            <InputError :message="error.days" class="mt-2" />
          </div>

          <div class="col-span-6 sm:col-span-4">
            <InputLabel for="salary" value="Salario" />
            <TextInput
              v-model="employee.salary"
              type="number"
              class="mt-1 block w-full"
              min="0"
            />
            <InputError :message="error.salary" class="mt-2" />
          </div>

          <div class="col-span-6 sm:col-span-4">
            <InputLabel
              for="porcent_aportation"
              value="Porcentaje de aportacion"
            />
            <TextInput
              v-model="employee.porcent_aportation"
              type="number"
              class="mt-1 block w-full"
              min="0"
            />
            <InputError :message="error.porcent_aportation" class="mt-2" />
          </div>

          <Checkbox
            v-model:checked="employee.is_a_parner"
            label="Es socio o propietario?"
          />
          <Checkbox
            v-model:checked="employee.is_a_qualified_craftsman"
            label="Es artesano calificado?"
          />
          <Checkbox
            v-model:checked="employee.affiliated_with_spouse"
            label="Afiliación al cónyuge?"
          />

          <div class="col-span-6 sm:col-span-4">
            <InputLabel for="date_start" value="Fecha de inicio" />
            <TextInput
              v-model="employee.date_start"
              type="date"
              class="mt-1 block w-full"
              required
            />
            <InputError :message="error.date_start" class="mt-2" />
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