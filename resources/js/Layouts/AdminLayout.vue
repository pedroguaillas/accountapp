<script setup lang="ts">
// Imports
import Header from "./admin/Header.vue";
import Sidebar from "./admin/Sidebar.vue";
import { Head } from "@inertiajs/vue3";
import { ref, watch, onMounted } from "vue";
import { initFlowbite } from "flowbite";

onMounted(() => {
  initFlowbite();
});

// Props
defineProps<{
  title: string;
}>()
// Refs
const divRef = ref<HTMLDivElement | null>(null);
const menu = ref(false);

const toggle = () => {
  menu.value = !menu.value;
};

watch(divRef, () => {
  if (!divRef.value) return;
  if (divRef.value.getBoundingClientRect().width > 640) {
    toggle();
  }
});

</script>

<template>

  <Head :title="title" />
  <div ref="divRef" class="w-screen h-screen flex">
    <!-- Sidebar -->
    <Sidebar :menu="menu" @toggle="toggle" />

    <!-- Main -->
    <div class="w-full h-full">
      <Header :menu="menu" @toggle="toggle" />
      <div class="h-[calc(100vh-60px)] bg-slate-200 p-4 md:p-8 overflow-y-auto">
        <slot />
      </div>
    </div>
  </div>
</template>