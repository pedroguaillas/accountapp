<script setup>

// Imports
import AdminLayout from "@/Layouts/AdminLayout.vue";
import Table from "@/Components/Table.vue";

// Props
const props = defineProps({
    depreciations: { type: Array, default: () => ([]) },
});

</script>

<template>
    <AdminLayout title="Depreciaciones">
        <!-- Card -->
        <div class="p-4 bg-white rounded drop-shadow-md">
            <!-- Card Header -->
            <div class="flex flex-col sm:flex-row justify-between items-center">
                <h2 class="text-sm sm:text-lg font-bold w-full pb-2 sm:pb-0">
                    Depreciaciones
                </h2>
            </div>

            <!-- Resposive -->
            <div class="w-full overflow-x-auto">
                <!-- Tabla -->
                <Table>
                    <thead>
                        <tr class="[&>th]:py-2">
                            <th class="w-1">N°</th>
                            <th>MONTO</th>
                            <th>VALOR</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(depreciation, i) in depreciations" :key="depreciation.id"
                            class="border-t [&>td]:py-2">
                            <td>{{ i + 1 }}</td>
                            <td>{{ depreciation.amount }}</td>
                            <td>{{ depreciation.value }}</td>
                        </tr>
                    </tbody>
                </Table>
            </div>
        </div>
    </AdminLayout>
</template>
