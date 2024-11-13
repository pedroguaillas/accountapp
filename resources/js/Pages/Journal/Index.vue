<script setup>

// Imports
import { ref, reactive } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { router,Link } from '@inertiajs/vue3';
import axios from 'axios';

// Props
defineProps({
    journals: { type: Array, default: () => [] },
})

// Refs

// Inicializador de objetos
const initialJournal = { date: '',description: '' };

// Reactives
const journal = reactive({ ...initialJournal });
const errorForm = reactive({ ...initialJournal });


const resetErrorForm = () => {
    Object.assign(errorForm, initialJournal);
}

const save = () => {
    if (journal.id === undefined) {
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
    } else {
        axios.put(route('journal.update', journal.id), journal)
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
}

const update = (journalEdit) => {
    resetErrorForm();
    Object.keys(journalEdit).forEach(key => {
        journal[key] = journalEdit[key];
    });
}

const removeJournal = (journalId) => {
    if (confirm("¿Estás seguro de que deseas eliminar el asiento?")) {
        axios.delete(route('journal.delete', journalId))
            .then(() => {
                router.reload({ only: ['journals'] });
            })
            .catch(error => {
                console.error("Error al eliminar el asiento", error);
            });
    }
}



</script>

<template>
    <AdminLayout title="Asientos Contables">

        <!-- Card -->
        <div class="p-4 bg-white rounded drop-shadow-md">

            <!-- Card Header -->
            <div class="flex justify-between items-center">
                <h2 class="text-sm sm:text-lg font-bold">Asientos Contables</h2>
                <Link :href="route('journal.create')"
                    class="px-2 bg-green-500 dark:bg-green-600 text-2xl text-white rounded font-bold">
                    +
            </Link>
            </div>

            <!-- Responsive -->
            <div class="w-full overflow-x-auto">
                <!-- Tabla -->
                <table class="mt-4 text-xs sm:text-sm table-auto w-full text-center text-gray-700">
                    <thead>
                        <tr class="[&>th]:py-2">
                            <th class="w-1">N°</th>
                            <th>Fecha</th>
                            <th>Descripcion</th>
                       
                            <th class="w-1"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="journal, i in journals" :key="journal.id" class="border-t [&>td]:py-2">
                            <td>{{ i + 1 }}</td>
                            <td>{{journal.date }}</td>
                            <td>{{journal.description }}</td>
                         <td>
                                <div class="relative inline-flex">
                                    <button class="rounded px-2 py-1 bg-red-500 text-white" @click="removeJournal(journal.id)">
                                        <i class="fa fa-trash"></i> Eliminar
                                    </button>
                                    <button class="rounded px-2 py-1 bg-blue-500 text-white" @click="update(journal)">
                                        <i class="fa fa-edit"></i> Modificar
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

    </AdminLayout>
</template>
