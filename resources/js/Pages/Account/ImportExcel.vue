<script setup>

import { router } from '@inertiajs/vue3';
import { ref } from 'vue';

// Define la referencia del archivo y el props
const fileInput = ref(null); // Referencia al input de archivo

const handleClick = () => {
    fileInput.value.click(); // Programáticamente hace clic en el input de archivo
}

const handleFileChange = (event) => {
    const files = event.target.files;
    if (files.length > 0) {
        // Aquí puedes manejar el archivo seleccionado
        submit(files[0])
        console.log('Archivo seleccionado:', files[0]);
        // Por ejemplo, podrías actualizar algún valor en el form
        // o ejecutar otra lógica
    }
}

const submit = (file) => {
    router.post(`accounts/import`, { file }, {
        onSuccess: () => {
            console.log('Bien')
        },
        onError: (err) => {
            console.log(err)
        }
    })
}

</script>

<template>
    <button @click="handleClick" class="px-2 bg-green-500 dark:bg-green-600 text-2xl text-white rounded font-bold">
        +
    </button>
    <input ref="fileInput" @change="handleFileChange" type="file" hidden id="file" />

</template>