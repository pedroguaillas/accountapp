<script setup>

// Imports
import DialogModal from "@/Components/DialogModal.vue";
import Table from "@/Components/Table.vue";

// Props
defineProps({
  accounts: { type: Array, default: () => ([]) },
  show: { type: Boolean, default: () => true },
});

// Emits
defineEmits(["selectAccount", "close"]);
</script>

<template>
  <DialogModal :show="show" maxWidth="lg" @close="$emit('close')">
    <template #title>
      Seleccionar cuenta
    </template>
    <template #content>
      <Table>
        <thead>
          <tr class="border-b">
            <th class="text-left w-24">CUENTA</th>
            <th class="text-left">DESCRIPCION</th>
          </tr>
        </thead>
        <tbody>
          <tr @click="$emit('selectAccount', account.id)" v-for="(account, i) in accounts" :key="`account-${i}`"
            class="h-8 cursor-pointer">
            <td class="text-left">{{ account.code }}</td>
            <td class="text-left">{{ account.name }}</td>
          </tr>
        </tbody>
      </Table>
    </template>
    <template #footer>
      <button @click="$emit('selectAccount')"
        class="px-6 py-2 ml-2 bg-blue-600 dark:bg-blue-600 text-blue-100 dark:text-blue-200 rounded">
        Guardar
      </button>
    </template>
  </DialogModal>
</template>