<script setup>
// Imports
import { reactive, computed } from "vue";
import AdminLayout from "@/Layouts/AdminLayout.vue";
import { useForm } from "@inertiajs/vue3";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import Checkbox from "@/Components/Checkbox.vue";

// Props
const date = new Date().toISOString().split("T")[0];

// Inicializador de objetos
const initialEmployee = {
  cuit: "",
  name: "",
  sector_code: "",
  position: "",
  days: "",
  salary: "",
  porcent_aportation: "",
  is_a_parnert: false,
  is_a_qualified_craftsman: false,
  affiliated_with_spouse: false,
  date_start: date,
  xiii: false,
  xiv: false,
  reserve_funds: false,
  email: "",
};

// Reactives
const employee = useForm({ ...initialEmployee });
const errorForm = reactive({});

const resetErrorForm = () => {
  Object.assign(errorForm, { ...initialEmployee, date_start: "" });
};

// Método de guardar con mensajes
const save = () => {
  // Enviar datos al backend
  employee.post(route("employee.store"), {
    onError: (errors) => {
      // Mostrar errores de validación desde el backend
      Object.keys(errors).forEach((key) => {
        errorForm[key] = errors[key];
      });
    },
    // onSuccess: () => {
    //   // Reiniciar el formulario tras éxito
    //   employee.reset();
    //   resetErrorForm();
    // },
  });
};
</script>

<template>
  <AdminLayout title="Empleados">
    <!-- Card -->
    <div class="p-4 bg-white rounded drop-shadow-md">
      <!-- Card Header -->
      <div class="flex justify-between items-center">
        <h2 class="text-sm sm:text-lg font-bold">Registrar empleado</h2>
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
            <InputLabel for="salary" value="Salario" />
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
            <InputLabel for="porcent_aportation" value="Aportación (%)" />
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
        <button :disabled="employee.processing" @click="save" class="px-4 py-2 bg-primary hover:bg-primaryhover text-white rounded">
          Guardar
        </button>
      </div>
    </div>
  </AdminLayout>
</template>
