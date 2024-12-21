<script setup>

// Imports
import Header from './admin/Header.vue';
import Sidebar from './admin/Sidebar.vue';
import AdminLayout from './AdminLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ref, watch, onMounted } from 'vue';
import { initFlowbite } from "flowbite";

onMounted(() => {
    initFlowbite();
});

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

    <AdminLayout title="Negocio ajustes de roles">

        <!-- Card -->
        <div class="p-4 bg-white rounded drop-shadow-md">
            <!-- Card Header -->
            <div class="flex flex-col sm:flex-row justify-between items-center">
                <h2 class="text-sm sm:text-lg font-bold w-full pb-2 sm:pb-0">
                    Negocio ajustes de roles
                </h2>
            </div>

            <!-- Resposive -->
            <div class="w-full overflow-x-auto">

                <div class="mb-4 border-b border-gray-200 dark:border-gray-700">
                    <ul class="flex flex-wrap -mb-px text-sm font-medium text-center">
                        <li class="me-2" role="presentation">
                            <Link class="inline-block p-4 border-b-2 rounded-t-lg" id="profile-styled-tab">
                            General</Link>
                        </li>
                        <li class="me-2" role="presentation">
                            <button
                                class="inline-block p-4 border-b-2 rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300"
                                id="dashboard-styled-tab" data-tabs-target="#styled-dashboard" type="button" role="tab"
                                aria-controls="dashboard" aria-selected="false">Correo</button>
                        </li>
                        <li class="me-2" role="presentation">
                            <button
                                class="inline-block p-4 border-b-2 rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300"
                                id="settings-styled-tab" data-tabs-target="#styled-settings" type="button" role="tab"
                                aria-controls="settings" aria-selected="false">Masividad</button>
                        </li>
                        <li role="presentation">
                            <button
                                class="inline-block p-4 border-b-2 rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300"
                                id="contacts-styled-tab" data-tabs-target="#styled-contacts" type="button" role="tab"
                                aria-controls="contacts" aria-selected="false">Contactos</button>
                        </li>
                    </ul>
                </div>
                <div id="default-styled-tab-content">
                    <div class="hidden p-4 rounded-lg bg-gray-50 dark:bg-gray-800" id="styled-profile" role="tabpanel"
                        aria-labelledby="profile-tab">
                        <p class="text-sm text-gray-500 dark:text-gray-400">This is some placeholder content the
                            <strong class="font-medium text-gray-800 dark:text-white">Profile tab's associated
                                content</strong>. Clicking another tab will toggle the visibility of this one
                            for the
                            next. The tab JavaScript swaps classes to control the content visibility and
                            styling.
                        </p>
                    </div>
                    <div class="hidden p-4 rounded-lg bg-gray-50 dark:bg-gray-800" id="styled-dashboard" role="tabpanel"
                        aria-labelledby="dashboard-tab">
                        <p class="text-sm text-gray-500 dark:text-gray-400">This is some placeholder content the
                            <strong class="font-medium text-gray-800 dark:text-white">Dashboard tab's associated
                                content</strong>. Clicking another tab will toggle the visibility of this one
                            for the
                            next. The tab JavaScript swaps classes to control the content visibility and
                            styling.
                        </p>
                    </div>
                    <div class="hidden p-4 rounded-lg bg-gray-50 dark:bg-gray-800" id="styled-settings" role="tabpanel"
                        aria-labelledby="settings-tab">
                        <p class="text-sm text-gray-500 dark:text-gray-400">This is some placeholder content the
                            <strong class="font-medium text-gray-800 dark:text-white">Settings tab's associated
                                content</strong>. Clicking another tab will toggle the visibility of this one
                            for the
                            next. The tab JavaScript swaps classes to control the content visibility and
                            styling.
                        </p>
                    </div>
                    <div class="hidden p-4 rounded-lg bg-gray-50 dark:bg-gray-800" id="styled-contacts" role="tabpanel"
                        aria-labelledby="contacts-tab">
                        <p class="text-sm text-gray-500 dark:text-gray-400">This is some placeholder content the
                            <strong class="font-medium text-gray-800 dark:text-white">Contacts tab's associated
                                content</strong>. Clicking another tab will toggle the visibility of this one
                            for the
                            next. The tab JavaScript swaps classes to control the content visibility and
                            styling.
                        </p>
                    </div>
                </div>

            </div>
        </div>
    </AdminLayout>
</template>