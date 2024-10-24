<script setup>

// Imports
import { ref, reactive } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import FormModal from './FormModalJournal.vue';
import { router } from '@inertiajs/vue3';
import axios from 'axios';

// Props
defineProps({
    branches: { type: Array, default: () => [] },
})

// Refs
const modal = ref(false);

// Inicializador de objetos
const initialBranch = { number: '', name: '', city: '', address: '', is_matriz: false, enviroment_type: '' };

// Reactives
const branch = reactive({ ...initialBranch });
const errorForm = reactive({ ...initialBranch });

const newBranch = () => {
    if (branch.id !== undefined) {
        delete branch.id;
    }
    Object.assign(branch, initialBranch);
    toggle();
}

const resetErrorForm = () => {
    Object.assign(errorForm, initialBranch);
}

const toggle = () => {
    modal.value = !modal.value;
}

const save = () => {
    if (branch.id === undefined) {
        axios.post(route('branch.store'), branch)
            .then(() => {
                toggle();
                resetErrorForm();
                router.reload({ only: ['branches'] });
            }).catch(error => {
                resetErrorForm();
                Object.keys(error.response.data.errors).forEach(key => {
                    errorForm[key] = error.response.data.errors[key][0];
                });
            });
    } else {
        axios.put(route('branch.update', branch.id), branch)
            .then(() => {
                toggle();
                resetErrorForm();
                router.reload({ only: ['branches'] });
            }).catch(error => {
                resetErrorForm();
                Object.keys(error.response.data.errors).forEach(key => {
                    errorForm[key] = error.response.data.errors[key][0];
                });
            });
    }
}

const update = (branchEdit) => {
    resetErrorForm();
    Object.keys(branchEdit).forEach(key => {
        branch[key] = branchEdit[key];
    });
    toggle();
}

const removeBranch = (branchId) => {
    if (confirm("¿Estás seguro de que deseas eliminar esta sucursal?")) {
        axios.delete(route('branch.delete', branchId))
            .then(() => {
                router.reload({ only: ['branches'] });
            })
            .catch(error => {
                console.error("Error al eliminar la sucursal", error);
            });
    }
}

</script>

<template>
    <AdminLayout title="Sucursales / Establecimientos">

        <!-- Card -->
        <div class="p-4 bg-white rounded drop-shadow-md">

            <!-- Card Header -->
            <div class="flex justify-between items-center">
                <h2 class="text-sm sm:text-lg font-bold">Sucursales / Establecimientos</h2>
                <button @click="newBranch"
                    class="px-2 bg-green-500 dark:bg-green-600 text-2xl text-white rounded font-bold">
                    +
                </button>
            </div>

            <!-- Responsive -->
            <div class="w-full overflow-x-auto">
                <!-- Tabla -->
                <table class="mt-4 text-xs sm:text-sm table-auto w-full text-center text-gray-700">
                    <thead>
                        <tr class="[&>th]:py-2">
                            <th class="w-1">N°</th>
                            <th>N° Establecimiento</th>
                            <th>Nombre</th>
                            <th>Ciudad</th>
                            <th>Dirección</th>
                            <th>Matriz o Sucursal</th>
                            <th>Ambiente</th>
                            <th class="w-1"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="branch, i in branches" :key="branch.id" class="border-t [&>td]:py-2">
                            <td>{{ i + 1 }}</td>
                            <td>{{ branch.number }}</td>
                            <td>{{ branch.name }}</td>
                            <td>{{ branch.city }}</td>
                            <td>{{ branch.address }}</td>
                            <td>{{ branch.is_matriz ? 'Matriz' : 'Sucursal' }}</td>
                            <td>{{ branch.enviroment_type === 1 ? 'Prueba' : 'Producción' }}</td>
                            <td>
                                <div class="relative inline-flex">
                                    <button class="rounded px-2 py-1 bg-red-500 text-white" @click="removeBranch(branch.id)">
                                        <i class="fa fa-trash"></i> Eliminar
                                    </button>
                                    <button class="rounded px-2 py-1 bg-blue-500 text-white" @click="update(branch)">
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

    <FormModal :show="modal" :branch="branch" :error="errorForm" @close="toggle" @save="save" />
</template>
