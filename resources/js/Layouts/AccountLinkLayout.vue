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
              <button class="inline-block p-4 border-b-2 rounded-t-lg">
                General
              </button>
            </li>
            <li class="me-2">
              <Link
                class="pointer inline-block p-4 border-b-2 rounded-t-lg"
                :href="route('setting.account.index')"
                :class="{
                  ' border-gray-300 dark:text-gray-300 text-xl pt-2':
                    route().current('setting.account.index'),
                  ' hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300':
                    !route().current('setting.account.index'),
                }"
                type="button"
              >
                Activos fijos
              </Link>
            </li>

            <li>
              <Link
                class="pointer inline-block p-4 border-b-2 rounded-t-lg"
                :href="route('setting.account.rol.index')"
                :class="{
                  ' border-gray-300 dark:text-gray-300 text-xl pt-2':
                    route().current('setting.account.rol.index'),
                  ' hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300':
                    !route().current('setting.account.rol.index'),
                }"
                type="button"
              >
                Roles
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