<script setup lang="ts">
// Imports
import { DialogModal, Table, SecondaryButton } from "@/Components";
import { MovementType } from "@/types";

// Props
defineProps<{
  movementTypes: MovementType[];
  show: boolean;
}>();


// Emits
defineEmits(["close", "selectedMovementType"]);
</script>

<template>
 <DialogModal :show="show" maxWidth="lg" @close="$emit('close')">
    <template #title>
      <h2 class="text-sm sm:text-lg font-bold w-full pb-2 sm:pb-0">
        Seleccionar movimientos bancarios
      </h2>
    </template>
    <template #content>
      <div class="mt-0 max-h-[300px] overflow-y-auto">
      <Table>
        <thead>
          <tr class="border-b">
            <th class="text-left w-24">CODIGO</th>
            <th class="text-left">NOMBRE</th>
            <th class="text-left">CAJA</th>
            <th class="text-left">TIPO</th>
          </tr>
        </thead>
        <tbody>
          <tr
            @click="$emit('selectedMovementType', movementType)"
            v-for="(movementType, i) in movementTypes"
            :key="`account-${i}`"
            class="h-8 cursor-pointer"
          >
            <td class="text-left">{{ movementType.code}}</td>
            <td class="text-left">{{ movementType.name }}</td>
            <td class="text-left">{{ movementType.venue }}</td>
            <td class="text-left">{{ movementType.type }}</td>
          </tr>
        </tbody>
      </Table>
    </div>
    </template>
    <template #footer>
      <SecondaryButton @click="$emit('close')" class="mr-2"
        >Cancelar</SecondaryButton
      >
    </template>
  </DialogModal>
</template>