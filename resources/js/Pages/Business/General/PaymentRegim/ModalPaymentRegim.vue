<script setup lang="ts">
// Imports
import { DialogModal, Table, SecondaryButton } from "@/Components";
import {  PayRegim } from "@/types";

// Props
defineProps<{
  globalPaymentRegims: PayRegim[];
  show: boolean;
}>();

// Emits
defineEmits(["close", "selectedPaymentRegim"]);
</script>

<template>
  <DialogModal :show="show" maxWidth="lg" @close="$emit('close')">
    <template #title>
      <h2 class="text-sm sm:text-lg font-bold w-full pb-2 sm:pb-0">
        Seleccionar el régimen
      </h2>
    </template>
    <template #content>
      <div class="mt-0 max-h-[300px] overflow-y-auto">
        <Table>
          <thead>
            <tr class="border-b">
              <th class="text-left w-24">REGION</th>
              <th class="text-left">XIII</th>
              <th class="text-left">XIV</th>
              <th class="text-left">FONDOS DE RESERVA</th>
            </tr>
          </thead>
          <tbody>
            <tr @click="$emit('selectedPaymentRegim', paymentRegim)" v-for="(paymentRegim, i) in globalPaymentRegims"
              :key="`paymentRegim-${i}`" class="h-8 cursor-pointer hover:bg-gray-200">
              <td class="text-left">{{ paymentRegim.region }}</td>
              <td class="text-left">{{ paymentRegim.months_xiii }}</td>
              <td class="text-left">{{ paymentRegim.months_xiv }}</td>
              <td class="text-left">{{ paymentRegim.months_reserve_funds }}</td>
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