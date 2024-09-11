<script setup>

// Imports
import { router } from '@inertiajs/vue3';

// Emits
defineEmits(['toggle']);

// Props
defineProps({
    menu: Boolean
});

const logout = () => {
    router.post(route('logout'));
};

</script>

<template>
    <header
        class="ease-out duration-300 w-full h-[50px] bg-slate-800 z-50 flex justify-between sm:justify-end items-center px-2">
        <button @click="$emit('toggle')" class="cursor-pointer sm:hidden">
            <svg fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor" class="w-6 h-6 stroke-white">
                <path strokeLinecap="round" strokeLinejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
            </svg>
        </button>
        <div class="text-white flex">
            <h3>Peterio</h3>
            <button @click="logout">
                <svg fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor" class="w-6 h-6 stroke-white">
                    <path strokeLinecap="round" strokeLinejoin="round"
                        d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" />
                </svg>
            </button>
        </div>
    </header>
</template>