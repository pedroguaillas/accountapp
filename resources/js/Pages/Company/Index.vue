<script setup>

// Imports
import { ref, reactive } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import ModalCompany from './ModalCompany.vue';
import axios from 'axios';

//Props
defineProps({
    companies: { type: Array, default: () => [] },
})

// Refs
const modal = ref(false);

const initialCompany = { ruc: '', company: '', economic_activity: '', type: '' }

// Reactives
const company = reactive({ ...initialCompany });
const errorForm = reactive({ ...initialCompany });

const newCompany = () => {
    // Reinicio el formularios con valores vacios
    if (company.id !== undefined) {
        delete company.id
    }
    Object.assign(company, initialCompany);
    // Muestro el modal
    toggle();
}

const resetErrorForm = () => {
    Object.assign(errorForm, initialCompany);
}

const toggle = () => {
    modal.value = !modal.value;
}

const save = () => {
    if (company.id === undefined) {
        // const data = { ...club, category_id: props.category.id, group_id: club.group_id > 0 ? club.group_id : null, extra_points: club.extra_points === '' ? 0 : club.extra_points }
        axios
            .post(route('company.store'), company)
            .then(() => {
                toggle();
                resetErrorForm();

                router.reload({ only: ['companies'] });
            }).catch(error => {
                resetErrorForm();

                Object.keys(error.response.data.errors).forEach(key => {
                    errorForm[key] = error.response.data.errors[key][0]
                });
            })
    }
}

</script>

<template>
    <AdminLayout title="Negocios / RUCs">

        <!-- Card -->
        <div class="p-4 bg-white rounded drop-shadow-md">

            <!-- Card Header -->
            <div class="flex justify-between items-center">
                <h2 class="text-sm sm:text-lg font-bold">Negocios / RUCs</h2>
                <button @click="newCompany"
                    class="px-2 bg-green-500 dark:bg-green-600 text-2xl text-white rounded font-bold">
                    +
                </button>
            </div>

            <!-- Resposive -->
            <div class="w-full overflow-x-auto">
                <!-- Tabla -->
                <table class="mt-4 text-xs sm:text-sm table-auto w-full text-center text-gray-700">
                    <thead>
                        <tr class="[&>th]:py-2">
                            <th class="w-1">N°</th>
                            <th>RUC</th>
                            <th>Razon social</th>
                            <th class="w-1"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="company, i in companies" :key="company.id" class="border-t [&>td]:py-2">
                            <td>{{ i + 1 }}</td>
                            <td>{{ company.ruc }}</td>
                            <td>{{ company.company }}</td>
                            <td>
                                <div class="relative inline-flex [&>a>i]:text-white [&>button>i]:text-white">
                                    <button class="rounded px-2 py-1 bg-red-500 text-white">
                                        <i class="fa fa-trash"></i> Eliminar
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

    </AdminLayout>

    <ModalCompany :show="modal" :company="company" :error="errorForm" @close="toggle" @save="save" />
</template>