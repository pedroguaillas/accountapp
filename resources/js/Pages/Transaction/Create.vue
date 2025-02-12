<script setup>
// Imports
import { reactive, computed, ref } from "vue";
import AdminLayout from "@/Layouts/AdminLayout.vue";
import SearchBankAccount from "./SearchBankAccount.vue";
import SearchPerson from "./SearchPerson.vue";
import { useForm, router } from "@inertiajs/vue3";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import DynamicSelect from "@/Components/DynamicSelect.vue";
import { TrashIcon } from "@heroicons/vue/24/outline";
import Checkbox from "@/Components/Checkbox.vue";

// Props
const props = defineProps({
  bankaccounts: { type: Array, default: () => [] },
  movementtypes: { type: Array, default: () => [] },
  people: { type: Array, default: () => [] },
  countperson: { type: Number, default: ()=>0} ,
});

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
  cheque_date: null,
  transfer_account: "",
  voucher_number: "",
  state_transaction: "proceso",
};

// Reactives
const transaction = useForm({ ...initialTransaction });
const errorForm = reactive({});

const save = () => {
  transaction.post(route("transactions.store"), {
    onSuccess: () => {
      router.visit(route("transactions.index"));
    },
    onError: (errors) => {
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

const handlePersonSelect = (person) => {
  transaction.beneficiary_id = person.id; // Asignar el centro de costo al objeto journal
};

const TypeOptions = [
  { value: "Ingreso", label: "Ingreso" },
  { value: "Egreso", label: "Egreso" },
];

const statusOptions = [
  { value: "proceso", label: "En proceso" },
  { value: "correcto", label: "Correcto" },
  { value: "cancelado", label: "Cancelado" },
];

const paymentMethodOptions = [
  { value: "1", label: "Cheque" },
  { value: "2", label: "Transferencia" },
  { value: "3", label: "Deposito" },
];

const movementTypeOptions = props.movementtypes.map((type) => ({
  value: type.id,
  label: type.name,
  type: type.type,
}));

const peopleOptions = props.people.map((person) => ({
  value: person.id,
  label: person.name,
}));

const selectedType = ref("");

const filteredMovementTypes = computed(() => {
  return movementTypeOptions.filter((type) => type.type === selectedType.value);
});

const bankacountOptions = props.bankaccounts.map((bankacount) => ({
  value: bankacount.id,
  label: `${bankacount.name} - ${bankacount.account_number}`,
}));
</script>

<template>
  <AdminLayout title="Asientos Contables">
    <!-- Card -->
    <div class="p-4 bg-white rounded drop-shadow-md">
      <!-- Card Header -->
      <div class="flex justify-between items-center">
        <h2 class="text-sm sm:text-lg font-bold">
          Registro movimientos bancarios
        </h2>
      </div>

      <!-- Formulario -->
      <div class="mt-4">
        <!-- Fecha -->
        <div class="col-span-6 sm:col-span-4">
          <InputLabel for="date" value="Fecha" />
          <TextInput
            v-model="transaction.transaction_date"
            type="date"
            class="mt-2 block w-full"
          />
          <InputError :message="errorForm.date" class="mt-2" />
        </div>

        <div class="col-span-6 sm:col-span-4">
          <InputLabel for="type" value="Tipo" />
          <DynamicSelect
            v-model="selectedType"
            :options="TypeOptions"
            placeholder="Seleccione un tipo"
            class="mt-2 block w-full"
          />
        </div>

        <div class="col-span-6 sm:col-span-4">
          <InputLabel for="movement_type" value="Tipo de Movimiento" />
          <DynamicSelect
            v-model="transaction.movement_type_id"
            :options="filteredMovementTypes"
            placeholder="Seleccione un tipo de movimiento"
            class="mt-2 block w-full"
          />
          <InputError :message="errorForm.movement_type_id" class="mt-2" />
        </div>

        <!-- Cuenta de bancos-->
        <div v-if="bankaccounts.length > 0" class="col-span-6 sm:col-span-4">
          <InputLabel for="bankaccount_id" value="Cuenta de banco" />
          <DynamicSelect
            v-if="bankaccounts.length <= 5"
            class="mt-2 block w-full"
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

        <!-- Campo de Descripción -->
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

        <!-- Campo de Monto -->
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
          <div class="col-span-6 sm:col-span-4">
            <InputLabel for="payment_method" value="Tipo de pago" />
            <DynamicSelect
              id="payment_method"
              v-model="transaction.payment_method"
              :options="paymentMethodOptions"
              placeholder="Seleccione un método de pago"
              class="mt-2 block w-full"
            />
          </div>
        </div>

        <!-- beneficiario-->
        <div v-if="props.countperson > 0" class="col-span-6 sm:col-span-4">
          <InputLabel for="person_id" value="Benediciario" />
          <DynamicSelect
            v-if=" props.countperson<5"
            class="mt-2 block w-full"
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

        <div
          class="col-span-6 sm:col-span-4"
          v-if="transaction.payment_method === '1'"
        >
          <InputLabel for="cheque_date" value="Fecha del cheque" />
          <TextInput
            type="date"
            v-model="transaction.cheque_date"
            class="mt-1 block w-full"
          />
        </div>

        <div
          class="col-span-6 sm:col-span-4"
          v-if="
            transaction.payment_method !== 'cheque' &&
            transaction.payment_method !== ''
          "
        >
          <InputLabel for="transfer_account" value="Número de cuenta" />
          <TextInput
            type="text"
            v-model="transaction.transfer_account"
            class="mt-1 block w-full"
            placeholder="Ingrese el número de cuenta"
          />
        </div>

        <div class="col-span-6 sm:col-span-4">
          <InputLabel for="voucher_number" value="Número de comprobante" />

          <TextInput
            type="text"
            v-model="transaction.voucher_number"
            class="mt-1 block w-full"
            placeholder="Ingrese el número de comprobante"
          />
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
