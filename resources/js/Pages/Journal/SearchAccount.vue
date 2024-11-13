<script setup>
import { ref } from 'vue';
import AccountSelectModal from './AccountSelectModal.vue';

// Props
defineProps({
    accounts: { type: Array, default: () => [] },
    journal: { type: Object, default: () => ({}) },
    //journalEntry: { type: Object, default: () => ({}) },
});

// Refs
const accountId=ref(0);
const code = ref('');
const search = ref('');
const debit = ref('');
const have= ref('');
const modal = ref(false);

// Método para alternar la visibilidad de la modal
const toggleModal = () => {
    modal.value = !modal.value;
};

// Método para recibir la cuenta seleccionada
const handleAccountSelect = (account) => {
    accountId.value=account.id;
    // Establecemos el valor de la descripción en el input de búsqueda
    search.value = account.name;
    // Establecemos el código de la cuenta en el input de código
    code.value = account.code;
   // emit('selectAccount', { accountId: account.id });
};

const agregarCuenta = () => {
    if ((debit !== '' || have !== '')) {
        const journalEntry = {
            account_id: accountId.value,
            code: code.value,
            name: search.value,
            debit: debit.value,
            have: have.value
        };

        // Emitimos el objeto journalEntry al componente principal
        emit('addJournalEntry', journalEntry);

        // Limpiar los campos después de agregar
        code.value = '';
        search.value = '';
        debit.value = '';
        have.value = '';
    }
}




const emit = defineEmits(['selectAccount']);




</script>

<template>
    <div class="flex mt-2">
        <!-- Campo para el código -->
        <div class="block w-full border border-gray-300 rounded-l px-4 py-2 focus:outline-none">{{ code }}</div>
        <!-- Campo para la descripción -->
        <input v-model="search" type="text"
            class="block w-full border border-gray-300 rounded-l px-4 py-2 focus:outline-none"
            placeholder="Buscar..." />
        <button @click="toggleModal" class="bg-slate-500 text-white  px-4 py-2 hover:bg-slate-600 focus:outline-none">
            @
        </button>

        <input type="number" v-model="debit" placeholder="Debe"
            class="block w-full border border-gray-300 rounded-l px-4 py-2 focus:outline-none" min="0">
        <input type="number" v-model="have" placeholder="Haber"
            class="block w-full border border-gray-300 rounded-l px-4 py-2 focus:outline-none" min="0">
        <button type="button" @click="agregarCuenta"
            class="bg-blue-500 text-white rounded-r px-4 py-2 hover:bg-blue-600 focus:outline-none">Agregar</button>
    </div>

    <!-- Modal de Selección de Cuenta -->
    <AccountSelectModal :show="modal" :accounts="accounts" @close="toggleModal" @selectAccount="handleAccountSelect" />
</template>
