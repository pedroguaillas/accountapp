<script setup>
// Imports
import { ref, reactive } from "vue";
import AdminLayout from "@/Layouts/AdminLayout.vue";
import { router, Link } from "@inertiajs/vue3";
import axios from "axios";

// Props
defineProps({
  journals: { type: Array, default: () => [] },
});

// Refs

// Inicializador de objetos
const initialJournal = { date: "", description: "" };

// Reactives
const journal = reactive({ ...initialJournal });
const errorForm = reactive({ ...initialJournal });

const resetErrorForm = () => {
  Object.assign(errorForm, initialJournal);
};

const save = () => {
  if (journal.id === undefined) {
    axios
      .post(route("journal.store"), journal)
      .then(() => {
        resetErrorForm();
        router.reload({ only: ["journals"] });
      })
      .catch((error) => {
        resetErrorForm();
        Object.keys(error.response.data.errors).forEach((key) => {
          errorForm[key] = error.response.data.errors[key][0];
        });
      });
  } else {
    axios
      .put(route("journal.update", journal.id), journal)
      .then(() => {
        resetErrorForm();
        router.reload({ only: ["journals"] });
      })
      .catch((error) => {
        resetErrorForm();
        Object.keys(error.response.data.errors).forEach((key) => {
          errorForm[key] = error.response.data.errors[key][0];
        });
      });
  }
};

const update = (journalEdit) => {
  resetErrorForm();
  Object.keys(journalEdit).forEach((key) => {
    journal[key] = journalEdit[key];
  });
};

const removeJournal = (journalId) => {
  if (confirm("¿Estás seguro de que deseas eliminar el asiento?")) {
    axios
      .delete(route("journal.delete", journalId))
      .then(() => {
        router.reload({ only: ["journals"] });
      })
      .catch((error) => {
        console.error("Error al eliminar el asiento", error);
      });
  }
};
</script>

<template>
  <AdminLayout title="Asientos Contables">
    <div class="flex justify-end mb-3">
      <Link
        :href="route('journal.create')"
        class="px-2 bg-green-500 dark:bg-green-600 text-2xl text-white rounded font-bold"
      >
        +
      </Link>
    </div>

    <!-- Card -->
    <div
      v-for="journal in journals"
      :key="journal.id"
      class="p-4 bg-white rounded drop-shadow-md mb-3"
    >
      <!-- Card Header -->
      <div class="items-center">
        <h2 class="text-sm sm:text-lg">
          <strong class="font-bold">Descripción:</strong>
          {{ journal.description }}
        </h2>

        <p>
          <strong class="font-bold">Fecha:</strong>
          {{ new Date(journal.date).toLocaleDateString("es-EC") }}
        </p>
      </div>

      <!-- Responsive -->
      <div class="w-full overflow-x-auto">
        <!-- Tabla -->
        <table
          class="mt-4 text-xs sm:text-sm table-auto w-full text-center text-gray-700"
        >
          <thead>
            <tr>
              <th class=" text-left w-24">Código</th>
              <th class="text-left ">Nombre de la Cuenta</th>
              <th class="text-right w-24">Débito</th>
              <th class="text-right w-24">Crédito</th>
            </tr>
          </thead>

          <tbody class="h-6">
            <tr
              v-for="(entry, j) in journal.journal_entries"
              :key="`journal-${i}-entry-${j}`"
            >
              <td class="text-left">{{ entry.code }}</td>
              <td class="text-left">{{ entry.name }}</td>
              <td class="text-right ">{{ entry.debit }}</td>
              <td class="text-right ">{{ entry.have }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </AdminLayout>
</template>
