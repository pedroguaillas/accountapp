<script setup lang="ts">
// Imports
import {
  ArrowRightOnRectangleIcon,
  Bars3Icon,
} from "@heroicons/vue/24/outline";
import { WrenchIcon } from "@heroicons/vue/24/solid";
import { Dropdown, DropdownLink } from "@/Components";
</script>

<template>
  <Dropdown width="60">
    <template #trigger>
      <button type="button">
        <WrenchIcon class="size-6 text-white transition-transform duration-300" />
      </button>
    </template>

    <template #content>
      <div class="w-60">
        <!-- Team Settings -->
        <DropdownLink as="a" :href="route('busssines.setting.iess.index')"> General </DropdownLink>

        <DropdownLink as="a" :href="route('business.setting.accountlinks')">
          Vinculación de cuentas
        </DropdownLink>

        <DropdownLink as="a" :href="route('business.setting.roles')">
          Roles
        </DropdownLink>
      </div>
    </template>
  </Dropdown>
</template>