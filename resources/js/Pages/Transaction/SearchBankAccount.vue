<script setup>
// Imports
import BankAccountSelectModal from "./BankAccountSelectModal.vue";
import { MagnifyingGlassIcon } from "@heroicons/vue/24/solid";
import { ref, computed } from "vue";

// Props
const props = defineProps({
  bankaccounts: { type: Array, default: () => [] },
});

// Refs
const modal = ref(false); // Estado del modal
const name = ref("");
const cuent = ref(""); // Asegurar que este campo almacene el número de cuenta
const isDropdownOpen = ref(false);

// Sugerencias filtradas según el texto ingresado
const filteredBankAccounts = computed(() =>
  props.bankaccounts.filter((bankaccount) =>
    bankaccount.name.toLowerCase().includes(name.value.toLowerCase())
  )
);

// Método para alternar la visibilidad del modal
const toggleModal = () => {
  modal.value = !modal.value;
};

// Método para recibir la cuenta bancaria seleccionada y actualizar los campos
const handleBankAccountSelect = (bankaccount) => {
  name.value = bankaccount.name;
  cuent.value = bankaccount.account_number; // Se actualiza el número de cuenta correctamente

  isDropdownOpen.value = false; // Cerrar el menú desplegable
  emit("selectBankAccount", bankaccount); // Emitir el evento con la cuenta seleccionada
};

// Control de eventos
const handleKey = () => {
  if (name.value.length > 0) {
    isDropdownOpen.value = true;
  }
};

const handleInputBlur = () => {
  setTimeout(() => (isDropdownOpen.value = false), 200); // Retraso para permitir clic en la sugerencia
};

const emit = defineEmits(["selectBankAccount"]);
</script>

<template>
  <div class="block w-full mt-2">
    <div class="flex">
      <!-- Campo para el código -->
     

      <!-- Campo para la cuenta -->
      <div class="w-[10em] border-y border-l border-gray-300 text-gray-500 px-4 py-2 focus:outline-none">
        {{ cuent || "Cuenta" }} <!-- Ahora muestra correctamente el número de cuenta -->
      </div>

      <!-- Campo para la descripción -->
      <div class="flex-1 relative">
        <input
          v-model="name"
          type="search"
          class="border w-full border-gray-300 px-4 py-2 focus:outline-none"
          placeholder="Buscar ..."
          @keydown="handleKey"
          @blur="handleInputBlur"
        />
        <ul
          v-if="isDropdownOpen && filteredBankAccounts.length"
          class="absolute z-10 bg-white border-b border-x border-gray-300 rounded-b w-full max-h-40 overflow-y-auto shadow-lg"
        >
          <li
            v-for="bankaccount in filteredBankAccounts"
            :key="bankaccount.id"
            @click="handleBankAccountSelect(bankaccount)"
            class="px-4 py-2 cursor-pointer hover:bg-gray-100"
          >
            {{ bankaccount.name }}
          </li>
        </ul>
      </div>

      <!-- Botón para abrir el modal -->
      <button
        @click="toggleModal"
        class="w-[3em] bg-slate-500 text-white px-3 py-2 rounded-r hover:bg-slate-600 focus:outline-none"
      >
        <MagnifyingGlassIcon class="size-6 text-white stroke-[3px]" />
      </button>
    </div>
  </div>

  <!-- Modal de Selección de Cuenta Bancaria -->
  <BankAccountSelectModal
    :show="modal"
    :bankaccounts="props.bankaccounts"
    @close="toggleModal"
    @selectBankAccount="handleBankAccountSelect"
  />
</template>
