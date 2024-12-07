<script setup>

// Imports
import { ChevronLeftIcon, ChevronRightIcon } from "@heroicons/vue/24/outline";
import { router } from "@inertiajs/vue3";

// Props
const props = defineProps({
  page: { type: Object, default: () => ({}) },
});

function nextPage() {
  let nextPageUrl = props.page.next_page_url;

  if (nextPageUrl) {
    reloadPage(nextPageUrl);
    //router.get(nextPageUrl, {}, { preserveState: true });
  }
}

function prevPage() {
  let prevPageUrl = props.page.prev_page_url;

  if (prevPageUrl) {
    reloadPage(prevPageUrl);
    //router.get(prevPageUrl, {}, { preserveState: true });
  }
}

function interPage(url) {
  if (url) {
    reloadPage(url);
    //router.get(prevPageUrl, {}, { preserveState: true });
  }
}

defineEmits(["reloadPage"]);
</script>
<template>
  <!-- <div>
    <button class="px-2 bg-gray-300 text-gray-700 text-2xl cursor-pointer font-bold rounded"
      :disabled="!props.page.prev_page_url" @click="prevPage()">
      < </button>

        <span>Página {{ props.page.current_page }} de
          {{ props.page.last_page }}</span>

        <button class="px-2 bg-gray-300 text-gray-700 text-2xl cursor-pointer font-bold rounded"
          :disabled="!props.page.next_page_url" @click="nextPage()">
          >
        </button>
  </div> -->
  <nav class="flex items-center flex-column flex-wrap md:flex-row justify-between pt-4" aria-label="Table navigation">
    <span
      class="text-sm font-normal text-gray-500 dark:text-gray-400 mb-4 md:mb-0 block w-full md:inline md:w-auto">Mostrando
      <span class="font-semibold text-gray-900 dark:text-white">{{ props.page.current_page }} - {{ props.page.last_page
        }}</span> de <span class="font-semibold text-gray-900 dark:text-white">{{ props.page.total }}</span></span>
    <ul class="inline-flex -space-x-px rtl:space-x-reverse text-sm h-8">
      <li>
        <button :disabled="!props.page.prev_page_url" @click="prevPage()"
          class="flex items-center justify-center px-3 h-8 ms-0 leading-tight text-gray-500 bg-white border border-gray-300 rounded-s-lg hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">
          <ChevronLeftIcon class="size-4 text-gray-500 stroke-[3px]" />
        </button>
      </li>
      <!-- <li>
        <button v-for="link,i in props.page.links" :key="`paginat-${i}`" :disabled="!props.page.prev_page_url" @click="interPage(link.url)" aria-current="page"
          class="flex items-center justify-center px-3 h-8 text-blue-600 border border-gray-300 bg-blue-50 hover:bg-blue-100 hover:text-blue-700 dark:border-gray-700 dark:bg-gray-700 dark:text-white">{{ link.label }}</button>
      </li> -->
      <li>
        <button :disabled="!props.page.prev_page_url" @click="nextPage()"
          class="flex items-center justify-center px-3 h-8 leading-tight text-gray-500 bg-white border border-gray-300 rounded-e-lg hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">
          <ChevronRightIcon class="size-4 text-gray-500 stroke-[3px]" />
        </button>
      </li>
    </ul>
  </nav>
</template>