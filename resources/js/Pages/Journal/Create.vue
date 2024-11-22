<script setup>
// Imports
import { reactive, computed } from "vue";
import AdminLayout from "@/Layouts/AdminLayout.vue";
import SearchAccount from "./SearchAccount.vue";
import SearchCostCenter from "./SearchCostCenter.vue";
import { useForm } from "@inertiajs/vue3";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import Table from "@/Components/Table.vue";
import DynamicSelect from "@/Components/DynamicSelect.vue";
import Checkbox from "@/Components/Checkbox.vue";

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

const costcenterOptions = props.costCenters.map((costCenter) => ({
  value: costCenter.id,
  label: costCenter.name,
}));
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
        <!-- atributos solo del journal -->

        <div class="grid grid-cols-1 gap-4">
          <!-- Fecha -->
          <div class="col-span-6 sm:col-span-4">
            <InputLabel for="date" value="Fecha" />
            <TextInput
              v-model="journal.date"
              type="date"
              class="mt-2 block w-full sm:w-[50%]"
            />
          </div>
          <!-- es deducible -->
          <div class="col-span-6 sm:col-span-4">
            <div class="w-full sm:w-[50%]">
              <Checkbox
                v-model:checked="journal.is_deductible"
                label="¿Es deducible?"
              />
            </div>
          </div>

          <!-- Descripción -->
          <div class="col-span-6 sm:col-span-4">
            <InputLabel for="description" value="Descripcion" />
            <TextInput
              v-model="journal.description"
              type="text"
              class="mt-2 block w-full sm:w-[50%]"
            />
            <InputError :message="errorForm.description" class="mt-2" />
          </div>
          <!-- centro de costos-->
          <div class="col-span-6 sm:col-span-4">
            <InputLabel for="cost_center_id" value="Centro de costos" />
            <DynamicSelect
              v-if="costCenters.length > 0 && costCenters.length <= 5"
              class="mt-2 block w-full sm:w-[50%]"
              v-model="journal.cost_center_id"
              :options="costcenterOptions"
              autofocus
            />
            <div v-else-if="costCenters.length > 5">
              <SearchCostCenter
                :costCenters="costCenters"
                @selectCostCenter="handleCostCenterSelect"
              />
            </div>
            <InputError :message="errorForm.cost_center_id" class="mt-2" />
          </div>
        </div>

        <!-- Cuentas -->

        <InputLabel for="accounts" value="Cuentas" />
        <SearchAccount
          :accounts="accounts"
          :journal="journal"
          @addJournalEntry="handleAddJournalEntry"
        />

        <!-- Tabla -->
        <Table>
          <thead>
            <tr>
              <th class="py-2 text-left w-24">Código</th>
              <th class="py-2 text-left">Cuenta</th>
              <th class="py-2 text-right w-24">Debe</th>
              <th class="py-2 text-right w-24">Haber</th>
              <th class="py-2 w-24">Acciones</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="(cuenta, index) in journal.journalEntries"
              :key="index"
              class="border-t hover:bg-gray-100"
            >
              <td class="py-2 text-left w-24">{{ cuenta.code }}</td>
              <td class="py-2 text-left">{{ cuenta.name }}</td>
              <td class="py-2 text-right w-24">{{ cuenta.debit }}</td>
              <td class="py-2 text-right w-24">{{ cuenta.have }}</td>
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
              <th class="py-2 text-right">{{ totalDebe }}</th>
              <th class="py-2 text-right">{{ totalHaver }}</th>
              <th></th>
            </tr>
          </tfoot>
        </Table>

        <!-- Mensaje de error para totales -->
        <p v-if="errorMessage.totalMismatch" class="text-red-500 mt-4">
          {{ errorMessage.totalMismatch }}
        </p>

        <div class="mt-4 text-right">
          <button
            @click="save"
            class="px-4 py-2 bg-blue-500 text-white rounded"
          >
            Guardar
          </button>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>
