<script  setup>
import { router } from "@inertiajs/vue3";


const props = defineProps({
  page: { type: Object, default: () => ({}) },
});

function nextPage() {
  let nextPageUrl = null;

  nextPageUrl = props.page.next_page_url;

  if (nextPageUrl) {
    router.get(nextPageUrl, {}, { preserveState: true });
  }
}

function prevPage() {
  let prevPageUrl = null;

  prevPageUrl = props.page.prev_page_url;

  if (prevPageUrl) {
    router.get(prevPageUrl, {}, { preserveState: true });
  }
}
</script>
<template>
  <button
    class="px-4 py-2 bg-gray-300 text-gray-700 rounded"
    :disabled="!props.page.prev_page_url"
    @click="prevPage()"
  >
    Anterior
  </button>
  <span
    >Página {{ props.page.current_page }} de
    {{ props.page.last_page }}</span
  >
  <button
    class="px-4 py-2 bg-gray-300 text-gray-700 rounded"
    :disabled="!props.page.next_page_url"
    @click="nextPage()"
  >
    Siguiente
  </button>
</template>