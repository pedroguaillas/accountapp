<script setup lang="ts">
// Imports
import { DialogModal, Table, SecondaryButton } from "@/Components";
import { Iess } from "@/types";

// Props
defineProps < {
  iesses: Iess[];
  show: boolean;
} > ();

// Emits
defineEmits(["close", "selectedIess"]);
</script>

<template>
  <DialogModal :show="show" maxWidth="lg" @close="$emit('close')">
    <template #title>
      <h2 class="text-sm sm:text-lg font-bold w-full pb-2 sm:pb-0">
        Seleccionar el IESS
      </h2>
    </template>
    <template #content>
      <div class="mt-0 max-h-[300px] overflow-y-auto">
        <Table>
          <thead>
            <tr class="border-b">
              <th class="text-left w-24">TYPE</th>
              <th class="text-left">NOMBRE</th>
              <th class="text-left">PORCENTAJE</th>
            </tr>
          </thead>
          <tbody>
            <tr @click="$emit('selectedIess', iess)" v-for="(iess, i) in iesses" :key="`account-${i}`"
              class="h-8 cursor-pointer">
              <td class="text-left">{{ iess.type }}</td>
              <td class="text-left">{{ iess.name }}</td>
              <td class="text-left">{{ iess.percentage }}</td>
            </tr>
          </tbody>
        </Table>
      </div>
    </template>
    <template #footer>
      <SecondaryButton @click="$emit('close')" class="mr-2">Cancelar</SecondaryButton>
    </template>
  </DialogModal>
</template>