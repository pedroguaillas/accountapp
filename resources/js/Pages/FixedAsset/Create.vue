<script setup>
// Imports
import { reactive, computed, watch, watchEffect } from "vue";
import AdminLayout from "@/Layouts/AdminLayout.vue";
import { useForm } from "@inertiajs/vue3";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import DynamicSelect from "@/Components/DynamicSelect.vue";
import Checkbox from "@/Components/Checkbox.vue";

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
  active_type_id: "",
  address: "",
  period: "",
  value: "",
  residual_value: "",
  date_end: "",
};

// Reactives
const fixedAsset = useForm({ ...initialFixedAsset });
const errorForm = reactive({});

const resetErrorForm = () => {
  Object.assign(errorForm, initialFixedAsset);
};

// Método de guardar datos
const submit = () => {
  fixedAsset.post(route("fixedassets.store"), {
    onError: (errors) => {
      Object.keys(errors).forEach((key) => {
        errorForm[key] = errors[key];
      });
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

const activeTypeOptions = Array.isArray(props.activeTypes)
  ? props.activeTypes.map((activeType) => ({
      value: activeType.id,
      label: activeType.name,
    }))
  : [];

const payMethodOptions = Array.isArray(props.payMethods)
  ? props.payMethods.map((payMethod) => ({
      value: payMethod.id,
      label: payMethod.name,
    }))
  : [];

watch(
  () => fixedAsset.active_type_id,
  (newTypeId) => {
    if (!Array.isArray(props.activeTypes)) return;

    const selectedType = props.activeTypes.find(
      (type) => String(type.id) === String(newTypeId)
    );

    fixedAsset.period = selectedType?.depreciation_time ?? "";
    if (fixedAsset.period === 0) {
      fixedAsset.residual_value = 0;
    }
  }
);

// Watch para actualizar los años cuando cambia el periodo en meses
watch(
  () => fixedAsset.periodMonths,
  (newMonths) => {
    if (newMonths >= 0) {
      fixedAsset.period = Math.floor(newMonths / 12); // Convertimos meses a años
    }
  }
);

watch(
  [() => fixedAsset.period, () => fixedAsset.periodMonths],
  ([newYears, newMonths]) => {
    if (newYears !== undefined) {
      fixedAsset.periodMonths = newYears * 12;
    } else if (newMonths !== undefined) {
      fixedAsset.period = Math.floor(newMonths / 12);
    }
  }
);
</script>

<template>
  <AdminLayout title="Registro activo fijo">
    <!-- Card -->
    <div class="p-4 bg-white rounded drop-shadow-md">
      <!-- Card Header -->
      <div class="flex justify-between items-center">
        <h2 class="text-sm sm:text-lg font-bold">Registro activo fijo</h2>
      </div>

      <!-- Formulario -->
      <form @submit.prevent="submit">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <!-- Primera columna -->
          <div class="grid grid-cols-1 gap-4">
            <!--  primera columna dentro de la columna primera-->
            <div class="col-span-6 sm:col-span-4">
              <Checkbox
                v-model:checked="fixedAsset.is_depretation_a"
                label="¿Posee depreciación acelerada?"
              />
            </div>

            <!--  segunda columna dentro de la columna primera-->
            <div class="col-span-6 sm:col-span-4">
              <Checkbox
                v-model:checked="fixedAsset.is_legal"
                label="¿Tiene sustento legal de compra?"
              />
            </div>

            <!--  tercera columna dentro de la columna primera-->
            <div
              :hidden="!fixedAsset.is_legal"
              class="col-span-6 sm:col-span-4"
            >
              <InputLabel for="is_legal" value="Número de documento" />
              <TextInput
                v-model="fixedAsset.vaucher"
                type="text"
                class="mt-1 block w-full"
                placeholder="001-001-098765432"
                minlength="17"
                maxlength="17"
              />
              <InputError :message="errorForm.vaucher" class="mt-2" />
            </div>

            <!--  cuarto columna dentro de la columna primera-->
            <div class="col-span-6 sm:col-span-4">
              <InputLabel for="date_acquisition" value="Fecha de adquisición" />
              <TextInput
                v-model="fixedAsset.date_acquisition"
                type="date"
                class="mt-1 block w-full"
                required
              />
              <InputError :message="errorForm.date_acquisition" class="mt-2" />
            </div>

            <!--  quinta columna dentro de la columna primera-->
            <div class="col-span-6 sm:col-span-4">
              <InputLabel for="detail" value="Detalle" />
              <TextInput
                v-model="fixedAsset.detail"
                type="text"
                class="mt-1 block w-full"
                maxlength="300"
                required
              />
              <InputError :message="errorForm.detail" class="mt-2" />
            </div>

            <!--  sexta columna dentro de la columna primera-->
            <div class="col-span-6 sm:col-span-4">
              <InputLabel for="code" value="Código" />
              <TextInput
                v-model="fixedAsset.code"
                type="text"
                class="mt-1 block w-full"
                maxlength="50"
                required
              />
              <InputError :message="errorForm.code" class="mt-2" />
            </div>

            <div class="col-span-6 sm:col-span-4">
              <InputLabel for="active_type_id" value="Tipo de activo fijo" />
              <DynamicSelect
                class="mt-1 block w-full"
                v-model="fixedAsset.active_type_id"
                :options="activeTypeOptions"
                autofocus
                :required="true"
              />
              <InputError :message="errorForm.activeType" class="mt-2" />
            </div>
          </div>

          <!-- Segunda columna  de la principal-->
          <div class="grid grid-cols-1 gap-4">
            <!-- primera columna de la segunda columna -->
            <div class="col-span-6 sm:col-span-4">
              <InputLabel for="adress" value="Dirección del activo fijo" />
              <TextInput
                v-model="fixedAsset.address"
                type="text"
                class="mt-1 block w-full"
                maxlength="300"
                required
              />
              <InputError :message="errorForm.address" class="mt-2" />
            </div>

            <!-- Campo Periodo de Depreciación (Años) -->
            <div class="col-span-6 sm:col-span-4">
              <InputLabel for="period" value="Periodo de depreciación (años)" />
              <TextInput
                v-model="fixedAsset.period"
                type="number"
                class="mt-1 block w-full"
                :disabled="!fixedAsset.is_depretation_a"
              />
            </div>

            <!-- Campo Periodo de Depreciación (Meses) -->
            <div class="col-span-6 sm:col-span-4">
              <InputLabel
                for="period-months"
                value="Periodo de depreciación (meses)"
              />
              <TextInput
                v-model="fixedAsset.periodMonths"
                type="number"
                class="mt-1 block w-full"
                :disabled="!fixedAsset.is_depretation_a"
              />
              <InputError :message="errorForm.period" class="mt-2" />
            </div>

            <div class="col-span-6 sm:col-span-4">
              <InputLabel for="value" value="Valor del activo fijo" />
              <TextInput
                v-model="fixedAsset.value"
                type="number"
                class="mt-1 block w-full"
                min="0"
                required
              />
              <InputError :message="errorForm.value" class="mt-2" />
            </div>

            <div class="col-span-6 sm:col-span-4">
              <InputLabel for="residual_value" value="Valor residual" />
              <TextInput
                v-model="fixedAsset.residual_value"
                type="number"
                class="mt-1 block w-full"
                min="0"
                :max="fixedAsset.value"
                :disabled="parseInt(fixedAsset.period, 10) === 0"
              />
              <InputError :message="errorForm.residual_value" class="mt-2" />
            </div>

            <div class="col-span-6 sm:col-span-4">
              <InputLabel for="date_end" value="Fecha final de depreciación" />
              <TextInput
                :value="calculofecha"
                v-model="fixedAsset.date_end"
                type="date"
                class="mt-1 block w-full"
                disabled
              />
              <InputError :message="errorForm.date_end" class="mt-2" />
            </div>

            <div class="col-span-6 sm:col-span-4">
              <InputLabel for="pay_method_id" value="Método de pago" />
              <DynamicSelect
                class="mt-1 block w-full"
                v-model="fixedAsset.pay_method_id"
                :options="payMethodOptions"
                autofocus
                :required="true"
              />
              <InputError :message="errorForm.pay_method_id" class="mt-2" />
            </div>
          </div>
        </div>
        <div class="mt-4 text-right">
          <button
            class="px-4 py-2 bg-blue-500 text-white rounded"
            :disabled="fixedAsset.processing"
          >
            Guardar
          </button>
        </div>
      </form>
    </div>
  </AdminLayout>
</template>
