<script setup>
// Imports
import { reactive, computed, watch } from "vue";
import AdminLayout from "@/Layouts/AdminLayout.vue";
import { useForm } from "@inertiajs/vue3";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import DynamicSelect from "@/Components/DynamicSelect.vue";
import Checkbox from "@/Components/Checkbox.vue";
import { useFocusNextField } from "@/composables/useFocusNextField";

// Props
const props = defineProps({
  fixedAsset: { type: Object, default: () => {} },
  payMethods: { type: Array, default: () => [] },
  activeTypes: { type: Array, default: () => [] },
});

const { focusNextField } = useFocusNextField();

// Reactives
const fixedAsset = useForm({ ...props.fixedAsset });
const errorForm = reactive({});

const save = () => {
  // Enviar el objeto completo 'fixedAsset' con el PUT
  fixedAsset.put(route("fixedassets.update", fixedAsset.id), {
    onError: (errors) => {
      // Mostrar errores de validación desde el backend
      Object.keys(errors).forEach((key) => {
        errorForm[key] = errors[key];
      });
    },
  });
};

// Cálculo de la fecha de finalización
const calculofecha = computed(() => {
  if (!fixedAsset.date_acquisition || fixedAsset.period === "") {
    return "";
  }

  const acquisitionDate = new Date(fixedAsset.date_acquisition);
  const periodYears = parseInt(fixedAsset.period, 10);

  if (periodYears === 0) {
    return acquisitionDate.toISOString().split("T")[0];
  }

  const endDate = new Date(
    acquisitionDate.getFullYear() + periodYears,
    acquisitionDate.getMonth(),
    acquisitionDate.getDate()
  );

  return endDate.toISOString().split("T")[0];
});

// Actualiza la fecha final de depreciación cada vez que el cálculo cambia
watch(calculofecha, (newDate) => {
  fixedAsset.date_end = newDate;
});

// Resto de las configuraciones
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
  () => fixedAsset.type_id,
  (newTypeId) => {
    const selectedType = props.activeTypes.find(
      (type) => String(type.id) === String(newTypeId)
    );

    if (selectedType) {
      fixedAsset.period = selectedType.depresiation_time ?? "";
    } else {
      fixedAsset.period = "";
    }
  }
);
</script>

<template>
  <AdminLayout title="Activos Fijos">
    <div class="p-4 bg-white rounded drop-shadow-md">
      <!-- Card Header -->
      <div class="flex justify-between items-center">
        <h2 class="text-sm sm:text-lg font-bold">Activo Fijo</h2>
      </div>

      <!-- Formulario -->
      <form @submit.prevent="save" @keydown.enter.prevent="focusNextField">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <!-- Primera columna -->
          <div class="grid grid-cols-1 gap-4">
            <div class="col-span-6 sm:col-span-4">
              <Checkbox
                v-model:checked="fixedAsset.is_depretation_a"
                label="¿Posee depreciación acelerada?"
              />
            </div>
            <div class="col-span-6 sm:col-span-4">
              <Checkbox
                v-model:checked="fixedAsset.is_legal"
                label="¿Tiene sustento legal de compra?"
              />
            </div>

            <div
              :hidden="fixedAsset.is_legal !== true"
              class="col-span-6 sm:col-span-4"
            >
              <InputLabel for="is_legal" value="Número de documento" />
              <TextInput
                v-model="fixedAsset.vaucher"
                type="text"
                class="mt-1 block w-full"
                placeholder="001-001-098765432"
                max="17"
              />
              <InputError :message="errorForm.vaucher" class="mt-2" />
            </div>

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

            <div class="col-span-6 sm:col-span-4">
              <InputLabel for="detail" value="Detalle" />
              <TextInput
                v-model="fixedAsset.detail"
                type="text"
                class="mt-1 block w-full"
                max="17"
                required
              />
              <InputError :message="errorForm.detail" class="mt-2" />
            </div>

            <div class="col-span-6 sm:col-span-4">
              <InputLabel for="code" value="Código" />
              <TextInput
                v-model="fixedAsset.code"
                type="text"
                class="mt-1 block w-full"
                required
              />
              <InputError :message="errorForm.code" class="mt-2" />
            </div>

            <div class="col-span-6 sm:col-span-4">
              <InputLabel for="active_type" value="Tipo de activo fijo" />
              <DynamicSelect
                class="mt-1 block w-full"
                v-model="fixedAsset.type_id"
                :options="activeTypeOptions"
                autofocus
                required
              />
              <InputError :message="errorForm.activeType" class="mt-2" />
            </div>
          </div>

          <!-- Segunda columna -->
          <div class="grid grid-cols-1 gap-4">
            <div class="col-span-6 sm:col-span-4">
              <InputLabel for="adress" value="Dirección del activo fijo" />
              <TextInput
                v-model="fixedAsset.address"
                type="text"
                class="mt-1 block w-full"
                required
              />
              <InputError :message="errorForm.address" class="mt-2" />
            </div>

            <div class="col-span-6 sm:col-span-4">
              <InputLabel for="period" value="Periodo de depreciación" />
              <TextInput
                v-model="fixedAsset.period"
                type="number"
                class="mt-1 block w-full"
                disabled
                required
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
              />
              <InputError :message="errorForm.residual_value" class="mt-2" />
            </div>

            <div class="col-span-6 sm:col-span-4">
              <InputLabel for="date_end" value="Fecha final de depreciación" />
              <TextInput
                :value="fixedAsset.date_end"
                v-model="fixedAsset.date_end"
                type="date"
                class="mt-1 block w-full"
                disabled
                required
              />
              <InputError :message="errorForm.date_end" class="mt-2" />
            </div>

            <div class="col-span-6 sm:col-span-4">
              <InputLabel for="pay_method" value="Metodo de pago" />
              <DynamicSelect
                class="mt-1 block w-full"
                v-model="fixedAsset.pay_method_id"
                :options="payMethodOptions"
              />
              <InputError :message="errorForm.pay_method_id" class="mt-2" />
            </div>
          </div>
        </div>

        <div class="flex justify-end items-center space-x-3 mt-4">
          <button
            type="submit"
            class="bg-blue-600 text-white px-4 py-2 rounded-lg"
            :disabled="fixedAsset.processing"
          >
            Guardar
          </button>
        </div>
      </form>
    </div>
  </AdminLayout>
</template>
