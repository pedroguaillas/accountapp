<<script setup>
// Imports
import { ChevronLeftIcon, ChevronRightIcon } from "@heroicons/vue/24/outline";
import { router } from "@inertiajs/vue3";

// Props
const props = defineProps({
  page: { type: Object, default: () => ({}) },
});

function nextPage() {
  if (props.page.next_page_url) {
    router.get(props.page.next_page_url, {}, { preserveState: true });
  }
}

function prevPage() {
  if (props.page.prev_page_url) {
    router.get(props.page.prev_page_url, {}, { preserveState: true });
  }
}

function interPage(url) {
  if (url) {
    router.get(url, {}, { preserveState: true });
  }
}
</script>

<template>
  <nav class="flex items-center flex-column flex-wrap md:flex-row justify-between pt-4" aria-label="Table navigation">
    <span
      class="text-sm font-normal text-gray-500 dark:text-gray-400 mb-4 md:mb-0 block w-full md:inline md:w-auto">
      Mostrando
      <span class="font-semibold text-gray-900 dark:text-white">{{ props.page.current_page }}</span> de
      <span class="font-semibold text-gray-900 dark:text-white">{{ props.page.last_page }}</span>
    </span>
    <ul class="inline-flex -space-x-px rtl:space-x-reverse text-sm h-8">
      <li>
        <button @click="prevPage"
          :disabled="!props.page.prev_page_url"
          class="flex items-center justify-center px-3 h-8 ms-0 leading-tight text-gray-500 bg-white border border-gray-300 rounded-s-lg hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">
          <ChevronLeftIcon class="size-4 text-gray-500 stroke-[3px]" />
        </button>
      </li>
      <li v-for="(link, i) in props.page.links" :key="`pagination-${i}`">
        <button
          :disabled="!link.url || link.active"
          @click="interPage(link.url)"
          :class="[
            'flex items-center justify-center px-3 h-8 text-sm leading-tight border',
            link.active ? 'bg-blue-50 text-blue-600' : 'bg-white text-gray-500 hover:bg-gray-100 hover:text-gray-700',
            'dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white'
          ]"
          v-html="link.label"
        ></button>
      </li>
      <li>
        <button @click="nextPage"
          :disabled="!props.page.next_page_url"
          class="flex items-center justify-center px-3 h-8 leading-tight text-gray-500 bg-white border border-gray-300 rounded-e-lg hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">
          <ChevronRightIcon class="size-4 text-gray-500 stroke-[3px]" />
        </button>
      </li>
    </ul>
  </nav>
</template>
