<script setup lang="ts">
// Imports
import { reactive, computed, watch } from "vue";
import AdminLayout from "@/Layouts/AdminLayout.vue";
import { useForm } from "@inertiajs/vue3";
import { TextInput, Checkbox, InputLabel, InputError, DynamicSelect } from "@/Components";
import { useFocusNextField } from "@/composables/useFocusNextField";
import { ActiveType, Errors, FixedAsset, PayMethod } from "@/types";

// Props
const props = defineProps<{
  fixedAsset: FixedAsset;
  payMethods: PayMethod[];
  activeTypes: ActiveType[];
}>();

const { focusNextField } = useFocusNextField();

// Reactives
const fixedAsset = useForm<FixedAsset>({ ...props.fixedAsset });
const errorForm: Record<string, string> = {};

const save = () => {
  // Enviar el objeto completo 'fixedAsset' con el PUT
  fixedAsset.put(route("fixedassets.update", fixedAsset.id), {
    onError: (errors: Errors) => {
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
  const fixedAssetAux = String(fixedAsset.period);
  const periodYears = parseInt(fixedAssetAux, 10);

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
    value: activeType.id !== undefined ? activeType.id : 0,
    label: activeType.name,
  }))
  : [];

const payMethodOptions = Array.isArray(props.payMethods)
  ? props.payMethods.map((payMethod) => ({
    value: payMethod.id !== undefined ? payMethod.id : 0,
    label: payMethod.name,
  }))
  : [];

watch(
  () => fixedAsset.active_type_id,
  (newTypeId) => {
    const selectedType = props.activeTypes.find(
      (type) => String(type.id) === String(newTypeId)
    );

    if (selectedType) {
      fixedAsset.period = selectedType.depreciation_time ?? "";
    } else {
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
      <form @submit.prevent="save" @keydown.enter.prevent="focusNextField">
        <div class="mt-4">
          <!--fila 1-->
          <div class="sm:flex gap-4 mt-4">
            <div class="w-full">
              <Checkbox v-model:checked="fixedAsset.is_depretation_a" label="¿Posee depreciación acelerada?" />
            </div>

            <div class="w-full mt-4 sm:mt-0">
              <Checkbox v-model:checked="fixedAsset.is_legal" label="¿Tiene sustento legal de compra?" />
            </div>
          </div>

          <!--fila 1-->
          <div class="sm:flex gap-4 mt-4">
            <div class="w-full sm:w-[50%]" :class="!fixedAsset.is_legal ? 'sm:pr-2' : ''">
              <InputLabel for="date_acquisition" value="Fecha de adquisición" />
              <TextInput v-model="fixedAsset.date_acquisition" type="date" class="mt-1 w-full" required />
              <InputError :message="errorForm.date_acquisition" class="mt-2" />
            </div>
            <div :hidden="fixedAsset.is_legal !== true" class="w-full sm:w-[50%] mt-4 sm:mt-0">
              <InputLabel for="is_legal" value="Número de documento" />
              <TextInput v-model="fixedAsset.vaucher" type="text" class="mt-1 w-full" placeholder="001-001-098765432"
                maxlength="17" />
              <InputError :message="errorForm.vaucher" class="mt-2" />
            </div>
          </div>

          <!--fila 1-->
          <div class="sm:flex gap-4 mt-4">
            <div class="w-full">
              <InputLabel for="detail" value="Detalle" />
              <TextInput v-model="fixedAsset.detail" type="text" class="mt-1 w-full" maxlength="300" required />
              <InputError :message="errorForm.detail" class="mt-2" />
            </div>

            <div class="w-full mt-4 sm:mt-0">
              <InputLabel for="code" value="Código" />
              <TextInput v-model="fixedAsset.code" type="text" class="mt-1 w-full" maxlength="50" required />
              <InputError :message="errorForm.code" class="mt-2" />
            </div>
          </div>

          <!--fila 1-->
          <div class="sm:flex gap-4 mt-4">
            <div class="w-full">
              <InputLabel for="active_type_id" value="Tipo de activo fijo" />
              <DynamicSelect class="mt-1 w-full" v-model="fixedAsset.active_type_id" :options="activeTypeOptions"
                :seleccione="false" autofocus required />
              <InputError :message="errorForm.activeType" class="mt-2" />
            </div>

            <div class="w-full mt-4 sm:mt-0">
              <InputLabel for="adress" value="Dirección del activo fijo" />
              <TextInput v-model="fixedAsset.address" type="text" class="mt-1 w-full" maxlength="300" required />
              <InputError :message="errorForm.address" class="mt-2" />
            </div>
          </div>

          <!--fila 1-->
          <div class="sm:flex gap-4 mt-4">
            <div class="w-full">
              <InputLabel for="period" value="Periodo de depreciación" />
              <TextInput v-model="fixedAsset.period" type="number" class="mt-1 w-full" disabled required />
              <InputError :message="errorForm.period" class="mt-2" />
            </div>

            <div class="w-full mt-4 sm:mt-0">
              <InputLabel for="value" value="Valor del activo fijo" />
              <TextInput v-model="fixedAsset.value" type="number" class="mt-1 w-full" min="0,00" />
              <InputError :message="errorForm.value" class="mt-2" />
            </div>
          </div>
          <!--fila 1-->
          <div class="sm:flex gap-4 mt-4">
            <div class="w-full">
              <InputLabel for="residual_value" value="Valor residual" />
              <TextInput v-model="fixedAsset.residual_value" type="number" class="mt-1 w-full" min="0,00"
                :max="fixedAsset.value" />
              <InputError :message="errorForm.residual_value" class="mt-2" />
            </div>

            <div class="w-full mt-4 sm:mt-0">
              <InputLabel for="date_end" value="Fecha final de depreciación" />
              <TextInput :value="fixedAsset.date_end" v-model="fixedAsset.date_end" type="date" class="mt-1 w-full"
                disabled required />
              <InputError :message="errorForm.date_end" class="mt-2" />
            </div>
          </div>

          <div class="col-span-6 sm:col-span-4 mt-4 sm:mt-0">
            <InputLabel for="pay_method" value="Metodo de pago" />
            <DynamicSelect class="mt-1 w-full" v-model="fixedAsset.pay_method_id" :options="payMethodOptions"
              :seleccione="false" />
            <InputError :message="errorForm.pay_method_id" class="mt-2" />
          </div>
        </div>

        <div class="flex justify-end items-center space-x-3 mt-4">
          <button type="submit" class="px-4 py-2 bg-primary hover:bg-primaryhover text-white rounded"
            :disabled="fixedAsset.processing">
            Guardar
          </button>
        </div>
      </form>
    </div>
  </AdminLayout>
</template>
