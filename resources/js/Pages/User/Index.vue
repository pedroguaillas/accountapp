<script setup>

// Imports
import { ref, reactive } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import ModalUser from './ModalUser.vue';
import { router } from '@inertiajs/vue3';
import axios from 'axios';

//Props
defineProps({
    users: { type: Array, default: () => [] },
})

// Refs
const modal = ref(false);

const initialUser = { name: '', email: '', username: '', password: '' }

// Reactives
const user = reactive({ ...initialUser });
const errorForm = reactive({ ...initialUser });

const addUser = () => {
    // Reinicio el formularios con valores vacios
    if (user.id !== undefined) {
        delete user.id
    }
    Object.assign(user, initialUser);
    // Muestro el modal
    toggle();
}

const resetErrorForm = () => {
    Object.assign(errorForm, initialUser);
}

const toggle = () => {
    modal.value = !modal.value;
}

const save = () => {
    if (user.id === undefined) {
        // const data = { ...club, category_id: props.category.id, group_id: club.group_id > 0 ? club.group_id : null, extra_points: club.extra_points === '' ? 0 : club.extra_points }
        axios
            .post(route('customers.store'), user)
            .then(() => {
                toggle();
                resetErrorForm();

                router.reload({ only: ['users'] });
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
    <AdminLayout title="Lista de clientes">

        <!-- Card -->
        <div class="p-4 bg-white rounded drop-shadow-md">

            <!-- Card Header -->
            <div class="flex justify-between items-center">
                <h2 class="text-sm sm:text-lg font-bold">Clientes</h2>
                <button @click="addUser" class="px-2 bg-green-500 text-2xl text-white rounded font-bold">
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
                            <th>Nombre</th>
                            <th>Usuario</th>
                            <th>Correo electrónico</th>
                            <th class="w-1"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="user, i in users" :key="user.id" class="border-t [&>td]:py-2">
                            <td>{{ i + 1 }}</td>
                            <td>{{ user.name }}</td>
                            <td>{{ user.username }}</td>
                            <td>{{ user.email }}</td>
                            <td>
                                <div class="relative inline-flex [&>a>i]:text-white [&>button>i]:text-white">
                                    <button class="mx-1 rounded px-2 py-1 bg-blue-500 text-white">
                                        <i class="fa fa-edit"></i> Editar
                                    </button>
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

    <ModalUser :show="modal" :user="user" :error="errorForm" @close="toggle" @save="save" />
</template>