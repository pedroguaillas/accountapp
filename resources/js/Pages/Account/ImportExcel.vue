<script setup lang="ts">

// Imports
import { ArrowUpTrayIcon } from '@heroicons/vue/24/outline'; // Icono Upload
import { router } from '@inertiajs/vue3';
import { ref } from 'vue';

const fileInput = ref<HTMLInputElement | null>(null); // Referencia al input de archivo

const handleClick = () => {
    if (fileInput.value) {
        fileInput.value.click(); // Programáticamente hace clic en el input de archivo
    } // Programáticamente hace clic en el input de archivo
}

const handleFileChange = (event: Event) => {
    const target = event.target as HTMLInputElement;
    const files = target.files;
    if (files && files.length > 0) {
        submit(files[0]);
    }
}

const submit = (file: File) => {
    router.post(`accounts/import`, { file }, {
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