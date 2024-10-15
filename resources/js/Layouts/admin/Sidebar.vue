<script setup>

// Imports
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

// Emits
defineEmits(['toggle']);

// Props
defineProps({
    menu: Boolean
});

const page = usePage()

const user = computed(() => page.props.auth.user)

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
            <ul v-show="current_tenant_id !== null"
                class="[&>li>a]:text-white [&>li>a]:inline-block [&>li>a]:w-full [&>li>a]:p-2 [&>li>a>i]:stroke-white">
                <li>
                    <button type="button"
                        class="flex items-center w-full p-2 text-base text-white hover:bg-sky-900 transition duration-75 rounded-lg group dark:text-white dark:hover:bg-gray-700"
                        aria-controls="dropdown-example" data-collapse-toggle="dropdown-example">
                        <svg class="flex-shrink-0 w-5 h-5 text-white transition duration-75 dark:text-gray-400 dark:group-hover:text-white"
                            aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                            viewBox="0 0 18 21">
                            <path
                                d="M15 12a1 1 0 0 0 .962-.726l2-7A1 1 0 0 0 17 3H3.77L3.175.745A1 1 0 0 0 2.208 0H1a1 1 0 0 0 0 2h.438l.6 2.255v.019l2 7 .746 2.986A3 3 0 1 0 9 17a2.966 2.966 0 0 0-.184-1h2.368c-.118.32-.18.659-.184 1a3 3 0 1 0 3-3H6.78l-.5-2H15Z" />
                        </svg>
                        <span class="flex-1 ms-3 text-left rtl:text-right whitespace-nowrap">Contabilidad</span>
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 10 6">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="m1 1 4 4 4-4" />
                        </svg>
                    </button>
                    <ul id="dropdown-example"
                        class="hidden py-2 space-y-2 [&>li>a]:flex [&>li>a]:items-center [&>li>a]:w-full">
                        <li>
                            <Link href="#"
                                class="transition duration-75 rounded-lg pl-11 group text-white hover:bg-sky-900 dark:text-white dark:hover:bg-gray-700">
                            Activos
                            fijos</Link>
                        </li>
                        <li>
                            <Link href="#"
                                class="transition duration-75 rounded-lg pl-11 group text-white hover:bg-sky-900 dark:text-white dark:hover:bg-gray-700">
                            Amortizaciones
                            y depreciaciones</Link>
                        </li>
                        <li>
                            <Link href="#"
                                class="transition duration-75 rounded-lg pl-11 group text-white hover:bg-sky-900 dark:text-white dark:hover:bg-gray-700">
                            Plan
                            de cuentas</Link>
                        </li>
                        <li>
                            <Link href="#"
                                class="transition duration-75 rounded-lg pl-11 group text-white hover:bg-sky-900 dark:text-white dark:hover:bg-gray-700">
                            Asientos
                            contables</Link>
                        </li>
                        <li>
                            <Link :href="route('costcenter.index')"
                                class="transition duration-75 rounded-lg pl-11 group text-white hover:bg-sky-900 dark:text-white dark:hover:bg-gray-700">
                            Centro
                            de costos</Link>
                        </li>
                    </ul>
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
                    <i class="fa fa-money-bill"></i> Ajustes
                    </Link>
                </li>
            </ul>
            <ul v-show="current_tenant_id === null"
                class="[&>li>a]:text-white [&>li>a]:inline-block [&>li>a]:w-full [&>li>a]:p-2 [&>li>a>i]:stroke-white">
                <li>
                    <Link class="rounded bg-slate-500 hover:bg-sky-900" :href="route('customers.index')">
                    <i class="fa fa-medal"></i> Clientes
                    </Link>
                </li>
            </ul>
        </nav>
    </aside>
</template>