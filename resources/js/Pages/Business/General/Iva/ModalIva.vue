<script setup>
// Imports
import DialogModal from "@/Components/DialogModal.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import Table from "@/Components/Table.vue";

// Props
defineProps({
  ivas: { type: Array, default: () => [] },
  show: { type: Boolean, default: false },
});

// Emits
defineEmits(["close", "save"]);
</script>

<template>
 <DialogModal :show="show" maxWidth="lg" @close="$emit('close')">
    <template #title>
      <h2 class="text-sm sm:text-lg font-bold w-full pb-2 sm:pb-0">
        Seleccionar IVA
      </h2>
    </template>
    <template #content>
      <div class="mt-0 max-h-[300px] overflow-y-auto">
      <Table>
        <thead>
          <tr class="border-b">
            <th class="text-left w-24">CODIGO</th>
            <th class="text-left">NOMBRE</th>
            <th class="text-left">PORCENTAJE</th>
          </tr>
        </thead>
        <tbody>
          <tr
            @click="$emit('selectedIva', iva)"
            v-for="(iva, i) in ivas"
            :key="`account-${i}`"
            class="h-8 cursor-pointer"
          >
            <td class="text-left">{{ iva.code}}</td>
            <td class="text-left">{{ iva.name }}</td>
            <td class="text-left">{{ iva.percentage }}</td>
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