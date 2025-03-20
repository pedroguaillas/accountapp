<script setup>
// Imports
import { reactive, computed, ref, watch } from "vue";
import AdminLayout from "@/Layouts/AdminLayout.vue";
import SearchEmployee from "./SearchEmployee.vue";
import { useForm, router, usePage } from "@inertiajs/vue3";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import DynamicSelect from "@/Components/DynamicSelect.vue";
import { TrashIcon } from "@heroicons/vue/24/outline";

// Props
const props = defineProps({
  bankAccounts: { type: Array, default: () => [] },
  employees: { type: Array, default: () => [] },
  cash: { type: Array, default: () => [] },
  optimum: { type: Boolean, default: () => false },
});

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

const date = new Date().toISOString().split("T")[0];

// Inicializador de objetos
const initialAdvance = {
  detail: "",
  amount: "",
  employee_id: 0,
  payment_type: "",
  payment_method_id: 0, //identificador de caja o banco
  date: "",
  receipt_number: "",
};

const employee_id = props.employees.length === 1 ? props.employees[0].id : 0;
// Reactives
const advance = useForm({
  ...initialAdvance,
  employee_id,
  date,
});
const errorForm = reactive({});

const save = () => {
  advance.post(route("advances.store"), {
    onError: (errors) => {
      if (errors.redirect) {
        redirect.value = errors.redirect;
      }
      Object.keys(errors).forEach((key) => {
        errorForm[key] = errors[key];
      });
    },
  });
};

// Método para recibir el centro de costo seleccionado desde SearchCostCenter
const handleBankAccountSelect = (bankaccount) => {
  transaction.bank_account_id = bankaccount.id; // Asignar el centro de costo al objeto journal
};

const handleEmployeeSelect = (employee) => {
  advance.employee_id = employee.id; // Asignar el centro de costo al objeto journal
};

const TypeOptions = ref(); 

watch(
  () => advance.amount, // Observa cambios en el monto
  (newAmount) => {
    if (newAmount >= 500) {
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
  value: bankAccount.id,
  label: `${bankAccount.name} ${bankAccount.account_number}`,
}));

const paymentMethodOptionsBox = props.cash.map((box) => ({
  value: box.id,
  label: box.name,
}));

const employeeOptions = props.employees.map((person) => ({
  value: person.id,
  label: person.name,
}));
</script>

<template>
  <AdminLayout title="Adelanto empleados">
    <!-- Card -->
    <div class="p-4 bg-white rounded drop-shadow-md">
      <!-- Modal de error -->
      <div
        v-if="showErrorModal"
        class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50"
      >
        <div class="bg-white p-6 rounded shadow-lg w-96">
          <h2 class="text-lg font-bold text-red-600">Error</h2>
          <p class="mt-2 text-gray-700">{{ errors.error }}</p>
          <div class="mt-4 text-right">
            <button
              @click="closeErrorModal"
              class="px-4 py-2 bg-red-500 text-white rounded"
            >
              Aceptar
            </button>
          </div>
        </div>
      </div>
      <div class="flex justify-between items-center">
        <h2 class="text-sm sm:text-lg font-bold">
          Registro de adelanto de empleados
        </h2>
      </div>

      <!-- Formulario -->
      <div class="mt-4">
        <!-- Fecha -->
        <div class="col-span-6 sm:col-span-4">
          <InputLabel for="date" value="Fecha" />
          <TextInput
            v-model="advance.date"
            type="date"
            class="mt-2 block w-full"
            :max="date"
          />
          <InputError :message="errorForm.date" class="mt-2" />
        </div>

        <!-- Campo de Monto -->
        <div class="col-span-6 sm:col-span-4">
          <InputLabel for="amount" value="Monto" />
          <TextInput
            v-model="advance.amount"
            type="number"
            class="mt-1 block w-full"
            min="0"
            step="0.01"
          />
          <InputError :message="errorForm.amount" class="mt-2" />
        </div>

        <div class="col-span-6 sm:col-span-4">
          <InputLabel for="type" value="Tipo de pago" />
          <DynamicSelect
            v-model="advance.payment_type"
            :options="TypeOptions"
            placeholder="Seleccione un tipo"
            class="mt-2 block w-full"
          />
        </div>

        <!-- Cuenta de bancos-->
        <div class="col-span-6 sm:col-span-4">
          <InputLabel for="payment_method_id" value="Formas de pago" />
          <DynamicSelect
            class="mt-2 block w-full"
            v-model="advance.payment_method_id"
            :options="
              advance.payment_type === 'banco'
                ? paymentMethodOptionsBank
                : paymentMethodOptionsBox
            "
            autofocus
          />
          <InputError :message="errorForm.payment_method_id" class="mt-2" />
        </div>

        <div
          v-show="advance.payment_type === 'banco'"
          class="col-span-6 sm:col-span-4"
        >
          <InputLabel for="receipt_number" value="Numero de comprobante" />
          <TextInput
            v-model="advance.receipt_number"
            type="text"
            class="mt-1 block w-full"
            minlength="3"
            maxlength="300"
          />
          <InputError :message="errorForm.receipt_number" class="mt-2" />
        </div>

        <!-- beneficiario-->
        <div class="col-span-6 sm:col-span-4">
          <InputLabel for="employee_id" value="Empleado" />
          <DynamicSelect
            v-if="props.optimum"
            class="mt-2 block w-full"
            v-model="advance.employee_id"
            :options="employeeOptions"
            autofocus
          />
          <SearchEmployee
            v-else
            @selectEmployees="handleEmployeeSelect"
          />
          <InputError :message="errorForm.employee_id" class="mt-2" />
        </div>

        <div class="col-span-6 sm:col-span-4">
          <InputLabel for="detail" value="Detalle" />
          <TextInput
            v-model="advance.detail"
            type="text"
            class="mt-1 block w-full"
            minlength="3"
            maxlength="300"
          />
          <InputError :message="errorForm.detail" class="mt-2" />
        </div>

        <div class="mt-4 text-right">
          <button
            @click="save"
            :disabled="advance.processing"
            class="px-4 py-2 bg-primary hover:bg-primaryhover text-white rounded"
          >
            Guardar
          </button>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>
