<script setup>
// Imports
import { Link, usePage } from "@inertiajs/vue3";
import { computed, onMounted } from "vue";
import { initFlowbite } from "flowbite";

onMounted(() => {
  initFlowbite();
});

// Props
defineProps({
  menu: Boolean,
});

// Emits
defineEmits(["toggle"]);

const page = usePage();

const user = computed(() => page.props.auth.user);
</script>

<template>
  <aside
    class="w-[16em] h-[100vh] p-4 flex flex-col z-40 fixed sm:relative bg-slate-800 ease-in-out duration-300"
    v-show="menu"
  >
    <header class="p-2 [&>a]:w-full">
      <Link
        class="rounded hover:bg-slate-500 p-2 text-xl text-white"
        :href="route('dashboard')"
      >
        <strong>CONTRI</strong>
        AP</Link
      >
      <button
        @click="$emit('toggle')"
        class="w-6 h-6 text-slate-200 text-3xl sm:hidden inline-block right-2 top-2 absolute rounded-full"
      >
        <i class="fa fa-close"></i>
      </button>
    </header>
    <nav class="p-2">
      <ul
        v-show="current_tenant_id !== null"
        class="[&>li>a]:text-white [&>li>a]:inline-block [&>li>a]:w-full [&>li>a]:p-2 [&>li>a>i]:stroke-white"
      >
        <li>
          <button
            type="button"
            class="flex items-center w-full p-2 text-base text-white hover:bg-sky-900 transition duration-75 rounded-lg group dark:text-white dark:hover:bg-gray-700"
            aria-controls="dropdown-example"
            data-collapse-toggle="dropdown-example"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              fill="none"
              viewBox="0 0 24 24"
              stroke-width="1.5"
              stroke="currentColor"
              class="size-6"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25"
              />
            </svg>

            <span class="flex-1 ms-3 text-left rtl:text-right whitespace-nowrap"
              >Contabilidad</span
            >
            <svg
              class="w-3 h-3"
              aria-hidden="true"
              xmlns="http://www.w3.org/2000/svg"
              fill="none"
              viewBox="0 0 10 6"
            >
              <path
                stroke="currentColor"
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="m1 1 4 4 4-4"
              />
            </svg>
          </button>
          <ul
            id="dropdown-example"
            class="hidden py-2 space-y-2 [&>li>a]:flex [&>li>a]:items-center [&>li>a]:w-full"
          >
            <li>
              <Link
                :href="route('accounts')"
                class="transition duration-75 rounded-lg pl-11 group text-white hover:bg-sky-900 dark:text-white dark:hover:bg-gray-700"
              >
                Plan de cuentas</Link
              >
            </li>
            <li>
              <Link
                :href="route('journal.index')"
                class="transition duration-75 rounded-lg pl-11 group text-white hover:bg-sky-900 dark:text-white dark:hover:bg-gray-700"
              >
                Asientos contables</Link
              >
            </li>
            <li>
              <Link
                :href="route('costcenter.index')"
                class="transition duration-75 rounded-lg pl-11 group text-white hover:bg-sky-900 dark:text-white dark:hover:bg-gray-700"
              >
                Centro de costos</Link
              >
            </li>
            <li>
              <Link
                :href="route('fixedassets.index')"
                class="transition duration-75 rounded-lg pl-11 group text-white hover:bg-sky-900 dark:text-white dark:hover:bg-gray-700"
              >
                Activos fijos</Link
              >
            </li>
            <li>
              <Link
                :href="route('intangibleassets.index')"
                class="transition duration-75 rounded-lg pl-11 group text-white hover:bg-sky-900 dark:text-white dark:hover:bg-gray-700"
              >
                Activos intangibles</Link
              >
            </li>
          </ul>
        </li>
        <li>
          <Link class="rounded hover:bg-sky-900" :href="route('dashboard')">
            <i class="fa fa-futbol-ball"></i> Inventarios
          </Link>
        </li>
        <li>
          <Link
            class="flex rounded hover:bg-sky-900"
            :href="route('dashboard')"
          >
            Compras
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
          <button
            type="button"
            class="flex items-center w-full p-2 text-base text-white hover:bg-sky-900 transition duration-75 rounded-lg group dark:text-white dark:hover:bg-gray-700"
            aria-controls="dropdown-personal"
            data-collapse-toggle="dropdown-personal"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              fill="none"
              viewBox="0 0 24 24"
              stroke-width="1.5"
              stroke="currentColor"
              class="size-6"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25"
              />
            </svg>

            <span class="flex-1 ms-3 text-left rtl:text-right whitespace-nowrap"
              >Personal</span
            >
            <svg
              class="w-3 h-3"
              aria-hidden="true"
              xmlns="http://www.w3.org/2000/svg"
              fill="none"
              viewBox="0 0 10 6"
            >
              <path
                stroke="currentColor"
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="m1 1 4 4 4-4"
              />
            </svg>
          </button>
          <ul
            id="dropdown-personal"
            class="hidden py-2 space-y-2 [&>li>a]:flex [&>li>a]:items-center [&>li>a]:w-full"
          >
            <li>
              <Link
                :href="route('employee.index')"
                class="transition duration-75 rounded-lg pl-11 group text-white hover:bg-sky-900 dark:text-white dark:hover:bg-gray-700"
              >
                Empleados</Link
              >
            </li>

            <li>
              <Link
                :href="route('advances.index')"
                class="transition duration-75 rounded-lg pl-11 group text-white hover:bg-sky-900 dark:text-white dark:hover:bg-gray-700"
              >
                Adelantos</Link
              >
            </li>
            <li>
              <Link
                :href="route('paymentrol.index')"
                class="transition duration-75 rounded-lg pl-11 group text-white hover:bg-sky-900 dark:text-white dark:hover:bg-gray-700"
              >
                Roles de pago</Link
              >
            </li>
          </ul>
        </li>
        <li>
          <Link class="rounded hover:bg-sky-900" :href="route('dashboard')">
            <i class="fa fa-money-bill"></i> Ajustes
          </Link>
        </li>
      </ul>
      <ul
        v-show="current_tenant_id === null"
        class="[&>li>a]:text-white [&>li>a]:inline-block [&>li>a]:w-full [&>li>a]:p-2 [&>li>a>i]:stroke-white"
      >
        <li>
          <Link
            class="rounded bg-slate-500 hover:bg-sky-900"
            :href="route('customers.index')"
          >
            <i class="fa fa-medal"></i> Clientes
          </Link>
        </li>
      </ul>
    </nav>
  </aside>
</template>