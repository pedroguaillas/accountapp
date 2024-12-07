<script setup>

// Imports
import Table from "@/Components/Table.vue";
import AdminLayout from "@/Layouts/AdminLayout.vue";
import ModalSelectAccount from "./ModalSelectAccount.vue";
import { MagnifyingGlassIcon } from "@heroicons/vue/24/solid";
import { Link, router } from "@inertiajs/vue3";
import { ref } from "vue";

// Props
defineProps({
    activeTypes: { type: Array, default: () => ([]) },
    accounts: { type: Array, default: () => ([]) },
});

// Refs
const modal = ref(true);
const activeTypeId = ref(0)

const toggle = () => {
    modal.value = !modal.value
}

const edit = (activeType) => {
    activeTypeId.value = activeType.id
    toggle()
}

const selectAccount = (accountId) => {
    router.put(
        route('settingaccount.update', activeTypeId.value),
        { account_id: accountId },
        { preserveState: true }
    )
    toggle()
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
                        <th class="w-10">N.</th>
                        <th class="text-left">DESCRIPCION</th>
                        <th>CUENTA</th>
                    </tr>
                </thead>
                <tbody class="h-6">
                    <tr v-for="(activeType, i) in activeTypes" :key="`activeType-${i}`">
                        <td>{{ i + 1 }}</td>
                        <td class="text-left">{{ activeType.name }}</td>
                        <td>
                            <div class="flex h-8">
                                <input type="text" :value="activeType.account_info ?? ''"
                                    class="block w-full rounded-l border border-gray-300 px-4 py-2 focus:outline-none" />
                                <button @click="edit(activeType)"
                                    class="bg-slate-500 rounded-r text-white px-3 py-2 hover:bg-slate-600 focus:outline-none">
                                    <MagnifyingGlassIcon class="size-4 text-white stroke-[3px]" />
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </Table>
        </div>
    </AdminLayout>
    <ModalSelectAccount :show="modal"
        :accounts="Array.isArray(accounts) ? accounts.filter(acc => acc.code.startsWith('1.2')) : []" @close="toggle"
        @selectAccount="selectAccount" />
</template>
