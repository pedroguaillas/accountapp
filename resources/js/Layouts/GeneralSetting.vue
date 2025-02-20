<script setup>
// Imports
import AdminLayout from "./AdminLayout.vue";
import { Link } from "@inertiajs/vue3";

// Props
defineProps({
  title: String,
});
</script>

<template>
  <AdminLayout :title="title">
    <!-- Card -->
    <div class="p-4 bg-white rounded drop-shadow-md">
      <!-- Card Header 1 -->
      <div class="flex flex-col sm:flex-row justify-between items-center">
        <h2 class="text-sm sm:text-lg font-bold w-full pb-2 sm:pb-0">
          {{ title }}
        </h2>
      </div>

      <!-- Resposive 1-->
      <div class="w-full overflow-x-auto">
        <div class="mb-4 border-b border-gray-200 dark:border-gray-700">
          <ul class="flex flex-wrap -mb-px text-sm font-medium text-center">
            <li class="me-2">
              <Link
                class="pointer inline-block p-4 border-b-2 rounded-t-lg"
                :href="route('busssines.setting.ivas.index')"
                :class="{
                  ' border-gray-300 dark:text-gray-300 text-xl pt-2':
                    route().current('busssines.setting.ivas.index'),
                  ' hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300':
                    !route().current('busssines.setting.ivas.index'),
                }"
                type="button"
              >
                Generales
              </Link>
            </li>
            <li>
              <Link
                class="pointer inline-block p-4 border-b-2 rounded-t-lg"
                :href="route('busssines.setting.paymethods.index')"
                :class="{
                  ' border-gray-300 dark:text-gray-300 text-xl pt-2':
                    route().current('busssines.setting.paymethods.index'),
                  ' hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300':
                    !route().current('busssines.setting.paymethods.index'),
                }"
                type="button"
              >
                Métodos de pago
              </Link>
            </li>

            <li>
              <Link
                class="pointer inline-block p-4 border-b-2 rounded-t-lg"
                :href="route('busssines.setting.ivas.index')"
                :class="{
                  ' border-gray-300 dark:text-gray-300 text-xl pt-2':
                    route().current('busssines.setting.ivas.index'),
                  ' hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300':
                    !route().current('busssines.setting.ivas.index'),
                }"
                type="button"
              >
                IVAS
              </Link>
            </li>

            <li>
              <Link
                class="pointer inline-block p-4 border-b-2 rounded-t-lg"
                :href="route('busssines.setting.ices.index')"
                :class="{
                  ' border-gray-300 dark:text-gray-300 text-xl pt-2':
                    route().current('busssines.setting.ices.index'),
                  ' hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300':
                    !route().current('busssines.setting.ices.index'),
                }"
                type="button"
              >
                ICES
              </Link>
            </li>

            <li>
              <Link
                class="pointer inline-block p-4 border-b-2 rounded-t-lg"
                :href="route('busssines.setting.withholding.index')"
                :class="{
                  ' border-gray-300 dark:text-gray-300 text-xl pt-2':
                    route().current('busssines.setting.withholding.index'),
                  ' hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300':
                    !route().current('busssines.setting.withholding.index'),
                }"
                type="button"
              >
                Retenciones
              </Link>
            </li>
          </ul>
        </div>
        <div class="p-0 rounded-lg">
          <slot />
        </div>
      </div>
    </div>
  </AdminLayout>
</template>