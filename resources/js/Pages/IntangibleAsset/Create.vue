<script setup>
// Imports
import { reactive, computed } from "vue";
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
});

const date = new Date().toISOString().split("T")[0];

// Inicializador de objetos
const initialIntangibleAsset = {
  pay_method_id: "",
  is_legal: false,
  vaucher: "",
  date_acquisition: date,
  detail: "",
  code: "",
  type: "",
  period: "5",
  value: "",
  date_end: "",
};

// Reactives
const intangibleAsset = useForm({ ...initialIntangibleAsset });
const errorForm = reactive({});

const resetErrorForm = () => {
  Object.assign(errorForm, initialIntangibleAsset);
};

// Método de guardar con mensajes
const save = () => {
  // Validar datos requeridos en el frontend
  if (!intangibleAsset.pay_method_id) {
    errorForm.pay_method_id = "Seleccione un método de pago.";
    return;
  }

  if (!intangibleAsset.date_acquisition) {
    errorForm.date_acquisition = "La fecha de adquisición es obligatoria.";
    return;
  }

  // Enviar datos al backend
  intangibleAsset.post(route("intangibleassets.store"), {
    onError: (errors) => {
      // Mostrar errores de validación desde el backend
      Object.keys(errors).forEach((key) => {
        errorForm[key] = errors[key];
      });
    },
    onSuccess: () => {
      // Reiniciar el formulario tras éxito
      intangibleAsset.reset();
      resetErrorForm();
    },
  });
};

const calculofecha = computed(() => {
  // Si no hay fecha de adquisición o periodo, no calculamos la fecha final
  if (!intangibleAsset.date_acquisition || intangibleAsset.period === "") {
    return "";
  }

  // Convertir la fecha de adquisición a un objeto Date
  const acquisitionDate = new Date(intangibleAsset.date_acquisition);

  // Asegurarse de que el periodo sea un número entero
  const periodYears = parseInt(intangibleAsset.period, 10);

  // Si el periodo es 0, la fecha final será la misma que la fecha de adquisición
  if (periodYears === 0) {
    intangibleAsset.date_end = acquisitionDate.toISOString().split("T")[0];
    return intangibleAsset.date_end;
  }

  // Si el periodo es mayor a 0, calculamos la fecha final sumando los años al año de adquisición
  const endDate = new Date(
    acquisitionDate.getFullYear() + periodYears,
    acquisitionDate.getMonth(),
    acquisitionDate.getDate()
  );

  // Asignamos la fecha de finalización calculada
  intangibleAsset.date_end = endDate.toISOString().split("T")[0];

  return intangibleAsset.date_end;
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
              disabled
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
        <button
          @click="save"
          :disabled="intangibleAsset.processing"
          class="px-4 py-2 bg-primary hover:bg-primaryhover text-white rounded"
        >
          Guardar
        </button>
      </div>
    </div>
  </AdminLayout>
</template>
