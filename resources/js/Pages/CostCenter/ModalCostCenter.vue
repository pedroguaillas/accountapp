<script setup>

// Imports
import DialogModal from '@/Components/DialogModal.vue';
import InputError from '@/Components/InputError.vue';
import TextInput from '@/Components/TextInput.vue';

// Props
defineProps({
    costCenter: { type: Object, default: () => ({}) },
    error: { type: Object, default: () => ({}) },
    show: { type: Boolean, default: false }
});

// Emits
defineEmits(['close', 'save'])

</script>

<template>
    <DialogModal :show="show" maxWidth="lg" @close="$emit('close')">
        <template #title>
            {{ `${costCenter.id === undefined ? 'Añadir' : 'Editar'} centro de costo` }}
        </template>
        <template #content>
            <div class="mt-4">
                <form class="w-2xl">

                    <TextInput v-model="costCenter.code" type="text" class="mt-2 block w-full" placeholder="Código"
                        minlength="3" maxlength="50" />
                    <InputError :message="error.code" class="mt-2" />

                    <TextInput v-model="costCenter.name" type="text" class="mt-2 block w-full"
                        placeholder="Nombre del centro de costo" minlength="3" maxlength="50" />
                    <InputError :message="error.name" class="mt-2" />

                    <select v-model="costCenter.type" class="mt-2 block w-full rounded">
                        <option value="">Seleccione</option>
                        <option value="T">Transaccional</option>
                        <option value="G">Global</option>
                    </select>
                    <InputError :message="error.type" class="mt-2" />

                    <select v-model="costCenter.state" class="mt-2 block w-full rounded dark:bg-slate-800">
                        <option value="">Seleccione</option>
                        <option value="A">Activo</option>
                        <option value="I">Inactivo</option>
                    </select>
                    <InputError :message="error.state" class="mt-2" />

                </form>
            </div>
        </template>
        <template #footer>
            <button @click="$emit('save')"
                class="px-6 py-2 ml-2 bg-blue-600 dark:bg-blue-600 text-blue-100 dark:text-blue-200 rounded">Guardar</button>
        </template>
    </DialogModal>
</template>