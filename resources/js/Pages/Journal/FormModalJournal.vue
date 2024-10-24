<script setup>

// Imports
import DialogModal from '@/Components/DialogModal.vue';
import InputError from '@/Components/InputError.vue';
import TextInput from '@/Components/TextInput.vue';

// Props
defineProps({
    journal: { type: Object, default: () => ({}) },
    error: { type: Object, default: () => ({}) },
    show: { type: Boolean, default: false },
});

// Emits
defineEmits(['close', 'save'])

</script>

<template>
    <DialogModal :show="show" maxWidth="lg" @close="$emit('close')">
        <template #title>
            {{ `${branch.id === undefined ? 'Añadir' : 'Editar'}Ajustes` }}
        </template>
        <template #content>
            <div class="mt-4">
                <form class="w-2xl">

                    <TextInput v-model="branch.number" type="number" class="mt-2 block w-full" 
                        placeholder="Referencia" min="1" max="999"/>
                    <InputError :message="error.number" class="mt-2" />

                    <TextInput v-model="branch.name" type="text" class="mt-2 block w-full"
                        placeholder="Nombre comercial o razon social" minlength="3" maxlength="300" />
                    <InputError :message="error.name" class="mt-2" />

                    <TextInput v-model="branch.city" type="text" class="mt-2 block w-full"
                        placeholder="Ciudad" minlength="3" maxlength="300" />
                    <InputError :message="error.city" class="mt-2" />

                    <TextInput v-model="branch.address" type="text" class="mt-2 block w-full"
                        placeholder="Dirección" minlength="3" maxlength="300" />
                    <InputError :message="error.address" class="mt-2" />


                    <input type="checkbox" v-model="branch.is_matriz"> Matriz
                    <br/>

                    <label>AMBIENTE</label>
                    <select v-model="branch.enviroment_type" class="mt-2 block w-full rounded">
                        <option value="1">PRUEBA</option>
                        <option value="2">PRODUCCION</option>
                    </select>

                </form>
            </div>
        </template>
        <template #footer>
            <button @click="$emit('save')"
                class="px-6 py-2 ml-2 bg-blue-600 dark:bg-blue-600 text-blue-100 dark:text-blue-200 rounded">Guardar</button>
        </template>
    </DialogModal>
</template>