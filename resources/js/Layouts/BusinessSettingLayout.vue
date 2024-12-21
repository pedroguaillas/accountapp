<script setup>

// Imports
import AdminLayout from './AdminLayout.vue';

// Props
defineProps({
    title: String
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
                            <button class="inline-block p-4 border-b-2 rounded-t-lg" >
                            General</button>
                        </li>
                        <li class="me-2">
                            <button
                                class="inline-block p-4 border-b-2 rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300"
                                type="button">Correo</button>
                        </li>
                        <li class="me-2">
                            <button
                                class="inline-block p-4 border-b-2 rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300"
                                type="button" >Masividad</button>
                        </li>
                        <li>
                            <button
                                class="inline-block p-4 border-b-2 rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300"
                                type="button">Contactos</button>
                        </li>
                    </ul>
                </div>
                <div >
                    <div class=" p-4 rounded-lg bg-gray-50 dark:bg-gray-800">
                        <slot />
                    </div>
                </div>

            </div>
        </div>
    </AdminLayout>
</template>