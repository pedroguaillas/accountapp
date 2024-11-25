<script setup>

// Imports
import AdminLayout from '@/Layouts/AdminLayout.vue';
import ImportExcel from './ImportExcel.vue';
import Table from "@/Components/Table.vue";

//Props
defineProps({
    accounts: { type: Array, default: () => [] },
})

// Función para calcular la clase de estilo
const styleAccount = (code) => {
    if (code.length === 1) {
        return 'text-black font-bold';
    } else if (code.length < 4) {
        return 'text-stone-600 font-semibold italic indent-2';
    } else {
        return 'text-stone-400 italic indent-4';
    }
};

</script>

<template>
    <AdminLayout title="Plan de Cuentas">

        <!-- Card -->
        <div class="p-4 bg-white rounded drop-shadow-md">

            <!-- Card Header -->
            <div class="flex justify-between items-center">
                <h2 class="text-sm sm:text-lg font-bold">Plan de Cuentas</h2>
                <ImportExcel />
            </div>

            <!-- Resposive -->
            <div class="w-full overflow-x-auto">
                <!-- Tabla -->
                <Table>
                    <thead>
                        <tr class="[&>th]:py-2">
                            <th class="w-12 text-left">CUENTA</th>
                            <th class="text-left">DESCRIPCION</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="account in accounts" :key="account.id" class="border-t [&>td]:py-2">
                            <td :class="styleAccount(account.code)" class="text-left">
                                {{ account.code }}
                            </td>
                            <td :class="styleAccount(account.code)" class="text-left">
                                {{ account.name }}
                            </td>
                        </tr>
                    </tbody>
                </Table>
            </div>
        </div>

    </AdminLayout>
</template>