<script setup>

// Imports
import DialogModal from '@/Components/DialogModal.vue';
import InputError from '@/Components/InputError.vue';
import TextInput from '@/Components/TextInput.vue';

// Props
defineProps({
    user: { type: Object, default: () => ({}) },
    error: { type: Object, default: () => ({}) },
    show: { type: Boolean, default: false }
});

// Emits
defineEmits(['close', 'save'])

</script>

<template>
    <DialogModal :show="show" maxWidth="lg" @close="$emit('close')">
        <template #title>
            {{ `${user.id === undefined ? 'Añadir' : 'Editar'} centro de costo` }}
        </template>
        <template #content>
            <div class="mt-4">
                <form class="w-2xl">

                    <TextInput v-model="user.name" type="text" class="mt-2 block w-full" placeholder="Nombres"
                        minlength="3" maxlength="50" />
                    <InputError :message="error.name" class="mt-2" />

                    <TextInput v-model="user.email" type="email" class="mt-2 block w-full"
                        placeholder="Correo electrónico" minlength="4" maxlength="50" />
                    <InputError :message="error.email" class="mt-2" />

                    <TextInput v-model="user.username" type="text" class="mt-2 block w-full" placeholder="Usuario"
                        minlength="6" maxlength="50" />
                    <InputError :message="error.username" class="mt-2" />

                    <TextInput v-model="user.password" type="password" class="mt-2 block w-full"
                        placeholder="Contraseña" minlength="6" maxlength="50" />
                    <InputError :message="error.password" class="mt-2" />

                </form>
            </div>
        </template>
        <template #footer>
            <button @click="$emit('save')"
                class="px-6 py-2 ml-2 bg-blue-600 dark:bg-blue-600 text-blue-100 dark:text-blue-200 rounded">Guardar</button>
        </template>
    </DialogModal>
</template>