<script setup>

// Imports
import AdminLayout from '@/Layouts/AdminLayout.vue';
import ImportExcel from './ImportExcel.vue';
import { reactive } from 'vue';

//Props
defineProps({
    accounts: { type: Array, default: () => [] },
})

//El inicializador de objetos
const initialAccount = { file: '' }

// Reactives
const account = reactive({ ...initialAccount });

const importXsls = () => {
    alert('Para importar el plan de cuentas');
}

</script>

<template>
    <AdminLayout title="Plan de Cuentas">

        <!-- Card -->
        <div class="p-4 bg-white rounded drop-shadow-md">

            <!-- Card Header -->
            <div class="flex justify-between items-center">
                <h2 class="text-sm sm:text-lg font-bold">Plan de Cuentas</h2>
                <ImportExcel :form="account" />
            </div>

            <!-- Resposive -->
            <div class="w-full overflow-x-auto">
                <!-- Tabla -->
                <table class="mt-4 text-xs sm:text-sm table-auto w-full text-center text-gray-700">
                    <thead>
                        <tr class="[&>th]:py-2">
                            <th class="w-1">Cuenta</th>
                            <th>Descripción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="account in accounts" :key="account.id" class="border-t [&>td]:py-2">
                            <td>{{ account.code }}</td>
                            <td>{{ account.name }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

    </AdminLayout>
</template>