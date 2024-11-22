<script setup>
// Imports
import { reactive, computed } from "vue";
import AdminLayout from "@/Layouts/AdminLayout.vue";
import { useForm } from "@inertiajs/vue3";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import { watch } from "vue";

// Props
const props = defineProps({
  payMethods: { type: Array, default: () => [] },
  activeTypes: { type: Array, default: () => [] },
});

const date = new Date().toISOString().split("T")[0];

// Inicializador de objetos
const initialFixedAsset = {
  pay_method_id: "",
  is_depretation_a: false,
  is_legal: false,
  vaucher: "",
  date_acquisition: date,
  detail: "",
  code: "",
  type_id: "",
  address: "",
  period: "",
  value: "",
  residual_value: "",
  date_end: "",
};

// Reactives
const fixedAsset = useForm({ ...initialFixedAsset });
const errorForm = reactive({ ...initialFixedAsset, date: "", date_end: "" });

const resetErrorForm = () => {
  Object.assign(errorForm, initialFixedAsset);
};

// Método de guardar con mensajes
const save = () => {
  // Validar datos requeridos en el frontend
  if (!fixedAsset.pay_method_id) {
    errorForm.pay_method_id = "Seleccione un método de pago.";
    return;
  }

  if (!fixedAsset.type_id) {
    errorForm.type_id = "Seleccione un tipo de activo.";
    return;
  }

  if (!fixedAsset.address) {
    errorForm.address = "La dirección es obligatoria.";
    return;
  }

  if (!fixedAsset.date_acquisition) {
    errorForm.date_acquisition = "La fecha de adquisición es obligatoria.";
    return;
  }

  // Enviar datos al backend
  fixedAsset.post(route("fixedassets.store"), {
    onError: (errors) => {
      // Mostrar errores de validación desde el backend
      Object.keys(errors).forEach((key) => {
        errorForm[key] = errors[key];
      });
    },
    onSuccess: () => {
      // Reiniciar el formulario tras éxito
      fixedAsset.reset();
      resetErrorForm();
    },
  });
};

const calculofecha = computed(() => {
  // Si no hay fecha de adquisición o periodo, no calculamos la fecha final
  if (!fixedAsset.date_acquisition || fixedAsset.period === "") {
    return "";
  }

  // Convertir la fecha de adquisición a un objeto Date
  const acquisitionDate = new Date(fixedAsset.date_acquisition);

  // Asegurarse de que el periodo sea un número entero
  const periodYears = parseInt(fixedAsset.period, 10);

  // Si el periodo es 0, la fecha final será la misma que la fecha de adquisición
  if (periodYears === 0) {
    fixedAsset.date_end = acquisitionDate.toISOString().split("T")[0];
    return fixedAsset.date_end;
  }

  // Si el periodo es mayor a 0, calculamos la fecha final sumando los años al año de adquisición
  const endDate = new Date(
    acquisitionDate.getFullYear() + periodYears,
    acquisitionDate.getMonth(),
    acquisitionDate.getDate()
  );

  // Asignamos la fecha de finalización calculada
  fixedAsset.date_end = endDate.toISOString().split("T")[0];

  return fixedAsset.date_end;
});

watch(
  () => fixedAsset.type_id,
  (newTypeId) => {
    // Encuentra el tipo activo seleccionado
    const selectedType = props.activeTypes.find(
      (type) => type.id === newTypeId
    );

    // Si se encuentra, asigna el tiempo de depreciación al período
    if (selectedType) {
      fixedAsset.period =
        selectedType.depresiation_time !== undefined
          ? selectedType.depresiation_time
          : ""; // Asigna el valor, incluso si es 0
    } else {
      // Si no hay tipo seleccionado, limpia el período
      fixedAsset.period = "";
    }
  }
);
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

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- Primera columna -->
        <div class="grid grid-cols-1 gap-4">
          <!--  primera columna dentro de la columna primera-->
          <div class="col-span-6 sm:col-span-4">
            <label class="mt-6 block">
              <input
                type="checkbox"
                v-model="fixedAsset.is_depretation_a"
                class="mr-2"
              />
              ¿Posee depreciación acelerada?
            </label>
          </div>

          <!--  segunda columna dentro de la columna primera-->
          <div class="col-span-6 sm:col-span-4">
            <label class="mt-6 block">
              <input
                type="checkbox"
                v-model="fixedAsset.is_legal"
                class="mr-2"
              />
              ¿Tiene sustento legal de compra?
            </label>
          </div>

          <!--  tercera columna dentro de la columna primera-->
          <div
            :hidden="fixedAsset.is_legal !== true"
            class="col-span-6 sm:col-span-4"
          >
            <InputLabel for="is_legal" value="Numero de documento" />
            <TextInput
              v-model="fixedAsset.vaucher"
              type="text"
              class="mt-2 block w-full"
              max="17"
            />
            <InputError :message="errorForm.vaucher" class="mt-2" />
          </div>

          <!--  cuarto columna dentro de la columna primera-->
          <div class="col-span-6 sm:col-span-4">
            <InputLabel for="date_acquisition" value="Fecha de adquisicion" />
            <TextInput
              v-model="fixedAsset.date_acquisition"
              type="date"
              class="mt-2 block w-full"
            />
          </div>

          <!--  quinta columna dentro de la columna primera-->
          <div class="col-span-6 sm:col-span-4">
            <InputLabel for="detail" value="Detalle" />
            <TextInput
              v-model="fixedAsset.detail"
              type="text"
              class="mt-1 block w-full"
              max="17"
            />
            <InputError :message="errorForm.detail" class="mt-2" />
          </div>

          <!--  sexta columna dentro de la columna primera-->
          <div class="col-span-6 sm:col-span-4">
            <InputLabel for="code" value="Codigo" />
            <TextInput
              v-model="fixedAsset.code"
              type="text"
              class="mt-1 block w-full"
            />
            <InputError :message="errorForm.code" class="mt-2" />
          </div>

          <div class="col-span-6 sm:col-span-4">
            <InputLabel for="active_type" value="Tipo de activo fijo" />
            <select
              v-model="fixedAsset.type_id"
              class="mt-2 block w-full rounded"
            >
              <option value="" selected>Seleccione</option>
              <option
                v-for="activeType in activeTypes"
                :key="activeType.id"
                :value="activeType.id"
              >
                {{ activeType.name }}
              </option>
            </select>
            <InputError :message="errorForm.activeType" class="mt-2" />
          </div>
        </div>

        <!-- Segunda columna  de la principal-->

        <div class="grid grid-cols-1 gap-4">
          <!-- primera columna de la segunda columna -->

          <div class="col-span-6 sm:col-span-4">
            <InputLabel for="adress" value="Direccion del activo Fijo" />
            <TextInput
              v-model="fixedAsset.address"
              type="text"
              class="mt-2 block w-full"
            />
            <InputError :message="errorForm.address" class="mt-2" />
          </div>

          <div class="col-span-6 sm:col-span-4">
            <InputLabel for="period" value="Periodo de depresiacion" />
            <TextInput
              v-model="fixedAsset.period"
              type="number"
              class="mt-2 block w-full"
            />
            <InputError :message="errorForm.period" class="mt-2" />
          </div>

          <div class="col-span-6 sm:col-span-4">
            <InputLabel for="value" value="Valor del activo fijo" />
            <TextInput
              v-model="fixedAsset.value"
              type="number"
              class="mt-2 block w-full"
              min="0"
            />
            <InputError :message="errorForm.value" class="mt-2" />
          </div>

          <div class="col-span-6 sm:col-span-4">
            <InputLabel for="residual_value" value="Valor residual" />
            <TextInput
              v-model="fixedAsset.residual_value"
              type="number"
              class="mt-2 block w-full"
              min="0"
              :max="fixedAsset.value"
            />
            <InputError :message="errorForm.residual_value" class="mt-2" />
          </div>

          <div class="col-span-6 sm:col-span-4">
            <InputLabel for="date_end" value="Fecha final de depreciación" />
            <TextInput
              :value="calculofecha"
              v-model="fixedAsset.date_end"
              type="date"
              class="mt-2 block w-full"
              disabled
            />
          </div>

          <div class="col-span-6 sm:col-span-4">
            <InputLabel for="pay_method_id" value="Metodo de pago" />
            <select
              v-model="fixedAsset.pay_method_id"
              class="mt-2 block w-full rounded"
            >
              <option value="" selected>Seleccione</option>
              <option
                v-for="payMethod in payMethods"
                :key="payMethod.id"
                :value="payMethod.id"
              >
                {{ payMethod.name }}
              </option>
            </select>
            <InputError :message="errorForm.pay_method_id" class="mt-2" />
          </div>
        </div>
      </div>
      <div class="mt-4 text-right">
        <button @click="save" class="px-4 py-2 bg-blue-500 text-white rounded">
          Guardar
        </button>
      </div>
    </div>
  </AdminLayout>
</template>
