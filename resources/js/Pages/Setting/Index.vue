<script setup>

// Imports
import Table from "@/Components/Table.vue";
import AdminLayout from "@/Layouts/AdminLayout.vue";
import ModalSelectAccount from "./ModalSelectAccount.vue";
import { MagnifyingGlassIcon } from "@heroicons/vue/24/solid";
import { Link, router } from "@inertiajs/vue3";
import { ref } from "vue";

// Props
const props = defineProps({
    activeTypes: { type: Array, default: () => ([]) },
    accounts: { type: Array, default: () => ([]) },
    payMethods: { type: Array, default: () => ([]) },
});

// Refs
const modal = ref(true);
const activeTypeId = ref(0)
const payMethodId = ref(0)
const accounts = ref(props.accounts)

const toggle = () => {
    modal.value = !modal.value
}

const editActiveType = (activeTypeIdEdit) => {
    accounts.value = Array.isArray(props.accounts) ? props.accounts.filter(acc => acc.code.startsWith('1.2')) : []
    activeTypeId.value = activeTypeIdEdit
    toggle()
}

const editPayMethod = (payMethodIdEdit) => {
    accounts.value = Array.isArray(props.accounts) ? props.accounts.filter(acc => acc.code.startsWith('1.2')) : []
    payMethodId.value = payMethodIdEdit
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
                <h2 class="text-sm sm:text-lg font-bold">
                    Vinculación de cuentas
                </h2>
            </div>

            <h3 class="mt-2">Activos fijos</h3>
            <hr />

            <Table>
                <thead>
                    <tr>
                        <th class="w-10">N.</th>
                        <th class="text-left">DESCRIPCION</th>
                        <th>CUENTA</th>
                    </tr>
                </thead>
                <tbody class="h-6">
                    <tr v-for="(activeType, i) in props.activeTypes" :key="`activeType-${i}`">
                        <td>{{ i + 1 }}</td>
                        <td class="text-left">{{ activeType.name }}</td>
                        <td>
                            <div class="flex h-8">
                                <input type="text" :value="activeType.account_info ?? ''"
                                    class="block w-full rounded-l border border-gray-300 px-4 py-2 focus:outline-none" />
                                <button @click="editActiveType(activeType.id)"
                                    class="bg-slate-500 rounded-r text-white px-3 py-2 hover:bg-slate-600 focus:outline-none">
                                    <MagnifyingGlassIcon class="size-4 text-white stroke-[3px]" />
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </Table>

            <h3 class="mt-2">Métodos de pago</h3>
            <hr />

            <Table>
                <thead>
                    <tr>
                        <th class="w-10">N.</th>
                        <th class="text-left">DESCRIPCION</th>
                        <th>CUENTA</th>
                    </tr>
                </thead>
                <tbody class="h-6">
                    <tr v-for="(payMethod, i) in props.payMethods" :key="`payMethod-${i}`">
                        <td>{{ i + 1 }}</td>
                        <td class="text-left">{{ payMethod.name }}</td>
                        <td>
                            <div class="flex h-8">
                                <input type="text"
                                    class="block w-full rounded-l border border-gray-300 px-4 py-2 focus:outline-none" />
                                <button @click="editPayMethod(payMethod.id)"
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
    <ModalSelectAccount :show="modal" :accounts="accounts" @close="toggle" @selectAccount="selectAccount" />
</template>
