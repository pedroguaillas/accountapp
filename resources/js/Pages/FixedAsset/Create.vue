<script setup>
// Imports
import { reactive } from "vue";
import AdminLayout from "@/Layouts/AdminLayout.vue";
import { router, useForm } from "@inertiajs/vue3";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/InputError.vue";

// Props
const props = defineProps({
  payMethods: { type: Array, default: () => [] },
});

const date = new Date().toISOString().split("T")[0];

// Inicializador de objetos
const initialFixedAsset = {
  pay_method_id: 0,
  is_depretation_a: false,
  is_legal: false,
  vaucher: "",
  date_acquisition: "",
  detail: "",
  code: "",
  type: "",
  address: "",
  period: 0,
  value: 0,
  residual_value: 0,
  date_end: "",
};

// Reactives
const fixedAsset = useForm({ ...initialFixedAsset });
const errorForm = reactive({ ...initialFixedAsset, date: "", date_end: "" });

const resetErrorForm = () => {
  Object.assign(errorForm, initialFixedAsset);
  console.log("Formulario de errores reiniciado.");
};

// Método de guardar con mensajes
const save = () => {
  console.log("Método 'save' iniciado.");

  // Validar datos requeridos en el frontend
  if (!fixedAsset.pay_method_id) {
    console.error("Método de pago no seleccionado.");
    errorForm.pay_method_id = "Seleccione un método de pago.";
    return;
  }

  if (!fixedAsset.address) {
    console.error("La dirección es obligatoria.");
    errorForm.address = "La dirección es obligatoria.";
    return;
  }

  if (!fixedAsset.date_acquisition) {
    console.error("La fecha de adquisición es obligatoria.");
    errorForm.date_acquisition = "La fecha de adquisición es obligatoria.";
    return;
  }

  console.log("Validaciones frontend completadas, enviando al servidor...");

  // Enviar datos al backend
  fixedAsset.post(route("fixedassets.store"), {
    onError: (errors) => {
      console.error("Errores recibidos del servidor:", errors);
      // Mostrar errores de validación desde el backend
      Object.keys(errors).forEach((key) => {
        errorForm[key] = errors[key];
      });
    },
    onSuccess: () => {
      console.log("Activo fijo guardado exitosamente.");
      // Reiniciar el formulario tras éxito
      fixedAsset.reset();
      resetErrorForm();
    },
  });
};
</script>

<template>
  <AdminLayout title="Activos Fijos">
    <!-- Card -->
    <div class="p-4 bg-white rounded drop-shadow-md">
      <!-- Card Header -->
      <div class="flex justify-between items-center">
        <h2 class="text-sm sm:text-lg font-bold">Activo Fijo</h2>
      </div>

      <!-- Formulario -->
      <div>
        <label class="mt-6 block">
          <input
            type="checkbox"
            v-model="fixedAsset.is_depretation_a"
            class="mr-2"
          />
          ¿Posee depreciacion acelerada?
        </label>
        <br />

        <label class="mt-6 block">
          <input type="checkbox" v-model="fixedAsset.is_legal" class="mr-2" />
          ¿Tiene sustento legal de compra?
        </label>
        <br />
        <TextInput
          v-model="fixedAsset.vaucher"
          type="text"
          placeholder="001-001-000000009"
          class="mt-2 block w-full"
          max="17"
        />
        <InputError :message="errorForm.vaucher" class="mt-2" />

        <!-- Fecha de adqusicion -->
        <TextInput
          v-model="fixedAsset.date_acquisition"
          type="date"
          class="mt-2 block w-full"
        />
        <InputError :message="errorForm.date_acquisition" class="mt-2" />

        <TextInput
          v-model="fixedAsset.detail"
          type="text"
          placeholder="Descripcion"
          class="mt-2 block w-full"
          max="17"
        />
        <InputError :message="errorForm.detail" class="mt-2" />

        <TextInput
          v-model="fixedAsset.code"
          type="text"
          placeholder="CMC23"
          class="mt-2 block w-full"
        />
        <InputError :message="errorForm.code" class="mt-2" />

        <TextInput
          v-model="fixedAsset.type"
          type="text"
          placeholder="Vehiculos"
          class="mt-2 block w-full"
        />
        <InputError :message="errorForm.type" class="mt-2" />

        <TextInput
          v-model="fixedAsset.address"
          type="text"
          placeholder="Lugar donde se encuentra el AF"
          class="mt-2 block w-full"
        />
        <InputError :message="errorForm.address" class="mt-2" />

        <TextInput
          v-model="fixedAsset.period"
          type="number"
          placeholder="5"
          class="mt-2 block w-full"
        />
        <InputError :message="errorForm.detail" class="mt-2" />

        <TextInput
          v-model="fixedAsset.value"
          type="number"
          placeholder="0,00"
          class="mt-2 block w-full"
          min="0"
        />
        <InputError :message="errorForm.value" class="mt-2" />

        <TextInput
          v-model="fixedAsset.residual_value"
          type="number"
          placeholder="0,00"
          class="mt-2 block w-full"
          min="0"
        />
        <InputError :message="errorForm.residual_value" class="mt-2" />

        <!-- Fecha de finalizacion de depreciacion-->
        <TextInput
          v-model="fixedAsset.date_end"
          type="date"
          class="mt-2 block w-full"
        />
        <InputError :message="errorForm.date_end" class="mt-2" />

        <select
          v-model="fixedAsset.pay_method_id"
          class="mt-2 block w-full rounded"
        >
          <option value="">Seleccione</option>
          <option
            v-for="payMethod in payMethods"
            :key="payMethod.id"
            :value="payMethod.id"
          >
            {{ payMethod.name }}
          </option>
        </select>
        <InputError :message="errorForm.pay_method_id" class="mt-2" />

        <div class="mt-4 text-right">
          <button
            @click="save"
            class="px-4 py-2 bg-blue-500 text-white rounded"
          >
            Guardar
          </button>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>
