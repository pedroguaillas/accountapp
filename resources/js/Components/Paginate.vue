<script setup>

// Imports
import { ArrowLeftIcon } from "@heroicons/vue/24/solid";
import { router } from "@inertiajs/vue3";

// Props
const props = defineProps({
  page: { type: Object, default: () => ({}) },
});

function nextPage() {
  let nextPageUrl = props.page.next_page_url;

  if (nextPageUrl) {
    router.get(nextPageUrl, {}, { preserveState: true });
  }
}

function prevPage() {
  let prevPageUrl = props.page.prev_page_url;

  if (prevPageUrl) {
    router.get(prevPageUrl, {}, { preserveState: true });
  }
}
</script>
<template>
  <div>
    <button class="px-2 bg-gray-300 text-gray-700 text-2xl cursor-pointer font-bold rounded"
      :disabled="!props.page.prev_page_url" @click="prevPage()">
      < </button>

        <span>Página {{ props.page.current_page }} de
          {{ props.page.last_page }}</span>

        <button class="px-2 bg-gray-300 text-gray-700 text-2xl cursor-pointer font-bold rounded"
          :disabled="!props.page.next_page_url" @click="nextPage()">
          >
        </button>
  </div>
</template>