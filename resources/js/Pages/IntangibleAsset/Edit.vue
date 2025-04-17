<script setup lang="ts">
// Imports
import { reactive, computed, watch } from "vue";
import AdminLayout from "@/Layouts/AdminLayout.vue";
import { useForm, router } from "@inertiajs/vue3";
import { InputError, TextInput, InputLabel, DynamicSelect, Checkbox } from "@/Components";
import { useFocusNextField } from "@/composables/useFocusNextField";
import { IntangibleAsset } from "@/types/intangible-asset";
import { Errors, PayMethod } from "@/types";

// Props
const props = defineProps<{
  intangibleAsset: IntangibleAsset;
  payMethods: PayMethod[]; // Filtros aplicados
}>();

const { focusNextField } = useFocusNextField();

// Reactives
const intangibleAsset = useForm<IntangibleAsset>({ ...props.intangibleAsset });
const errorForm = reactive<Errors>({});

const save = () => {
  // Enviar el objeto completo 'fixedAsset' con el PUT
  intangibleAsset.put(route("intangibleassets.update", intangibleAsset.id), {
    onError: (error: Errors) => {
      // Asegurarte de limpiar los errores previos
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

// Cálculo de la fecha de finalización
const calculofecha = computed(() => {
  if (!intangibleAsset.date_acquisition || intangibleAsset.period === "") {
    return "";
  }

  const acquisitionDate = new Date(intangibleAsset.date_acquisition);
  const periodYears = parseInt(String(intangibleAsset.period), 10);

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
    value: payMethod.id !== undefined ? payMethod.id : 0,
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
        <div class="mt-4">
          <!--fila 1-->
          <div class="sm:flex gap-4 mt-4">
            <!--  primera columna dentro de la columna primera-->

            <div class="w-full">
              <Checkbox v-model:checked="intangibleAsset.is_legal" label="¿Tiene sustento legal de compra?" />
            </div>

            <!--  segunda columna dentro de la columna primera-->
            <div :hidden="intangibleAsset.is_legal !== true" class="w-full mt-4 sm:mt-0">
              <InputLabel for="is_legal" value="Número de documento" />
              <TextInput v-model="intangibleAsset.vaucher" type="text" class="mt-1 w-full"
                placeholder="001-001-098765432" max="17" />
              <InputError :message="errorForm.vaucher" class="mt-2" />
            </div>
          </div>
          <!--fila 1-->
          <div class="sm:flex gap-4 mt-4">
            <!--  cuarto columna dentro de la columna primera-->
            <div class="w-full">
              <InputLabel for="date_acquisition" value="Fecha de adquisición" />
              <TextInput v-model="intangibleAsset.date_acquisition" type="date" class="mt-1 w-full" />
              <InputError :message="errorForm.date_acquisition" class="mt-2" />
            </div>

            <!--  quinta columna dentro de la columna primera-->
            <div class="w-full mt-4 sm:mt-0">
              <InputLabel for="detail" value="Detalle" />
              <TextInput v-model="intangibleAsset.detail" type="text" class="mt-1 w-full" max="17" />
              <InputError :message="errorForm.detail" class="mt-2" />
            </div>
          </div>
          <!--fila 1-->
          <div class="sm:flex gap-4 mt-4">
            <!--  sexta columna dentro de la columna primera-->
            <div class="w-full">
              <InputLabel for="code" value="Código" />
              <TextInput v-model="intangibleAsset.code" type="text" class="mt-1 w-full" />
              <InputError :message="errorForm.code" class="mt-2" />
            </div>

            <div class="w-full mt-4 sm:mt-0">
              <InputLabel for="type" value="Tipo" />
              <TextInput v-model="intangibleAsset.type" type="text" class="mt-1 w-full" />
              <InputError :message="errorForm.type" class="mt-2" />
            </div>
          </div>

          <!--fila 1-->
          <div class="sm:flex gap-4 mt-4">
            <div class="w-full">
              <InputLabel for="period" value="Periodo" />
              <TextInput v-model="intangibleAsset.period" type="number" class="mt-1 w-full" value="5" />
              <InputError :message="errorForm.period" class="mt-2" />
            </div>

            <div class="w-full mt-4 sm:mt-0">
              <InputLabel for="value" value="Valor del activo fijo" />
              <TextInput v-model="intangibleAsset.value" type="number" class="mt-1 w-full" min="0" />
              <InputError :message="errorForm.value" class="mt-2" />
            </div>
          </div>

          <!--fila 1-->
          <div class="sm:flex gap-4 mt-4">
            <div class="w-full">
              <InputLabel for="date_end" value="Fecha final de depreciación" />
              <TextInput :value="calculofecha" v-model="intangibleAsset.date_end" type="date" class="mt-1 w-full"
                disabled />
              <InputError :message="errorForm.date_end" class="mt-2" />
            </div>

            <div class="w-full mt-4 sm:mt-0">
              <InputLabel for="pay_method_id" value="Método de pago" />
              <DynamicSelect class="mt-1 w-full" v-model="intangibleAsset.pay_method_id" :options="payMethodOptions"
                :seleccione="false" autofocus />

              <InputError :message="errorForm.pay_method_id" class="mt-2" />
            </div>
          </div>
        </div>
        <div class="mt-4 text-right">
          <SecondaryButton @click="() => router.visit(route('intangibleassets.index'))" class="mr-2">
            Cancelar
          </SecondaryButton>
          <button @click="save" :disabled="intangibleAsset.processing"
            class="px-4 py-2 bg-primary hover:bg-primaryhover text-white rounded">
            Guardar
          </button>
        </div>
      </form>
    </div>
  </AdminLayout>
</template>
