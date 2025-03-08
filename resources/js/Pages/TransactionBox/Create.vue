<script setup>
import { reactive, ref } from "vue";
import AdminLayout from "@/Layouts/AdminLayout.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import DynamicSelect from "@/Components/DynamicSelect.vue";
import { useForm, router } from "@inertiajs/vue3";

const props = defineProps({
  cashSessions: { type: Array, default: () => [] },
});

const date = new Date().toISOString().split("T")[0];

const initialTransaction = {
  cash_session_id: 0,
  transaction_date: date,
  type: "",
  amount: "",
  description: "",
}
  const cash_session_id =
  props.cashSessions.length === 1 ? props.cashSessions[0].id : 0;

const transaction = useForm({ ...initialTransaction,cash_session_id });
const errorForm = reactive({});

const TypeOptions = [
  { value: "Ingreso", label: "Ingreso" },
  { value: "Egreso", label: "Egreso" },
];

const cashSessionOptions = props.cashSessions.map((session) => ({
  value: session.id,
  label: `${session.id} - ${session.name}`, // 🔥 Aquí la interpolación es correcta
}));

const save = () => {
  console.log("Ruta de la petición:", route("transaction.boxes.store"));
  
  transaction.post(route("transaction.boxes.store"), {
    onSuccess: () => {
      console.log("Guardado con éxito");
      router.visit(route("transaction.boxes.index"));
    },
    onError: (errors) => {
      console.error("Errores:", errors);
      Object.keys(errors).forEach((key) => {
        errorForm[key] = errors[key];
      });
    },
  });
};

</script>

<template>
  <AdminLayout title="Registro de Transacciones">
    <div class="p-4 bg-white rounded drop-shadow-md">
      <h2 class="text-lg font-bold">Registrar Transacción</h2>
      <div class="mt-4">
        <div class="col-span-6 sm:col-span-4">
          <InputLabel for="cash_session_id" value="Sesión de Caja" />
          <DynamicSelect
            v-model="transaction.cash_session_id"
            :options="cashSessionOptions"
            placeholder="Seleccione una sesión"
            class="mt-2 block w-full"
          />
          <InputError :message="errorForm.cash_session_id" class="mt-2" />
        </div>

        <div class="col-span-6 sm:col-span-4">
          <InputLabel for="transaction_date" value="Fecha" />
          <TextInput
            v-model="transaction.transaction_date"
            type="date"
            class="mt-2 block w-full"
          />
          <InputError :message="errorForm.transaction_date" class="mt-2" />
        </div>

        <div class="col-span-6 sm:col-span-4">
          <InputLabel for="type" value="Tipo" />
          <DynamicSelect
            v-model="transaction.type"
            :options="TypeOptions"
            placeholder="Seleccione un tipo"
            class="mt-2 block w-full"
          />
          <InputError :message="errorForm.type" class="mt-2" />
        </div>

        <div class="col-span-6 sm:col-span-4">
          <InputLabel for="amount" value="Monto" />
          <TextInput
            v-model="transaction.amount"
            type="number"
            class="mt-1 block w-full"
            min="0"
            step="0.01"
          />
          <InputError :message="errorForm.amount" class="mt-2" />
        </div>

        <div class="col-span-6 sm:col-span-4">
          <InputLabel for="description" value="Descripción" />
          <TextInput
            v-model="transaction.description"
            type="text"
            class="mt-1 block w-full"
            minlength="3"
            maxlength="300"
          />
          <InputError :message="errorForm.description" class="mt-2" />
        </div>

        <div class="mt-4 text-right">
          <button
            @click="save"
            :disabled="transaction.processing"
            class="px-4 py-2 bg-primary hover:bg-primaryhover text-white rounded"
          >
            Guardar
          </button>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>