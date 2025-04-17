<script setup lang="ts">
// Imports
import { reactive, computed, ref, watch } from "vue";
import AdminLayout from "@/Layouts/AdminLayout.vue";
import { useForm, usePage } from "@inertiajs/vue3";
import { TextInput, InputError, InputLabel, DynamicSelect } from "@/Components";
import { BankAccount, Box, Errors, Expense, TransactionExpense } from "@/types";

// Props
const props = defineProps<{
  bankAccounts: BankAccount[];
  expenses: Expense[];
  boxes: Box[];
}>();

const page = usePage();
const errors = computed(() => page.props.errors);
const showErrorModal = ref(false); // Estado del modal de error
const redirect = ref("");
// Observa si hay errores y muestra el modal automáticamente
watch(errors, (newErrors) => {
  if (newErrors?.error) {
    showErrorModal.value = true;
  }
});

const closeErrorModal = () => {
  showErrorModal.value = false;
  window.open(route(redirect.value), "_blank");
};

const date_expense = new Date().toISOString().split("T")[0];

// Inicializador de objetos
const initialTransactionExpense = {
  description: "",
  amount: "",
  expense_id: 0,
  payment_type: "",
  payment_method_id: "",
  document: "",
};
const expense_id = props.expenses.length === 1 ? props.expenses[0].id : 0;

// Reactives
const transactionExpense = useForm<TransactionExpense>({
  ...initialTransactionExpense,
  expense_id,
  date_expense,
});

const errorForm = reactive<Errors>({});

const save = () => {
  transactionExpense.post(route("transaction.expenses.store"), {
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

const TypeOptions = ref();

watch(
  () => transactionExpense.amount, // Observa cambios en el monto
  (newAmount) => {
    if (Number(String(newAmount)) >= 500) {
      TypeOptions.value = [{ value: "banco", label: "Bancos" }];
    } else {
      TypeOptions.value = [
        { value: "caja", label: "Caja" },
        { value: "banco", label: "Bancos" },
      ];
    }
  }
);

const paymentMethodOptionsBank = props.bankAccounts.map((bankAccount) => ({
  value: bankAccount.id ?? 0,
  label: `${bankAccount.name} ${bankAccount.account_number}`,
}));

const paymentMethodOptionsBox = props.boxes.map((box) => ({
  value: box.id ?? 0,
  label: box.name,
}));

const expenseOptions = props.expenses.map((expense) => ({
  value: expense.id ?? 0,
  label: expense.name,
}));
</script>

<template>
  <AdminLayout title="Gastos">
    <!-- Card -->
    <div class="p-4 bg-white rounded drop-shadow-md">
      <!-- Modal de error -->
      <div v-if="showErrorModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50">
        <div class="bg-white p-6 rounded shadow-lg w-96">
          <h2 class="text-lg font-bold text-red-600">Error</h2>
          <p class="mt-2 text-gray-700">{{ errors.error }}</p>
          <div class="mt-4 text-right">
            <button @click="closeErrorModal" class="px-4 py-2 bg-red-500 text-white rounded">
              Aceptar
            </button>
          </div>
        </div>
      </div>
      <div class="flex justify-between items-center">
        <h2 class="text-sm sm:text-lg font-bold">Registro de gastos</h2>
      </div>

      <!-- Formulario -->
      <div class="mt-4">
        <!--fila 1-->
        <div class="sm:flex gap-4">
          <!-- Fecha -->
          <div class="w-full">
            <InputLabel for="date" value="Fecha" />
            <TextInput v-model="transactionExpense.date_expense" type="date" class="mt-1 w-full" :max="date_expense" />
            <InputError :message="errorForm.date_expense" class="mt-2" />
          </div>

          <div class="w-full mt-4 sm:mt-0">
            <InputLabel for="payment_method_id" value="Gastos" />
            <DynamicSelect class="mt-1 w-full" v-model="transactionExpense.expense_id" :options="expenseOptions"
              autofocus />
            <InputError :message="errorForm.expense_id" class="mt-2" />
          </div>
        </div>

        <!--fila 2-->
        <div class="sm:flex gap-4 mt-4">
          <!-- Campo de Monto -->
          <div class="w-full">
            <InputLabel for="amount" value="Monto" />
            <TextInput v-model="transactionExpense.amount" type="number" class="mt-1 w-full" min="0" step="0.01" />
            <InputError :message="errorForm.amount" class="mt-2" />
          </div>

          <div class="w-full mt-4 sm:mt-0">
            <InputLabel for="type" value="Tipo de pago" />
            <DynamicSelect v-model="transactionExpense.payment_type" :options="TypeOptions"
              placeholder="Seleccione un tipo" class="mt-1 w-full" />
          </div>
        </div>

        <!--fila 3-->
        <div class="sm:flex gap-4 mt-4">
          <!-- Cuenta de bancos-->
          <div class="w-full">
            <InputLabel for="payment_method_id" value="Formas de pago" />
            <DynamicSelect class="mt-1 w-full" v-model="transactionExpense.payment_method_id" :options="transactionExpense.payment_type === 'banco'
              ? paymentMethodOptionsBank
              : paymentMethodOptionsBox
              " autofocus />
            <InputError :message="errorForm.payment_method_id" class="mt-2" />
          </div>

          <div class="w-full mt-4 sm:mt-0">
            <InputLabel for="document" value="Numero de comprobante" />
            <TextInput v-model="transactionExpense.document" type="text" class="mt-1 w-full" minlength="3"
              maxlength="300" />
            <InputError :message="errorForm.document" class="mt-2" />
          </div>
        </div>

        <div class="mt-4">
          <InputLabel for="description" value="Descripción" />
          <TextInput v-model="transactionExpense.description" type="text" class="mt-1 w-full" minlength="3"
            maxlength="300" />
          <InputError :message="errorForm.description" class="mt-2" />
        </div>

        <div class="mt-4 text-right">
          <button @click="save" :disabled="transactionExpense.processing"
            class="px-4 py-2 bg-primary hover:bg-primaryhover text-white rounded">
            Guardar
          </button>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>