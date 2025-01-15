<script setup>
// Imports
import DialogModal from "@/Components/DialogModal.vue";

// Props
const props = defineProps({
  paymentRol: { type: Object, default: () => ({}) },
  show: { type: Boolean, default: false },
});

// Emits
defineEmits(["close", "save"]);
</script>

<template>
  <DialogModal :show="show" maxWidth="2xl" @close="$emit('close')">
    <template #title>
      {{ props.paymentRol.cuit }} / {{ props.paymentRol.name }}
    </template>
    <template #content>
      <div class="mt-4 flex flex-col sm:flex-row gap-4 w-full">
        <article class="rounded">
          <h2 class="text-xl font-bold">Ingresos</h2>
          <div
            v-for="ingressrol in props.paymentRol.ingresses"
            :key="ingressrol.id"
            class="col-2 flex mt-2"
          >
            <span
              class="w-4/6 px-4 bg-slate-100 flex items-center border rounded-l"
              >{{ ingressrol.name }}</span
            >
            <input
              v-model="ingressrol.value"
              type="number"
              class="block w-4/6 border border-gray-300 px-4 py-1 focus:outline-none text-right rounded-r"
              :disabled="ingressrol.name === 'IESS PATRONAL'"
            />
          </div>
        </article>

        <article>
          <h2 class="text-xl font-bold">Egresos</h2>
          <div
            v-for="egressrol in props.paymentRol.egresses"
            :key="egressrol.id"
            class="col-2 flex mt-2"
          >
            <span
              class="w-4/6 px-4 bg-slate-100 flex items-center border rounded-l"
            >
              {{ egressrol.name }}
            </span>

            <input
              v-model="egressrol.value"
              type="number"
              class="block w-4/6 border border-gray-300 px-4 py-1 focus:outline-none text-right rounded-r"
              :disabled="egressrol.name === 'IESS PATRONAL'"
            />
          </div>
        </article>
      </div>
    </template>

    <template #footer>
      <button
        @click="$emit('save')"
        class="px-6 py-2 ml-2 bg-blue-600 dark:bg-blue-600 text-blue-100 dark:text-blue-200 rounded"
      >
        Guardar
      </button>
    </template>
  </DialogModal>
</template>
