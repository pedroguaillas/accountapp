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
import { TrashIcon } from "@heroicons/vue/24/outline";
import Checkbox from "@/Components/Checkbox.vue";

// Props
const props = defineProps({
  accounts: { type: Array, default: () => [] },
  costCenters: { type: Array, default: () => [] },
  countJournals: { type: Number, default: 0 },
});

const date = new Date().toISOString().split("T")[0];

// Inicializador de objetos
const initialJournal = {
  date,
  description: "",
  journalEntries: [],
  cost_center_id: 0,
  is_deductible: true,
};
 const cost_center_id  =
 props.  costCenters.length === 1 ? props.costCenters[0].id : 0;


// Reactives
const journal = useForm({ ...initialJournal,cost_center_id , description: props.countJournals === 0 ? "ASIENTO DE SITUACION INICIAL" : "" });
const errorForm = reactive({});

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
    errorForm.totalMismatch =
      "El total del Debe y el Haber deben ser iguales.";
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
        <h2 class="text-sm sm:text-lg font-bold">Registro de asiento contable</h2>
      </div>

      <!-- Formulario -->
      <div class="mt-4">

        <!-- Campos solo del journal -->
        <div class="grid grid-cols-1 gap-4 w-full sm:w-[50%]">

          <!-- Fecha -->
          <div class="col-span-6 sm:col-span-4">
            <InputLabel for="date" value="Fecha" />
            <TextInput v-model="journal.date" type="date" class="mt-2 block w-full" />
            <InputError :message="errorForm.date" class="mt-2" />
          </div>

          <!-- Descripción -->
          <div class="col-span-6 sm:col-span-4">
            <InputLabel for="description" value="Descripción" />
            <TextInput v-model="journal.description" type="text" class="mt-2 block w-full"
              :disabled="props.countJournals === 0" />
            <InputError :message="errorForm.description" class="mt-2" />
          </div>

          <!-- Centro de costos-->
          <div v-if="costCenters.length > 0" class="col-span-6 sm:col-span-4">
            <InputLabel for="cost_center_id" value="Centro de costo" />
            <DynamicSelect v-if="costCenters.length <= 5" class="mt-2 block w-full" v-model="journal.cost_center_id"
              :options="costcenterOptions" autofocus />
            <SearchCostCenter v-else-if="costCenters.length > 5" :costCenters="costCenters"
              @selectCostCenter="handleCostCenterSelect" />
            <InputError :message="errorForm.cost_center_id" class="mt-2" />
          </div>

          <!-- Es deducible -->
          <div class="col-span-6 sm:col-span-4">
            <div class="w-full">
              <Checkbox v-model:checked="journal.is_deductible" label="¿Es deducible?" />
            </div>
          </div>
        </div>

        <!-- Cuentas -->
        <h3 class="font-bold mt-4">Cuentas</h3>
        <SearchAccount :accounts="accounts" :journal="journal" @addJournalEntry="handleAddJournalEntry" />

        <!--Component Table -->
        <Table>
          <thead>
            <tr>
              <th class="py-2 text-left w-24">CODIGO</th>
              <th class="py-2 text-left">CUENTA</th>
              <th class="py-2 text-right w-24">DEBE</th>
              <th class="py-2 text-right w-24">HABER</th>
              <th class="py-2 w-24">ACCIONES</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(journalEntry, index) in journal.journalEntries" :key="index" class="border-t hover:bg-gray-100">
              <td class="py-2 text-left w-24">{{ journalEntry.code }}</td>
              <td class="py-2 text-left">{{ journalEntry.name }}</td>
              <td class="py-2 text-right w-24">{{ journalEntry.debit.toFixed(2) }}</td>
              <td class="py-2 text-right w-24">{{ journalEntry.have.toFixed(2) }}</td>
              <td class="py-2">
                <button @click="eliminarCuenta(index)" class="text-red-500 hover:text-red-700">
                  <TrashIcon class="size-6 text-red-500" />
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
        <InputError :message="errorForm.totalMismatch" class="mt-2" />

        <div class="mt-4 text-right">
          <button @click="save" :disabled="journal.processing" class="px-4 py-2 bg-primary hover:bg-primaryhover text-white rounded">
            Guardar
          </button>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>
