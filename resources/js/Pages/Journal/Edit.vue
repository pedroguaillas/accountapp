<script setup lang="ts">

// Imports
import { reactive, computed } from "vue";
import AdminLayout from "@/Layouts/AdminLayout.vue";
import SearchAccount from "./SearchAccount.vue";
import SearchCostCenter from "./SearchCostCenter.vue";
import { useForm } from "@inertiajs/vue3";
import { Checkbox, DynamicSelect, InputError, InputLabel,TextInput, Table } from "@/Components";
import { Account, CostCenter, Errors, Journal, JournalEntry } from "@/types";

// Props
const props = defineProps<{
  costCenters: CostCenter[]; // Paginación de los bancos
  accounts: Account[];
  journal: Journal;
}>();

// Inicializador de objetos reactivos
const journal = useForm<Journal>({ ...props.journal }); // Precarga los datos del asiento contable
const errorForm: Record<string, string> = {};

// Métodos
const eliminarCuenta = (index: number) => {
  journal.journalEntries.splice(index, 1);
};

const save = () => {
  // Validar centro de costos si es necesario
  if (props.costCenters.length > 0 && !journal.cost_center_id) {
    errorForm.cost_center_id = "Seleccione un centro de costo.";
    return; // Salir sin guardar
  }
  // Validar si los totales de "Debe" y "Haber" coinciden
  if (totalDebe.value !== totalHaver.value) {
    errorForm.totalMismatch = "El total del Debe y el Haber deben ser iguales.";
    return; // Salir sin guardar
  }

  journal.put(route("journal.update", journal.id), {
    onError: (errors: Errors) => {
      Object.keys(errors).forEach((key) => {
        errorForm[key] = (errors[key] as string[])[0];
      });
    },
  });
};

const handleAddJournalEntry = (entry: JournalEntry) => {
  journal.journalEntries.push(entry);
};

// Método para recibir el centro de costos seleccionado desde SearchCostCenter
const handleCostCenterSelect = (costCenter: CostCenter) => {
  journal.cost_center_id = costCenter.id;
};

const costcenterOptions = props.costCenters.map((costCenter) => ({
  value: costCenter.id ?? 0,
  label: costCenter.name,
}));

// Cálculo de totales para "Debe" y "Haber"
const totalDebe = computed(() => {
  return journal.journalEntries.reduce(
    (total:number, entry:JournalEntry) => total + parseFloat(String(entry.debit || 0)),
    0
  );
});

const totalHaver = computed(() => {
  return journal.journalEntries.reduce(
    (total:number, entry:JournalEntry) => total + parseFloat(String(entry.have || 0)),
    0
  );
});

</script>

<template>
  <AdminLayout title="Editar Asiento Contable">
    <div class="p-4 bg-white rounded drop-shadow-md">
      <div class="flex justify-between items-center">
        <h2 class="text-sm sm:text-lg font-bold">Editar Asiento Contable</h2>
      </div>

      <!-- Formulario -->
      <div class="mt-4">

        <!-- Campos del Journal -->
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
            <TextInput v-model="journal.description" type="text" class="mt-2 block w-full" />
            <InputError :message="errorForm.description" class="mt-2" />
          </div>

          <!-- Centro de costos -->
          <div v-if="costCenters.length > 0" class="col-span-6 sm:col-span-4">
            <InputLabel for="cost_center_id" value="Centro de Costos" />
            <DynamicSelect v-if="costCenters.length <= 5" class="mt-2 block w-full" v-model="journal.cost_center_id"
              :options="costcenterOptions" />
            <SearchCostCenter v-else-if="costCenters.length > 5" :costCenters="costCenters"
              @selectCostCenter="handleCostCenterSelect" />
            <InputError :message="errorForm.cost_center_id" class="mt-2" />
          </div>

          <!-- Es deducible -->
          <div class="col-span-6 sm:col-span-4">
            <div class="w-full sm:w-[50%]">
              <Checkbox v-model:checked="journal.is_deductible" label="¿Es deducible?" />
            </div>
          </div>
        </div>

        <!-- Cuentas -->
        <h3 class="font-bold mt-4">Cuentas</h3>
        <SearchAccount :accounts="accounts" :journal="journal" @addJournalEntry="handleAddJournalEntry" />

        <!-- Tabla -->
        <Table>
          <thead>
            <tr>
              <th class="py-2 text-left w-24">CÓDIGO</th>
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
              <td class="py-2 text-right w-24">{{ journalEntry.debit }}</td>
              <td class="py-2 text-right w-24">{{ journalEntry.have }}</td>
              <td class="py-2">
                <button @click="eliminarCuenta(index)" class="text-red-500 hover:text-red-700">
                  Eliminar
                </button>
              </td>
            </tr>
          </tbody>
          <tfoot>
            <tr>
              <th colspan="2">TOTAL</th>
              <th class="py-2 text-right">{{ totalDebe.toFixed(2) }}</th>
              <th class="py-2 text-right">{{ totalHaver.toFixed(2) }}</th>
              <th></th>
            </tr>
          </tfoot>
        </Table>

        <InputError :message="errorForm.totalMismatch" class="mt-2" />

        <div class="mt-4 text-right">
          <button @click="save" :disabled="journal.processing"
            class="px-4 py-2 bg-primary hover:bg-primaryhover text-white rounded">
            Guardar Cambios
          </button>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>
