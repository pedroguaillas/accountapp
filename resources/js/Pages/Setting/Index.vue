<script setup>

// Imports
import Table from "@/Components/Table.vue";
import AdminLayout from "@/Layouts/AdminLayout.vue";
import { Link } from "@inertiajs/vue3";
import ModalSelectAccount from "./ModalSelectAccount.vue";
import { ref } from "vue";

// Props
defineProps({
    activeTypes: { type: Array, default: () => ([]) },
    accounts: { type: Array, default: () => ([]) },
});

const modal = ref(true);

const toggle = () => {
    modal.value = !modal.value
}
</script>

<template>
    <AdminLayout title="Vinculación de cuentas">

        <!-- Card -->
        <div class="p-4 bg-white rounded drop-shadow-md mb-3">

            <!-- Card Header -->
            <div class="items-center">
                <h2 class="text-sm sm:text-lg">
                    Vinculación de cuentas
                </h2>
            </div>

            <Table>
                <thead>
                    <tr>
                        <th class="w-2">N.</th>
                        <th class="text-left">DESCRIPCION</th>
                        <th class="text-right w-24">CUENTA</th>
                    </tr>
                </thead>
                <tbody class="h-6">
                    <tr v-for="(activeType, i) in activeTypes" :key="`activeType-${i}`">
                        <td>{{ i + 1 }}</td>
                        <td class="text-left">{{ activeType.name }}</td>
                        <td>Cuenta</td>
                    </tr>
                </tbody>
            </Table>
        </div>
    </AdminLayout>
    <ModalSelectAccount :show="modal" />
</template>
