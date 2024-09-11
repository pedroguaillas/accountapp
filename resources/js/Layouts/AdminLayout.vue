<script setup>

// Imports
import Header from './admin/Header.vue';
import Sidebar from './admin/Sidebar.vue';
import { Head } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

// Props
defineProps({
    title: String
});

// Refs
const divRef = ref(null);
const menu = ref(false);

const toggle = () => {
    menu.value = !menu.value;
};

watch(divRef, () => {
    if (divRef.value.getBoundingClientRect().width > 640) {
        toggle();
    }
    // divRef si altura del main mas altura del Header > h-screen ? h-full : h-screen
})

</script>

<template>
    <Head :title="title" />

    <div ref="divRef" class="w-screen h-screen flex">

        <!-- Sidebar -->
        <Sidebar :menu="menu" @toggle="toggle" />

        <!-- Main -->
        <div class="w-full h-full">
            <Header :menu="menu" @toggle="toggle" />
            <div class="h-[calc(100vh-50px)] bg-slate-200 p-4 overflow-y-auto">
                <slot />

            </div>
        </div>
    </div>
</template>