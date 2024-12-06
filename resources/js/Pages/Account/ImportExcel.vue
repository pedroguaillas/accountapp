<script setup>

// Imports
import { ArrowUpTrayIcon } from '@heroicons/vue/24/outline'; // Icono Upload
import { router } from '@inertiajs/vue3';
import { ref } from 'vue';

const fileInput = ref(null); // Referencia al input de archivo

const handleClick = () => {
    fileInput.value.click(); // Programáticamente hace clic en el input de archivo
}

const handleFileChange = (event) => {
    const files = event.target.files;
    if (files.length > 0) submit(files[0])
}

const submit = (file) => {
    router.post(`accounts/import`, { file }, {
        onSuccess: () => {
            console.log('Bien')
        },
        onError: (err) => {
            alert(err)
        }
    })
}

</script>

<template>
    <button @click="handleClick"
        class="px-2 bg-green-500 dark:bg-green-600 hover:bg-green-700 text-2xl text-white rounded">
        <ArrowUpTrayIcon class="w-6 h-6 my-1 stroke-[2px]" />
    </button>
    <input ref="fileInput" @change="handleFileChange" type="file" hidden id="file" />

</template>