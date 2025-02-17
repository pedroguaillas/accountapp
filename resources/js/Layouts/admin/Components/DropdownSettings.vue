<script setup>
// Imports
import {
  ArrowRightOnRectangleIcon,
  Bars3Icon,
} from "@heroicons/vue/24/outline";
import { WrenchIcon } from "@heroicons/vue/24/solid";
import Dropdown from "@/Components/Dropdown.vue";
import DropdownLink from "@/Components/DropdownLink.vue";
</script>

<template>
  <Dropdown width="60">
    <template #trigger>
      <button type="button">
        <WrenchIcon
          class="size-6 text-white transition-transform duration-300"
        />
      </button>
    </template>

    <template #content>
      <div class="w-60">
        <!-- Team Settings -->
        <DropdownLink :href="route('paymethods.index')"> General </DropdownLink>

        <DropdownLink :href="route('business.setting.accountlinks')">
          Vinculación de cuentas
        </DropdownLink>

        <DropdownLink :href="route('business.setting.roles')">
          Roles
        </DropdownLink>
      </div>
    </template>
  </Dropdown>
</template>