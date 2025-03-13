<script setup>
// Imports
import { reactive} from "vue";
import AdminLayout from "@/Layouts/AdminLayout.vue";
import { useForm } from "@inertiajs/vue3";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import Checkbox from "@/Components/Checkbox.vue";

// Props
const props = defineProps({
  employee: { type: Object, default: () => ({}) },
});

// Reactives
const employee = useForm({ ...props.employee });
const errorForm = reactive({});

// Método de guardar con mensajes
const save = () => {
  employee.put(route("employee.update", employee.id), {
    onError: (errors) => {
      Object.keys(errors).forEach((key) => {
        errorForm[key] = errors[key];
      });
    },
  });
};
</script>

<template>
  <AdminLayout title="Empleados">
    <!-- Card -->
    <div class="p-4 bg-white rounded drop-shadow-md">
      <!-- Card Header -->
      <div class="flex justify-between items-center">
        <h2 class="text-sm sm:text-lg font-bold">Modificar empleado</h2>
      </div>

      <!-- Formulario -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- Primera columna -->
        <div class="grid grid-cols-1 gap-4">
          <div class="col-span-6 sm:col-span-4">
            <InputLabel for="cuit" value="Cédula" />
            <TextInput
              v-model="employee.cuit"
              type="text"
              class="mt-1 block w-full"
              minlegth="10"
              maxlength="13"
              required
            />
            <InputError :message="errorForm.cuit" class="mt-2" />
          </div>

          <div class="col-span-6 sm:col-span-4">
            <InputLabel for="name" value="Nombre y apellido" />
            <TextInput
              v-model="employee.name"
              type="text"
              class="mt-1 block w-full"
              minlength="3"
              maxlength="300"
              required
            />
            <InputError :message="errorForm.name" class="mt-2" />
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
            <InputError :message="errorForm.sector_code" class="mt-2" />
          </div>
          <div class="col-span-6 sm:col-span-4">
            <InputLabel for="position" value="Cargo" />
            <TextInput
              v-model="employee.position"
              type="text"
              class="mt-1 block w-full"
              minlength="3"
              maxlength="300"
              required
            />
            <InputError :message="errorForm.position" class="mt-2" />
          </div>

          <div class="col-span-6 sm:col-span-4">
            <InputLabel for="days" value="Días" />
            <TextInput
              v-model="employee.days"
              type="number"
              class="mt-1 block w-full"
              min="0"
              required
            />
            <InputError :message="errorForm.days" class="mt-2" />
          </div>

          <div class="col-span-6 sm:col-span-4">
            <InputLabel for="salary" value="Salario ($)" />
            <TextInput
              v-model="employee.salary"
              type="number"
              class="mt-1 block w-full"
              min="0"
              required
            />
            <InputError :message="errorForm.salary" class="mt-2" />
          </div>

          <div class="col-span-6 sm:col-span-4">
            <InputLabel
              for="porcent_aportation"
              value="Aportación (%)"
            />
            <TextInput
              v-model="employee.porcent_aportation"
              type="number"
              class="mt-1 block w-full"
              min="0"
            />
            <InputError :message="errorForm.porcent_aportation" class="mt-2" />
          </div>
        </div>

        <!-- segunda columna -->
        <div class="grid grid-cols-1 gap-4">
          <div class="col-span-6 sm:col-span-4">
            <InputLabel for="date_start" value="Fecha de inicio" />
            <TextInput
              v-model="employee.date_start"
              type="date"
              class="mt-1 block w-full"
              required
            />
            <InputError :message="errorForm.date_start" class="mt-2" />
          </div>

          <div class="col-span-6 sm:col-span-4">
            <InputLabel for="email" value="Correo" />
            <TextInput
              v-model="employee.email"
              type="email"
              class="mt-1 block w-full"
              minlength="3"
              maxlength="300"
              required
            />
            <InputError :message="errorForm.email" class="mt-2" />
          </div>

          <div class="col-span-6 sm:col-span-4">
            <Checkbox
              v-model:checked="employee.is_a_parnert"
              label="Es socio o propietario?"
            />
          </div>

          <div class="col-span-6 sm:col-span-4">
            <Checkbox
              v-model:checked="employee.is_a_qualified_craftsman"
              label="Es artesano calificado?"
            />
          </div>

          <div class="col-span-6 sm:col-span-4">
            <Checkbox
              v-model:checked="employee.affiliated_with_spouse"
              label="Afiliación al cónyuge?"
            />
          </div>

          <div class="col-span-6 sm:col-span-4">
            <Checkbox
              v-model:checked="employee.xiii"
              label="Acumulación décimo tercero?"
            />
          </div>

          <div class="col-span-6 sm:col-span-4">
            <Checkbox
              v-model:checked="employee.xiv"
              label="Acumulación décimo cuarto?"
            />
          </div>

          <div class="col-span-6 sm:col-span-4">
            <Checkbox
              v-model:checked="employee.reserve_funds"
              label="Acumulación fondos de reserva?"
            />
          </div>
        </div>
      </div>
      <div class="mt-4 text-right">
        <button 
        :disabled="employee.processing"
        @click="save" class="px-4 py-2 bg-primary hover:bg-primaryhover text-white rounded">
          Guardar
        </button>
      </div>
    </div>
  </AdminLayout>
</template>
