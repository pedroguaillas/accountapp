<script setup>

// Imports
import AdminLayout from '@/Layouts/AdminLayout.vue';
import ImportExcel from './ImportExcel.vue';
import Table from "@/Components/Table.vue";
import FormModal from "./FormModal.vue";
import { useForm, router } from "@inertiajs/vue3";
import { TrashIcon, PencilIcon } from "@heroicons/vue/24/solid";
import { ref, reactive } from "vue";

//Props
defineProps({
    accounts: { type: Array, default: () => [] },
})

// Refs
const modal = ref(false);
const account = useForm({});
const errorForm = reactive({});

const toggle = () => {
    modal.value = !modal.value;
};

const edit = (accountEdit) => {
    Object.keys(accountEdit).forEach((key) => {
        account[key] = accountEdit[key];
    });
    toggle()
}

const save = () => {
    if (account.id) {
        update()
    }
}

const update = () => {
    account.put(route('account.update', account.id), {
        onSuccess: () => {
            toogle()
            router.reload(['accounts'])
        },
        onError: (errors) => {
            Object.keys(errors).forEach((key) => {
                errorForm[key] = errors[key];
            });
        },
    })
}

const remove = (account) => {
    console.log(account)
}

// Función para calcular la clase de estilo
const styleAccount = (code) => {
    let result = ''
    if (code.length === 1) {
        result = 'text-black';
    } else if (code.length < 4) {
        result = 'text-stone-600 indent-2';
    } else {
        result = 'text-stone-400 indent-4';
    }
    return result + ' text-left'
};

</script>

<template>
    <AdminLayout title="Plan de Cuentas">

        <!-- Card -->
        <div class="p-4 bg-white rounded drop-shadow-md">

            <!-- Card Header -->
            <div class="flex justify-between items-center">
                <h2 class="text-sm sm:text-lg font-bold">Plan de cuentas</h2>
                <ImportExcel v-if="accounts.length === 0" />
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
                        <tr v-for="account in accounts" :key="account.id" class="border-t"
                            :class="account.is_detail ? 'italic' : 'font-bold'">
                            <td :class="styleAccount(account.code)">
                                {{ account.code }}
                            </td>
                            <td :class="styleAccount(account.code)">
                                {{ account.name }}
                            </td>
                            <td class="flex justify-end">
                                <div v-if="account.code.length > 1" class="relative inline-flex gap-1">
                                    <button class="rounded px-1 py-1 bg-red-500 text-white" @click="remove(account)">
                                        <TrashIcon class="size-6 text-white" />
                                    </button>
                                    <button class="rounded px-2 py-1 bg-blue-500 text-white" @click="edit(account)">
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