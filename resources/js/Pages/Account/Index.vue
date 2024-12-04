<script setup>

// Imports
import AdminLayout from '@/Layouts/AdminLayout.vue';
import ImportExcel from './ImportExcel.vue';
import Table from "@/Components/Table.vue";
import FormModal from "./FormModal.vue";
import { TrashIcon, PencilIcon } from "@heroicons/vue/24/solid";
import { ref } from "vue";

//Props
defineProps({
    accounts: { type: Array, default: () => [] },
})

// Refs
const modal = ref(false);
const account = ref({});
const errorForm = ref({});

const toggle = () => {
    modal.value = !modal.value;
};

const update = (account) => {
    console.log(account)
    toggle()
}

const remove = (account) => {
    console.log(account)
}

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
                <h2 class="text-sm sm:text-lg font-bold">Plan de cuentas</h2>
                <ImportExcel v-if="accounts.lenght === 0" />
            </div>

            <!-- Resposive -->
            <div class="w-full overflow-x-auto">
                <!-- Tabla -->
                <Table>
                    <thead>
                        <tr>
                            <th class="w-12 text-left">CUENTA</th>
                            <th class="text-left">DESCRIPCION</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="account in accounts" :key="account.id" class="border-t">
                            <td :class="styleAccount(account.code)" class="text-left">
                                {{ account.code }}
                            </td>
                            <td :class="styleAccount(account.code)" class="text-left">
                                {{ account.name }}
                            </td>
                            <td class="flex justify-end">
                                <div class="relative inline-flex gap-1">
                                    <button class="rounded px-1 py-1 bg-red-500 text-white" @click="remove(account)">
                                        <TrashIcon class="size-6 text-white" />
                                    </button>
                                    <button class="rounded px-2 py-1 bg-blue-500 text-white" @click="update(account)">
                                        <PencilIcon class="size-4 text-white" />
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </Table>
            </div>
        </div>

    </AdminLayout>

    <FormModal :show="modal" :account="account" :error="errorForm" @close="toggle" @save="save" />
</template>