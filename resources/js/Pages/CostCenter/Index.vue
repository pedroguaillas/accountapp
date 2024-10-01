<script setup>

// Imports
import { ref, reactive } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import ModalCostCenter from './ModalCostCenter.vue';
import axios from 'axios';

//Props
defineProps({
    costCenters: { type: Array, default: () => [] },
})

// Refs
const modal = ref(false);

const initialCostCenter = { name: '', code: '' }

// Reactives
const costCenter = reactive({ ...initialCostCenter });
const errorForm = reactive({ ...initialCostCenter });

const newCostCenter = () => {
    // Reinicio el formularios con valores vacios
    if (costCenter.id !== undefined) {
        delete costCenter.id
    }
    Object.assign(costCenter, initialCostCenter);
    // Muestro el modal
    toggle();
}

const resetErrorForm = () => {
    Object.assign(errorForm, initialCostCenter);
}

const toggle = () => {
    modal.value = !modal.value;
}

const save = () => {
    if (costCenter.id === undefined) {
        // const data = { ...club, category_id: props.category.id, group_id: club.group_id > 0 ? club.group_id : null, extra_points: club.extra_points === '' ? 0 : club.extra_points }
        axios
            .post(route('costcenter.store'), costCenter)
            .then(() => {
                toggle();
                resetErrorForm();

                router.reload({ only: ['costCenters'] });
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
    <AdminLayout title="Centro de costos">

        <!-- Card -->
        <div class="p-4 bg-white rounded drop-shadow-md">

            <!-- Card Header -->
            <div class="flex justify-between items-center">
                <h2 class="text-sm sm:text-lg font-bold">Centro de costos</h2>
                <button @click="newCostCenter"
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
                            <th>Código</th>
                            <th>Nombre</th>
                            <th>Tipo</th>
                            <th class="w-1"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="costCenter, i in costCenters" :key="costCenter.id" class="border-t [&>td]:py-2">
                            <td>{{ i + 1 }}</td>
                            <td>{{ costCenter.code }}</td>
                            <td>{{ costCenter.name }}</td>
                            <td>{{ costCenter.type }}</td>
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

    <ModalCostCenter :show="modal" :costCenter="costCenter" :error="errorForm" @close="toggle" @save="save" />
</template>