<script setup>

// Imports
import { Link } from '@inertiajs/vue3';

// Emits
defineEmits(['toggle']);

// Props
defineProps({
    menu: Boolean
});

</script>

<template>
    <aside class="w-[16em] h-[100vh] p-4 flex flex-col z-40 fixed sm:relative bg-slate-800 ease-in-out duration-300"
        v-show="menu">
        <header class="p-2 [&>a]:w-full">
            <Link class="rounded hover:bg-slate-500 p-2 text-xl text-white" :href="route('dashboard')">
            <strong>CONTRI</strong>
            AP</Link>
            <button @click="$emit('toggle')"
                class="w-6 h-6 text-slate-200 text-3xl sm:hidden inline-block right-2 top-2 absolute rounded-full">
                <i class="fa fa-close"></i>
            </button>
        </header>
        <nav class="p-2">
            <ul class="[&>li>a]:text-white [&>li>a]:inline-block [&>li>a]:w-full [&>li>a]:p-2 [&>li>a>i]:stroke-white">
                <li>
                    <Link class="rounded bg-slate-500 hover:bg-sky-900" :href="route('dashboard')">
                    <i class="fa fa-medal"></i> Contabilidad
                    </Link>
                </li>
                <li>
                    <Link class="rounded hover:bg-sky-900" :href="route('dashboard')">
                    <i class="fa fa-futbol-ball"></i> Inventarios
                    </Link>
                </li>
                <li>
                    <Link class="rounded hover:bg-sky-900" :href="route('dashboard')">
                    <i class="fa fa-calendar"></i> Compras
                    </Link>
                </li>
                <li>
                    <Link class="rounded hover:bg-sky-900" :href="route('dashboard')">
                    <i class="fa fa-align-center"></i> Ventas
                    </Link>
                </li>
                <li>
                    <Link class="rounded hover:bg-sky-900" :href="route('dashboard')">
                    <i class="fa fa-align-right"></i> Caja y Bancos
                    </Link>
                </li>
                <li>
                    <Link class="rounded hover:bg-sky-900" :href="route('dashboard')">
                    <i class="fa fa-bell"></i> Personal
                    </Link>
                </li>
                <li>
                    <Link class="rounded hover:bg-sky-900" :href="route('dashboard')">
                    <i class="fa fa-money-bill"></i> Reportes
                    </Link>
                </li>
            </ul>
        </nav>
    </aside>
</template>