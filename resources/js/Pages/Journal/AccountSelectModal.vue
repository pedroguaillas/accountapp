<script setup>
// Imports
import DialogModal from '@/Components/DialogModal.vue';

// Props
defineProps({
    accounts: { type: Array, default: () => [] },
    show: { type: Boolean, default: false },
});

// Emits
const emit = defineEmits(['close', 'selectAccount']);

// Método para seleccionar cuenta
const handleSelectAccount = (account) => {
    emit('selectAccount', ({ ...account  }));
    emit('close');
};


</script>

<template>
    <DialogModal :show="show" maxWidth="lg" @close="close">
        <template #title>
            Seleccionar Cuenta
        </template>
        <template #content>
            <div class="mt-4 max-h-[300px] overflow-y-auto">
                <table class="mt-4 text-xs sm:text-sm table-auto w-full text-center text-gray-700">
                    <thead>
                        <tr class="[&>th]:py-2">
                            <th class="w-1">N°</th>
                            <th>Cuenta</th>
                            <th>Descripción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(account, i) in accounts" :key="account.id" class="border-t [&>td]:py-2 cursor-pointer" @click="handleSelectAccount(account)">
                            <td>{{ i + 1 }}</td>
                            <td>{{ account.code }}</td>
                            <td>{{ account.name }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </template>
    </DialogModal>
</template>
