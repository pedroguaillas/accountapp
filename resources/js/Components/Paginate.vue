<script setup lang="ts">
// Imports
import { ChevronLeftIcon, ChevronRightIcon } from "@heroicons/vue/24/outline";
import { router } from "@inertiajs/vue3";
import { computed, ref } from "vue";
import { GeneralRequest } from "@/types/general-request";

// Props
const props = defineProps<{
  page: GeneralRequest<any>
}>();

// States
const currentGroup = ref(0); // Tracks the current group of pages
const itemsPerGroup = 5; // Number of pages per group

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

function interPage(url:string) {
  if (url) {
    router.get(url, {}, { preserveState: true });
  }
}

// Divide pagination links into groups
const groupedLinks = computed(() => {
  const links = props.page.links.filter(link => link.url); // Only include valid links
  const groups = [];

  for (let i = 0; i < links.length; i += itemsPerGroup) {
    groups.push(links.slice(i, i + itemsPerGroup));
  }

  return groups;
});

// Get the current group of links
const visibleLinks = computed(() => groupedLinks.value[currentGroup.value] || []);

// Navigate to the next group
function nextGroup() {
  if (currentGroup.value < groupedLinks.value.length - 1) {
    currentGroup.value++;
  }
}

// Navigate to the previous group
function prevGroup() {
  if (currentGroup.value > 0) {
    currentGroup.value--;
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
      <!-- Previous Group Button -->
      <li>
        <button
          @click="prevGroup"
          :disabled="currentGroup === 0"
          class="flex items-center justify-center px-3 h-8 leading-tight text-gray-500 bg-white border border-gray-300 rounded-s-lg hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">
          <ChevronLeftIcon class="size-4 text-gray-500 stroke-[3px]" />
        </button>
      </li>
      
      <!-- Page Links -->
      <li v-for="(link, i) in visibleLinks" :key="`pagination-${i}`">
        <button
          :disabled="!link.url || link.active"
          @click="()=>link.url && interPage(link.url)"
          :class="[
            'flex items-center justify-center px-3 h-8 text-sm leading-tight border',
            link.active ? 'bg-blue-50 text-blue-600' : 'bg-white text-gray-500 hover:bg-gray-100 hover:text-gray-700',
            'dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white'
          ]"
          v-html="link.label"
        ></button>
      </li>
      
      <!-- Next Group Button -->
      <li>
        <button
          @click="nextGroup"
          :disabled="currentGroup === groupedLinks.length - 1"
          class="flex items-center justify-center px-3 h-8 leading-tight text-gray-500 bg-white border border-gray-300 rounded-e-lg hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">
          <ChevronRightIcon class="size-4 text-gray-500 stroke-[3px]" />
        </button>
      </li>
    </ul>
  </nav>
</template>
