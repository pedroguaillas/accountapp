<script setup lang="ts">
// Imports
import {
  BookOpenIcon,
  UserGroupIcon,
  BuildingLibraryIcon,
} from "@heroicons/vue/24/outline";
import { ChevronDownIcon } from "@heroicons/vue/24/solid";
import { Link, usePage } from "@inertiajs/vue3";
import { ref, computed } from "vue";


// Props
defineProps<{
  menu: boolean;
}>()

// Estados de los menúa desplegables
const isDropdownOpenAccount = ref(false);
const isDropdownOpenPersonal = ref(false);
const isDropdownOpenBank = ref(false);
const current_tenant_id =ref(null);// para identificar cuando un usuario tiene muchos rucs cambiado la condion del === para finalidades de prueba

// Emits
defineEmits(["toggle"]);

const page = usePage();

const user = computed<AuthUser>(() => page.props.auth.user as AuthUser);
</script>

<template>
  <aside
    class="w-[21em] h-[100vh] p-4 flex flex-col z-40 fixed sm:relative bg-gradient-to-l from-slate-700 to-slate-800 ease-in-out duration-300"
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
        x
      </button>
    </header>
    <nav
      class="p-2 max-h-[100%] overflow-y-auto bg-scroll"
      style="scrollbar-color: #7da8ce transparent; scrollbar-width: thin"
    >
      <ul
        v-show="current_tenant_id === null"
        class="[&>li>a]:text-white [&>li>a]:inline-block [&>li>a]:w-full [&>li>a]:py-2 [&>li>a>i]:stroke-white"
      >
        <li>
          <button
            type="button"
            class="flex items-center w-full p-2 text-base text-white hover:bg-sky-900 transition duration-75 rounded-lg group dark:text-white dark:hover:bg-gray-700"
            @click="isDropdownOpenAccount = !isDropdownOpenAccount"
            :aria-expanded="isDropdownOpenAccount"
            aria-controls="dropdown-account"
          >
            <BookOpenIcon class="size-6 text-white" />
            <span class="flex-1 ms-3 text-left rtl:text-right whitespace-nowrap"
              >Contabilidad</span
            >
            <ChevronDownIcon
              :class="isDropdownOpenAccount ? 'rotate-180' : ''"
              class="size-6 text-white transition-transform duration-300"
            />
          </button>
          <div
            id="dropdown-account"
            :class="isDropdownOpenAccount ? 'max-h-[1000px]' : 'max-h-0'"
            class="transition-all duration-1000 ease-in-out overflow-hidden"
          >
            <ul
              class="ml-2 pl-1 border-l-2 rounded-l py-2 space-y-2 [&>li>a]:flex [&>li>a]:items-center [&>li>a]:w-full"
            >
              <li>
                <Link
                  :href="route('accounts')"
                  class="transition duration-75 rounded-lg pl-7 group text-white hover:bg-sky-900 dark:text-white dark:hover:bg-gray-700"
                >
                  Plan de cuentas</Link
                >
              </li>
              <li>
                <Link
                  :href="route('journal.index')"
                  class="transition duration-75 rounded-lg pl-7 group text-white hover:bg-sky-900 dark:text-white dark:hover:bg-gray-700"
                >
                  Asientos contables</Link
                >
              </li>
              <li>
                <Link
                  :href="route('costcenter.index')"
                  class="transition duration-75 rounded-lg pl-7 group text-white hover:bg-sky-900 dark:text-white dark:hover:bg-gray-700"
                >
                  Centro de costos</Link
                >
              </li>
              <li>
                <Link
                  :href="route('fixedassets.index')"
                  class="transition duration-75 rounded-lg pl-7 group text-white hover:bg-sky-900 dark:text-white dark:hover:bg-gray-700"
                >
                  Activos fijos</Link
                >
              </li>
              <li>
                <Link
                  :href="route('intangibleassets.index')"
                  class="transition duration-75 rounded-lg pl-7 group text-white hover:bg-sky-900 dark:text-white dark:hover:bg-gray-700"
                >
                  Activos intangibles</Link
                >
              </li>
            </ul>
          </div>
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
          <button
            type="button"
            class="flex items-center w-full p-2 text-base text-white hover:bg-sky-900 transition duration-75 rounded-lg group dark:text-white dark:hover:bg-gray-700"
            @click="isDropdownOpenBank = !isDropdownOpenBank"
            :aria-expanded="isDropdownOpenBank"
            aria-controls="dropdown-bank"
          >
            <BuildingLibraryIcon class="size-6 text-white" />
            <span class="flex-1 ms-3 text-left rtl:text-right whitespace-nowrap"
              >Caja y bancos</span
            >
            <ChevronDownIcon
              :class="isDropdownOpenBank ? 'rotate-180' : ''"
              class="size-6 text-white"
            />
          </button>
          <div
            id="dropdown-bank"
            :class="isDropdownOpenBank ? 'max-h-[1000px]' : 'max-h-0'"
            class="transition-all duration-1000 ease-in-out overflow-hidden"
          >
            <ul
              class="ml-2 pl-1 border-l-2 rounded-l py-2 space-y-2 [&>li>a]:flex [&>li>a]:items-center [&>li>a]:w-full"
            >
              <li>
                <Link
                  :href="route('banks.index')"
                  class="transition duration-75 rounded-lg pl-11 group text-white hover:bg-sky-900 dark:text-white dark:hover:bg-gray-700"
                >
                  Bancos</Link
                >
              </li>

              <li>
                <Link
                  :href="route('transactions.index')"
                  class="transition duration-75 rounded-lg pl-11 group text-white hover:bg-sky-900 dark:text-white dark:hover:bg-gray-700"
                >
                  Movimientos bancarios</Link
                >
              </li>

              <li>
                <Link
                  :href="route('boxes.index')"
                  class="transition duration-75 rounded-lg pl-11 group text-white hover:bg-sky-900 dark:text-white dark:hover:bg-gray-700"
                >
                  Cajas</Link
                >
              </li>

              <li>
                <Link
                  :href="route('transaction.boxes.index')"
                  class="transition duration-75 rounded-lg pl-11 group text-white hover:bg-sky-900 dark:text-white dark:hover:bg-gray-700"
                >
                  Movimiento de cajas</Link
                >
              </li>
              <li>
                <Link
                  :href="route('transaction.expenses.index')"
                  class="transition duration-75 rounded-lg pl-11 group text-white hover:bg-sky-900 dark:text-white dark:hover:bg-gray-700"
                >
                  Gastos</Link
                >
              </li>
            </ul>
          </div>
        </li>

        <li>
          <button
            type="button"
            class="flex items-center w-full p-2 text-base text-white hover:bg-sky-900 transition duration-75 rounded-lg group dark:text-white dark:hover:bg-gray-700"
            @click="isDropdownOpenPersonal = !isDropdownOpenPersonal"
            :aria-expanded="isDropdownOpenPersonal"
            aria-controls="dropdown-personal"
          >
            <UserGroupIcon class="size-6 text-white" />
            <span class="flex-1 ms-3 text-left rtl:text-right whitespace-nowrap"
              >Personal</span
            >
            <ChevronDownIcon
              :class="isDropdownOpenPersonal ? 'rotate-180' : ''"
              class="size-6 text-white"
            />
          </button>
          <div
            id="dropdown-personal"
            :class="isDropdownOpenPersonal ? 'max-h-[1000px]' : 'max-h-0'"
            class="transition-all duration-1000 ease-in-out overflow-hidden"
          >
            <ul
              class="ml-2 pl-1 border-l-2 rounded-l py-2 space-y-2 [&>li>a]:flex [&>li>a]:items-center [&>li>a]:w-full"
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
                  :href="route('hours.index')"
                  class="transition duration-75 rounded-lg pl-11 group text-white hover:bg-sky-900 dark:text-white dark:hover:bg-gray-700"
                >
                  Horas Extra-Normales</Link
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
          </div>
        </li>
        <li>
          <Link class="rounded hover:bg-sky-900" :href="route('dashboard')">
            <i class="fa fa-money-bill"></i> Ajustes
          </Link>
        </li>
      </ul>
      <ul
        v-show="current_tenant_id !== null"
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