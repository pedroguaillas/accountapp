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
  intangibleAsset: { type: Object, default: () => {} },
  payMethods: { type: Array, default: () => [] },
});

const { focusNextField } = useFocusNextField();

// Reactives
const intangibleAsset = useForm({ ...props.intangibleAsset});
const errorForm = reactive({});

const save = () => {
  // Enviar el objeto completo 'fixedAsset' con el PUT
  intangibleAsset.put(route("intangibleassets.update", intangibleAsset.id), {
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
  if (!intangibleAsset.date_acquisition || intangibleAsset.period === "") {
    return "";
  }

  const acquisitionDate = new Date(intangibleAsset.date_acquisition);
  const periodYears = parseInt(intangibleAsset.period, 10);

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
  intangibleAsset.date_end = newDate;
});



const payMethodOptions = Array.isArray(props.payMethods)
  ? props.payMethods.map((payMethod) => ({
      value: payMethod.id,
      label: payMethod.name,
    }))
  : [];

</script>

<template>
  <AdminLayout title="Activos Intangibles">
    <!-- Card -->
    <div class="p-4 bg-white rounded drop-shadow-md">
      <!-- Card Header -->
      <div class="flex justify-between items-center">
        <h2 class="text-sm sm:text-lg font-bold">Activo Intangible</h2>
      </div>

      <!-- Formulario -->
      <form @submit.prevent="save" @keydown.enter.prevent="focusNextField">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- Primera columna -->
        <div class="grid grid-cols-1 gap-4">
          <!--  primera columna dentro de la columna primera-->

          <div class="col-span-6 sm:col-span-4">
            <Checkbox
              v-model:checked="intangibleAsset.is_legal"
              label="¿Tiene sustento legal de compra?"
            />
          </div>

          <!--  segunda columna dentro de la columna primera-->
          <div
            :hidden="intangibleAsset.is_legal !== true"
            class="col-span-6 sm:col-span-4"
          >
            <InputLabel for="is_legal" value="Número de documento" />
            <TextInput
              v-model="intangibleAsset.vaucher"
              type="text"
              class="mt-1 block w-full"
              placeholder="001-001-098765432"
              max="17"
            />
            <InputError :message="errorForm.vaucher" class="mt-2" />
          </div>

          <!--  cuarto columna dentro de la columna primera-->
          <div class="col-span-6 sm:col-span-4">
            <InputLabel for="date_acquisition" value="Fecha de adquisición" />
            <TextInput
              v-model="intangibleAsset.date_acquisition"
              type="date"
              class="mt-1 block w-full"
            />
            <InputError :message="errorForm.date_acquisition" class="mt-2" />
          </div>

          <!--  quinta columna dentro de la columna primera-->
          <div class="col-span-6 sm:col-span-4">
            <InputLabel for="detail" value="Detalle" />
            <TextInput
              v-model="intangibleAsset.detail"
              type="text"
              class="mt-1 block w-full"
              max="17"
            />
            <InputError :message="errorForm.detail" class="mt-2" />
          </div>

          <!--  sexta columna dentro de la columna primera-->
          <div class="col-span-6 sm:col-span-4">
            <InputLabel for="code" value="Código" />
            <TextInput
              v-model="intangibleAsset.code"
              type="text"
              class="mt-1 block w-full"
            />
            <InputError :message="errorForm.code" class="mt-2" />
          </div>

          <div class="col-span-6 sm:col-span-4">
            <InputLabel for="type" value="Tipo" />
            <TextInput
              v-model="intangibleAsset.type"
              type="text"
              class="mt-1 block w-full"
            />
            <InputError :message="errorForm.type" class="mt-2" />
          </div>
        </div>

        <!-- Segunda columna  de la principal-->

        <div class="grid grid-cols-1 gap-4">
          <!-- primera columna de la segunda columna -->

          <div class="col-span-6 sm:col-span-4">
            <InputLabel for="period" value="Periodo" />
            <TextInput
              v-model="intangibleAsset.period"
              type="number"
              class="mt-1 block w-full"
              value="5"
            />
            <InputError :message="errorForm.period" class="mt-2" />
          </div>

          <div class="col-span-6 sm:col-span-4">
            <InputLabel for="value" value="Valor del activo fijo" />
            <TextInput
              v-model="intangibleAsset.value"
              type="number"
              class="mt-1 block w-full"
              min="0"
            />
            <InputError :message="errorForm.value" class="mt-2" />
          </div>

          <div class="col-span-6 sm:col-span-4">
            <InputLabel for="date_end" value="Fecha final de depreciación" />
            <TextInput
              :value="calculofecha"
              v-model="intangibleAsset.date_end"
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
              v-model="intangibleAsset.pay_method_id"
              :options="payMethodOptions"
              autofocus
            />

            <InputError :message="errorForm.pay_method_id" class="mt-2" />
          </div>
        </div>
      </div>
      <div class="mt-4 text-right">
        <button @click="save" class="px-4 py-2 bg-blue-500 text-white rounded">
          Guardar
        </button>
      </div>
      </form>
    </div>
  </AdminLayout>
</template>
