<script setup>

// Imports
import DialogModal from '@/Components/DialogModal.vue';
import InputError from '@/Components/InputError.vue';
import TextInput from '@/Components/TextInput.vue';

// Props
defineProps({
    company: { type: Object, default: () => ({}) },
    error: { type: Object, default: () => ({}) },
    show: { type: Boolean, default: false }
});

// Emits
defineEmits(['close', 'save'])

</script>

<template>
    <DialogModal :show="show" maxWidth="lg" @close="$emit('close')">
        <template #title>
            {{ `${company.id === undefined ? 'Añadir' : 'Editar'} RUC` }}
        </template>
        <template #content>
            <div class="mt-4">
                <form class="w-2xl">

                    <TextInput v-model="company.ruc" type="text" class="mt-2 block w-full" placeholder="RUC"
                        minlength="13" maxlength="13" />
                    <InputError :message="error.ruc" class="mt-2" />

                    <TextInput v-model="company.company" type="text" class="mt-2 block w-full"
                        placeholder="Razon social" minlength="3" maxlength="300" />
                    <InputError :message="error.company" class="mt-2" />

                    <TextInput v-model="company.economic_activity" type="text" class="mt-2 block w-full"
                        placeholder="Actividad economica" minlength="3" maxlength="50" />
                    <InputError :message="error.economic_activity" class="mt-2" />

                    <select v-model="company.type" class="mt-2 block w-full rounded">
                        <option value="">Seleccione</option>
                        <option value="Comercial">Comercial</option>
                        <option value="Servicio">Servicio</option>
                    </select>
                    <InputError :message="error.type" class="mt-2" />

                </form>
            </div>
        </template>
        <template #footer>
            <button @click="$emit('save')"
                class="px-6 py-2 ml-2 bg-blue-600 dark:bg-blue-600 text-blue-100 dark:text-blue-200 rounded">Guardar</button>
        </template>
    </DialogModal>
</template>