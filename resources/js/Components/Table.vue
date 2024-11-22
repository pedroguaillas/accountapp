<script setup>
import { computed, useSlots } from "vue";
</script>

<template>
  <!-- Responsive -->
  <div class="w-full overflow-x-auto">
    <!-- Tabla -->
    <table
      class="mt-4 text-xs sm:text-sm table-auto w-full text-center text-gray-700"
    >
      <slot />
    </table>
  </div>
</template>
