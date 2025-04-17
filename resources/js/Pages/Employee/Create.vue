<script setup lang="ts">
// Imports
import AdminLayout from "@/Layouts/AdminLayout.vue";
import { useForm,router } from "@inertiajs/vue3";
import { TextInput, Checkbox, InputLabel, InputError, SecondaryButton } from "@/Components";
import { Employee, Errors } from "@/types";
import { reactive } from "vue";

const date = new Date().toISOString().split("T")[0];

// Inicializador de objetos
const initialEmployee: Employee = {
  id: undefined,
  cuit: "",
  name: "",
  sector_code: "",
  position: "",
  days: 0,
  salary: 0,
  porcent_aportation: 0,
  is_a_parnert: false,
  is_a_qualified_craftsman: false,
  affiliated_with_spouse: false,
  date_start: date,
  xiii: false,
  xiv: false,
  reserve_funds: false,
  email: "",
  processing: false,
};

// Reactives
const employee = useForm<Employee>({ ...initialEmployee });
const errorForm = reactive<Errors>({});

// Método de guardar con mensajes
const save = () => {
  // Enviar datos al backend
  employee.post(route("employee.store"), {
    onError: (error: Errors) => {
      if (error) {
        // Iterar sobre los errores recibidos del servidor
        Object.entries(error).forEach(([key, value]) => {
          errorForm[key] = value;// Mostrar el primer error asociado a cada campo
        });
      } else {
        console.error("Error desconocido:", error);
        alert("Ocurrió un error inesperado. Por favor, intente nuevamente.");
      }
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
        <h2 class="text-sm sm:text-lg font-bold">Registrar empleado</h2>
      </div>

      <!-- Formulario -->
      <div class="mt-4">
        <!--fila 1-->
        <div class="sm:flex gap-4 mt-4">
          <div class="w-full">
            <InputLabel for="cuit" value="Cédula" />
            <TextInput v-model="employee.cuit" type="text" class="mt-1 w-full" minlegth="10" maxlength="13" required />
            <InputError :message="errorForm.cuit" class="mt-2" />
          </div>

          <div class="w-full mt-4 sm:mt-0">
            <InputLabel for="name" value="Nombre y apellido" />
            <TextInput v-model="employee.name" type="text" class="mt-1 w-full" minlength="3" maxlength="300" required />
            <InputError :message="errorForm.name" class="mt-2" />
          </div>
        </div>

        <!--fila 2-->
        <div class="sm:flex gap-4 mt-4">
          <div class="w-full">
            <InputLabel for="city" value="Código sectorial" />
            <TextInput v-model="employee.sector_code" type="text" class="mt-1 w-full" minlength="3" maxlength="300" />
            <InputError :message="errorForm.sector_code" class="mt-2" />
          </div>
          <div class="w-full mt-4 sm:mt-0">
            <InputLabel for="position" value="Cargo" />
            <TextInput v-model="employee.position" type="text" class="mt-1 w-full" minlength="3" maxlength="300"
              required />
            <InputError :message="errorForm.position" class="mt-2" />
          </div>
        </div>

        <!--fila 3-->
        <div class="sm:flex gap-4 mt-4">
          <div class="w-full">
            <InputLabel for="days" value="Días" />
            <TextInput v-model="employee.days" type="number" class="mt-1 w-full" min="0" required />
            <InputError :message="errorForm.days" class="mt-2" />
          </div>

          <div class="w-full mt-4 sm:mt-0">
            <InputLabel for="salary" value="Salario" />
            <TextInput v-model="employee.salary" type="number" class="mt-1 w-full" min="0" required />
            <InputError :message="errorForm.salary" class="mt-2" />
          </div>
        </div>

        <!--fila 4-->
        <div class="sm:flex gap-4 mt-4">
          <div class="w-full">
            <InputLabel for="porcent_aportation" value="Aportación (%)" />
            <TextInput v-model="employee.porcent_aportation" type="number" class="mt-1 w-full" min="0" />
            <InputError :message="errorForm.porcent_aportation" class="mt-2" />
          </div>

          <div class="w-full mt-4 sm:mt-0">
            <InputLabel for="date_start" value="Fecha de inicio" />
            <TextInput v-model="employee.date_start" type="date" class="mt-1 w-full" required />
            <InputError :message="errorForm.date_start" class="mt-2" />
          </div>
        </div>

        <!--fila 5-->
        <div class="sm:flex gap-4 mt-4">
          <div class="w-full">
            <InputLabel for="email" value="Correo" />
            <TextInput v-model="employee.email" type="email" class="mt-1 w-full" minlength="3" maxlength="300"
              required />
            <InputError :message="errorForm.email" class="mt-2" />
          </div>

          <div class="w-full mt-4 sm:mt-0">
            <Checkbox v-model:checked="employee.is_a_parnert" class="mt-8 w-full" label="Es socio o propietario?" />
          </div>
        </div>

        <!--fila 6-->
        <div class="sm:flex gap-4 mt-4">
          <div class="w-full">
            <Checkbox class="mt-4 w-full" v-model:checked="employee.is_a_qualified_craftsman"
              label="Es artesano calificado?" />
          </div>

          <div class="w-full mt-4 sm:mt-0">
            <Checkbox class="mt-4 w-full" v-model:checked="employee.affiliated_with_spouse"
              label="Afiliación al cónyuge?" />
          </div>
        </div>
        <!--fila 6-->
        <div class="sm:flex gap-4 mt-4">
          <div class="w-full">
            <Checkbox class="mt-4 w-full" v-model:checked="employee.xiii" label="Acumulación décimo tercero?" />
          </div>

          <div class="w-full">
            <Checkbox class="mt-4 w-full" v-model:checked="employee.xiv" label="Acumulación décimo cuarto?" />
          </div>
        </div>

        <div class="col-span-6 sm:col-span-4 mt-4 sm:mt-0">
          <Checkbox class="mt-4 sm:mt-8 w-full" v-model:checked="employee.reserve_funds"
            label="Acumulación fondos de reserva?" />
        </div>
      </div>


      <div class="mt-4 text-right">
        <SecondaryButton @click="() => router.visit(route('employee.index'))" class="mr-2">
          Cancelar
        </SecondaryButton>
        <button :disabled="employee.processing" @click="save"
          class="px-4 py-2 bg-primary hover:bg-primaryhover text-white rounded">
          Guardar
        </button>
      </div>
    </div>
  </AdminLayout>
</template>
