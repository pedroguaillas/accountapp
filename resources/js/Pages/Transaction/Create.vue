<script setup lang="ts">
// Imports
import { reactive, computed, ref, watch } from "vue";
import AdminLayout from "@/Layouts/AdminLayout.vue";
import SearchBankAccount from "./SearchBankAccount.vue";
import SearchPerson from "./SearchPerson.vue";
import { useForm, router, usePage } from "@inertiajs/vue3";
import { TextInput, InputError, InputLabel, DynamicSelect } from "@/Components";
import { BankAccount, Errors, MovementType, Person, TransactionBank } from "@/types";

// Props
const props = defineProps<{
  bankaccounts: BankAccount[],
  movementtypes: MovementType[],
  people:Person[],
  countperson: number;
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

const date = new Date().toISOString().split("T")[0];

// Inicializador de objetos
const initialTransaction = {
  movement_type_id: "",
  bank_account_id: 0,
  transaction_date: date,
  amount: "",
  description: "",
  payment_method: "",
  beneficiary_id: "",
  cheque_date: "",
  transfer_account: "",
  voucher_number: "",
  state_transaction: "pendiente",
};

const bank_account_id =
  props.bankaccounts.length === 1 ? props.bankaccounts[0].id : 0;

const beneficiary_id = props.people.length === 1 ? props.people[0].id : 0;
// Reactives
const transaction = useForm({
  ...initialTransaction,
  bank_account_id,
  beneficiary_id,
});
const errorForm = reactive<Errors>({});

const save = () => {
  transaction.post(route("transactions.store"), {
    // onSuccess: () => {
    //  // router.visit(route("transactions.index"));
    // },
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

// Método para recibir el centro de costo seleccionado desde SearchCostCenter
const handleBankAccountSelect = (bankaccount: BankAccount) => {
  transaction.bank_account_id = bankaccount.id; // Asignar el centro de costo al objeto journal
};

const handlePersonSelect = (person: Person) => {
  transaction.beneficiary_id = person.id; // Asignar el centro de costo al objeto journal
};

const TypeOptions = [
  { value: "Ingreso", label: "Ingreso" },
  { value: "Egreso", label: "Egreso" },
];

const paymentMethodOptions = [
  { value: "1", label: "Cheque" },
  { value: "2", label: "Transferencia" },
  { value: "3", label: "Deposito" },
];

const movementTypeOptions = props.movementtypes.map((type) => ({
  value: type.id ?? 0,
  label: type.name,
  type: type.type,
}));

const peopleOptions = props.people.map((person) => ({
  value: person.id ?? 0,
  label: person.name,
}));

const selectedType = ref("");

const filteredMovementTypes = computed(() => {
  return movementTypeOptions.filter((type) => type.type === selectedType.value);
});

const bankacountOptions = props.bankaccounts.map((bankacount) => ({
  value: bankacount.id ?? 0,
  label: `${bankacount.name} - ${bankacount.account_number}`,
}));
</script>

<template>
  <AdminLayout title="Asientos Contables">
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
          Registro movimientos bancarios
        </h2>
      </div>

      <!-- Formulario -->
      <div class="mt-4">
        <!--fila 1-->
        <div class="sm:flex gap-4 mt-4">
          <!-- Fecha -->
          <div class="w-full">
            <InputLabel for="date" value="Fecha" />
            <TextInput
              v-model="transaction.transaction_date"
              type="date"
              class="mt-1 w-full"
            />
            <InputError :message="errorForm.date" class="mt-2" />
          </div>

          <div class="w-full mt-4 sm:mt-0">
            <InputLabel for="type" value="Tipo" />
            <DynamicSelect
              v-model="selectedType"
              :options="TypeOptions"
              placeholder="Seleccione un tipo"
              class="mt-1 w-full"
            />
          </div>
        </div>

        <!--fila 2-->
        <div class="sm:flex gap-4 mt-4">
          <div class="w-full">
            <InputLabel for="movement_type" value="Tipo de Movimiento" />
            <DynamicSelect
              v-model="transaction.movement_type_id"
              :options="filteredMovementTypes"
              placeholder="Seleccione un tipo de movimiento"
              class="mt-1 w-full"
            />
            <InputError :message="errorForm.movement_type_id" class="mt-2" />
          </div>

          <!-- Cuenta de bancos-->
          <div v-if="bankaccounts.length > 0" class="w-full mt-4 sm:mt-0">
            <InputLabel for="bankaccount_id" value="Cuenta de banco" />
            <DynamicSelect
              v-if="bankaccounts.length <= 5"
              class="mt-1 w-full"
              v-model="transaction.bank_account_id"
              :options="bankacountOptions"
              autofocus
            />
            <SearchBankAccount
              v-else-if="bankaccounts.length > 5"
              :bankaccounts="bankaccounts"
              @selectBankAccount="handleBankAccountSelect"
            />
            <InputError :message="errorForm.bank_account_id" class="mt-2" />
          </div>
        </div>

        <!--fila 3-->
        <div class="sm:flex gap-4 mt-4">
          <!-- Campo de Descripción -->
          <div class="w-full">
            <InputLabel for="voucher_number" value="Número de comprobante" />

            <TextInput
              type="text"
              v-model="transaction.voucher_number"
              class="mt-1 w-full"
              placeholder="Ingrese el número de comprobante"
            />
          </div>

          <!-- Campo de Monto -->
          <div class="w-full mt-4 sm:mt-0">
            <InputLabel for="amount" value="Monto" />
            <TextInput
              v-model="transaction.amount"
              type="number"
              class="mt-1 w-full"
              min="0"
              step="0.01"
            />
            <InputError :message="errorForm.amount" class="mt-2" />
          </div>
        </div>

        <!--fila 4-->
        <div class="sm:flex gap-4 mt-4">
          <div
            class="w-full sm:w-[50%]"
            :class="transaction.payment_method !== '1' ? 'sm:pr-2' : ''"
          >
            <InputLabel for="payment_method" value="Tipo de pago" />
            <DynamicSelect
              id="payment_method"
              v-model="transaction.payment_method"
              :options="paymentMethodOptions"
              placeholder="Seleccione un método de pago"
              class="mt-1 w-full"
            />
          </div>

          <div
            class="w-full sm:w-[50%] mt-4 sm:mt-0"
            v-if="transaction.payment_method === '1'"
          >
            <InputLabel for="cheque_date" value="Fecha del cheque" />
            <TextInput
              type="date"
              v-model="transaction.cheque_date"
              class="mt-1 w-full"
            />
          </div>
        </div>

        <!--fila 5-->

        <div class="sm:flex gap-4 mt-4">
          <!-- beneficiario-->
          <div v-if="props.countperson > 0" class="w-full">
            <InputLabel for="person_id" value="Beneficiario/Remitente" />
            <DynamicSelect
              v-if="props.countperson < 5"
              class="mt-1 w-full"
              v-model="transaction.beneficiary_id"
              :options="peopleOptions"
              autofocus
            />
            <SearchPerson
              v-else-if="people.length > 5"
              @selectPerson="handlePersonSelect"
            />
            <InputError :message="errorForm.beneficiary_id" class="mt-2" />
          </div>
          <div v-else class="col-span-6 sm:col-span-4">
            <p class="text-red-500 mt-2">
              ⚠️ No hay personas disponibles para seleccionar como beneficiario
              o remitente.
            </p>
          </div>

          <div class="w-full mt-4 sm:mt-0">
            <InputLabel for="transfer_account" value="Número de cuenta" />
            <TextInput
              type="text"
              v-model="transaction.transfer_account"
              class="mt-1 w-full"
              placeholder="Ingrese el número de cuenta"
            />
          </div>
        </div>

        <div class="col-span-6 sm:col-span-4">
          <InputLabel for="description" value="Descripción" />
          <TextInput
            v-model="transaction.description"
            type="text"
            class="mt-1 w-full"
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
