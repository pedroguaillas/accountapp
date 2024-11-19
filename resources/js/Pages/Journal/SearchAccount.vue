<script setup>
import { ref, watch } from "vue";
import AccountSelectModal from "./AccountSelectModal.vue";

// Props
defineProps({
  accounts: { type: Array, default: () => [] },
  journal: { type: Object, default: () => ({}) },
});

// Refs
const accountId = ref(0);
const code = ref("");
const search = ref("");
const debit = ref("");
const have = ref("");
const modal = ref(false);

// Método para alternar la visibilidad de la modal
const toggleModal = () => {
  modal.value = !modal.value;
};

// Método para recibir la cuenta seleccionada
const handleAccountSelect = (account) => {
  accountId.value = account.id;
  search.value = account.name;
  code.value = account.code;
};

// Método para agregar una cuenta al diario
const agregarCuenta = () => {
  // console.log(typeof debit);
  // console.log(typeof debit.value);
  // console.log(typeof have.value);
  if (accountId.value === 0) {
    alert("No se ha seleccionado la cuenta.");
    return; // Salir sin guardar
  }
  if (typeof debit.value === "number" || typeof have.value === "number") {
    if (typeof debit.value === "number" && debit.value > 0) {
      have.value = 0;
    } else if (typeof have.value === "number" && have.value > 0) {
      debit.value = 0;
    } else {
      return;
    }

    const journalEntry = {
      account_id: accountId.value,
      code: code.value,
      name: search.value,
      debit: parseFloat(debit.value),
      have: parseFloat(have.value),
    };

    // Emitimos el objeto journalEntry al componente principal
    emit("addJournalEntry", journalEntry);

    // Limpiar los campos después de agregar
    code.value = "";
    search.value = "";
    debit.value = "";
    have.value = "";
  }else
  {
    alert("Ingresar almenos un valor para el DEBE o el HABER");
  }
};

const emit = defineEmits(["selectAccount", "addJournalEntry"]);

// Watchers para ajustar automáticamente el valor de "debit" o "have"
// watch(debit, (newValue) => {
//   if (newValue > 0) {
//     have.value = 0; // Si hay un valor en "debit", "have" se pone en 0
//   }
// });

// watch(have, (newValue) => {
//   if (newValue > 0) {
//     debit.value = 0; // Si hay un valor en "have", "debit" se pone en 0
//   }
// });
</script>

<template>
  <div class="flex mt-2">
    <!-- Campo para el código -->
    <div
      class="block w-full border border-gray-300 rounded-l px-4 py-2 focus:outline-none"
    >
      {{ code }}
    </div>
    <!-- Campo para la descripción -->
    <input
      v-model="search"
      type="text"
      class="block w-full border border-gray-300 rounded-l px-4 py-2 focus:outline-none"
      placeholder="Buscar..."
    />
    <button
      @click="toggleModal"
      class="bg-slate-500 text-white px-4 py-2 hover:bg-slate-600 focus:outline-none"
    >
      @
    </button>

    <!-- Campo para "Debe" -->
    <input
      type="number"
      v-model.number="debit"
      placeholder="Debe"
      class="block w-full border border-gray-300 rounded-l px-4 py-2 focus:outline-none"
    />
    <!-- Campo para "Haber" -->
    <input
      type="number"
      v-model.number="have"
      placeholder="Haber"
      class="block w-full border border-gray-300 rounded-l px-4 py-2 focus:outline-none"
    />
    <button
      type="button"
      @click="agregarCuenta"
      class="bg-blue-500 text-white rounded-r px-4 py-2 hover:bg-blue-600 focus:outline-none"
    >
      Agregar
    </button>
  </div>

  <!-- Modal de Selección de Cuenta -->
  <AccountSelectModal
    :show="modal"
    :accounts="accounts"
    @close="toggleModal"
    @selectAccount="handleAccountSelect"
  />
</template>
