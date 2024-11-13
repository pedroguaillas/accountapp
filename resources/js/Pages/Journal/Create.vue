<script setup>

// Imports
import { ref, reactive } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import SearchAccount from './SearchAccount.vue';
import { router } from '@inertiajs/vue3';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import axios from 'axios';

// Props
defineProps({
    accounts: { type: Array, default: () => [] },
})

// Refs
const date = new Date().toISOString().split('T')[0];

// Inicializador de objetos
const initialJournal = { date, description: '', journalEntries: [] };

// Reactives
const journal = reactive({ ...initialJournal });
const journalEntry = reactive({ account_id: 0, code: '', name: '', debit: '', have: '' });
const errorForm = reactive({ ...initialJournal, date: '' });

const resetErrorForm = () => {
    Object.assign(errorForm, initialJournal);
}

const eliminarCuenta = (index) => {
    journal.journalEntries.splice(index, 1);
}

const save = () => {

    axios.post(route('journal.store'), journal)
        .then(() => {
            resetErrorForm();
            router.reload({ only: ['journals'] });
        }).catch(error => {
            resetErrorForm();
            Object.keys(error.response.data.errors).forEach(key => {
                errorForm[key] = error.response.data.errors[key][0];
            });
        });
}

const selectAccount = (accountId) => {
    //llamar a una funcion del create para emitir el account id
    journalEntry.account_id = accountId;
}

const handleAddJournalEntry = (entry) => {
    journal.journalEntries.push(entry);
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

                <TextInput v-model="journal.date" type="date" class="mt-2 block w-full" />
                <InputError :message="errorForm.date" class="mt-2" />

                <TextInput v-model="journal.description" type="text" placeholder="Descripcion"
                    class="mt-2 block w-full" />
                <InputError :message="errorForm.description" class="mt-2" />

                <!-- Cuentas -->
                <h3 class="mt-4">Cuentas</h3>
                <SearchAccount :accounts="accounts" :journal="journal" @addJournalEntry="handleAddJournalEntry" />
            </div>

            <!-- Tabla -->
            <table class="mt-4 text-xs sm:text-sm table-auto w-full text-center text-gray-700">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Cuenta</th>
                        <th>Debe</th>
                        <th>Haber</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(cuenta, index) in journal.journalEntries" :key="index">
                        <td>{{ cuenta.code }}</td>
                        <td>{{ cuenta.name }}</td> <!-- Cambié 'cuenta.account' por 'cuenta.name' -->
                        <td>{{ cuenta.debit }}</td>
                        <td>{{ cuenta.have }}</td>
                        <td>
                            <button @click="eliminarCuenta(index)">Eliminar</button>
                        </td>
                    </tr>
                </tbody>
            </table>

            <div>
                <button type="button" @click="save">GUARDAR</button>

            </div>
        </div>
    </AdminLayout>
</template>
