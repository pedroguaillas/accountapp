<script setup>
// Imports
import { reactive, computed } from "vue";
import AdminLayout from "@/Layouts/AdminLayout.vue";
import SearchAccount from "./SearchAccount.vue";
import SearchCostCenter from "./SearchCostCenter.vue";
import { router, useForm } from "@inertiajs/vue3";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/InputError.vue";
import axios from "axios";

// Props
const props = defineProps({
  accounts: { type: Array, default: () => [] },
  costCenters: { type: Array, default: () => [] },
});

const date = new Date().toISOString().split("T")[0];

// Inicializador de objetos
const initialJournal = {
  date,
  description: "",
  journalEntries: [],
  cost_center_id: "",
  is_deductible: false,
};

// Reactives
const journal = useForm({ ...initialJournal });
const journalEntry = reactive({
  account_id: 0,
  code: "",
  name: "",
  debit: "",
  have: "",
});
const errorForm = reactive({ ...initialJournal, date: "" });
const errorMessage = reactive({ totalMismatch: "" }); // Mensaje de error para totales

const resetErrorForm = () => {
  Object.assign(errorForm, initialJournal);
  errorMessage.totalMismatch = ""; // Limpiar mensaje de error
};

const eliminarCuenta = (index) => {
  journal.journalEntries.splice(index, 1);
};

const save = () => {
  if (props.costCenters.length > 0 && journal.cost_center_id === "") {
    errorForm.cost_center_id = "Seleccione un centro de costo.";
    return; // Salir sin guardar
  }
  // Verificar si los totales de "debe" y "haber" coinciden
  if (totalDebe.value !== totalHaver.value) {
    errorMessage.totalMismatch =
      "El total del Debe y el Haber no coinciden. Por favor verifica los valores.";
    return; // Salir sin guardar
  }

  journal.post(route("journal.store"), {
    onError: (errors) => {
      Object.keys(errors).forEach((key) => {
        errorForm[key] = errors[key];
      });
    },
  });
};

const selectAccount = (accountId) => {
  journalEntry.account_id = accountId;
};

const handleAddJournalEntry = (entry) => {
  journal.journalEntries.push(entry);
};

// Calcular totales de 'debe' y 'haber'
const totalDebe = computed(() => {
  const total = journal.journalEntries.reduce(
    (sum, entry) => sum + Number(entry.debit),
    0
  );
  return total.toFixed(2); // Devuelve el valor con dos decimales
});

const totalHaver = computed(() => {
  const total = journal.journalEntries.reduce(
    (sum, entry) => sum + Number(entry.have),
    0
  );
  return total.toFixed(2);
});

// Método para recibir el centro de costo seleccionado desde SearchCostCenter
const handleCostCenterSelect = (costCenter) => {
  journal.cost_center_id = costCenter.id; // Asignar el centro de costo al objeto journal
};
</script>

<template>
  <AdminLayout title="Asientos Contables">
    <!-- Card -->
    <div class="p-4 bg-white rounded drop-shadow-md">
      <!-- Card Header -->
      <div class="flex justify-between items-center">
        <h2 class="text-sm sm:text-lg font-bold">Asiento</h2>
      </div>

      <!-- Formulario -->
      <div>
        <!-- Fecha y Descripción -->
        <TextInput
          v-model="journal.date"
          type="date"
          class="mt-2 block w-full"
        />

        <TextInput
          v-model="journal.description"
          type="text"
          placeholder="Descripcion"
          class="mt-2 block w-full"
        />
        <InputError :message="errorForm.description" class="mt-2" />

        <label class="mt-6 block">
          <input type="checkbox" v-model="journal.is_deductible" class="mr-2" />
          ¿Es deducible?
        </label>
        <br />

        <select
          v-if="costCenters.length > 0 && costCenters.length <= 5"
          v-model="journal.cost_center_id"
          class="mt-2 block w-full rounded"
        >
          <option value="">Seleccione</option>
          <option
            v-for="costCenter in costCenters"
            :key="costCenter.id"
            :value="costCenter.id"
          >
            {{ costCenter.name }}
          </option>
        </select>

        <div v-else-if="costCenters.length > 5">
          <h3 class="mt-4">Centros de Costo</h3>
          <SearchCostCenter
            :costCenters="costCenters"
            @selectCostCenter="handleCostCenterSelect"
          />
        </div>

        <InputError :message="errorForm.cost_center_id" class="mt-2" />
        <!-- Cuentas -->
        <h3 class="mt-1">Cuentas</h3>
        <SearchAccount
          :accounts="accounts"
          :journal="journal"
          @addJournalEntry="handleAddJournalEntry"
        />
      </div>
      <!-- Tabla -->
      <table
        class="mt-4 text-xs sm:text-sm table-auto w-full text-center text-gray-700"
      >
        <thead>
          <tr>
            <th class="py-2">Código</th>
            <th class="py-2">Cuenta</th>
            <th class="py-2">Debe</th>
            <th class="py-2">Haber</th>
            <th class="py-2">Acciones</th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="(cuenta, index) in journal.journalEntries"
            :key="index"
            class="border-t hover:bg-gray-100"
          >
            <td class="py-2">{{ cuenta.code }}</td>
            <td class="py-2">{{ cuenta.name }}</td>
            <td class="py-2">{{ cuenta.debit }}</td>
            <td class="py-2">{{ cuenta.have }}</td>
            <td class="py-2">
              <button
                @click="eliminarCuenta(index)"
                class="text-red-500 hover:text-red-700"
              >
                Eliminar
              </button>
            </td>
          </tr>
        </tbody>
        <tfoot>
          <tr>
            <th colspan="2">TOTAL</th>
            <th>{{ totalDebe }}</th>
            <th>{{ totalHaver }}</th>
            <th></th>
          </tr>
        </tfoot>
      </table>

      <!-- Mensaje de error para totales -->
      <p v-if="errorMessage.totalMismatch" class="text-red-500 mt-4">
        {{ errorMessage.totalMismatch }}
      </p>

      <div class="mt-4 text-right">
        <button @click="save" class="px-4 py-2 bg-blue-500 text-white rounded">
          Guardar
        </button>
      </div>
    </div>
  </AdminLayout>
</template>
