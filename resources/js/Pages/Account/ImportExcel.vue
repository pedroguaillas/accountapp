<script setup>

defineProps({
    form: { type: Object, default: () => ({}) }
})

const handleChange = () => {
    document.getElementById('file').click();
}

</script>

<template>
    <button @click="handleChange" class="px-2 bg-green-500 dark:bg-green-600 text-2xl text-white rounded font-bold">
        +
    </button>
    <input v-on:change="form.file" type="file" hidden id="file" />

</template>