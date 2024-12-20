<script setup>
// Imports
import DialogModal from "@/Components/DialogModal.vue";
import InputError from "@/Components/InputError.vue";
import TextInput from "@/Components/TextInput.vue";
import InputLabel from "@/Components/InputLabel.vue";
import Checkbox from "@/Components/Checkbox.vue";
import { useFocusNextField } from "@/composables/useFocusNextField";

// Props
defineProps({
  paymentRol: { type: Object, default: () => ({}) },
  error: { type: Object, default: () => ({}) },
  show: { type: Boolean, default: false },
  roleingress: { type: Array, default: () => [] },
  roleegress: { type: Array, default: () => [] },
});

const { focusNextField } = useFocusNextField();
// Emits
defineEmits(["close", "save"]);
</script>

<template>
  <DialogModal :show="show" maxWidth="2xl" @close="$emit('close')">
    <template #title>
      Editar rol de {{ paymentRol.cuit }} / {{ paymentRol.name }}
    </template>
    <template #content>
      <div class="mt-4 flex flex-col sm:flex-row gap-4 w-full">
        <article class="rounded">
          <h2 class="text-xl font-bold">Ingresos</h2>
          <div
            v-for="ingress in roleingress"
            :key="ingress.id"
            class="col-2 flex mt-2"
          >
            <span
              class="w-4/6 px-4 bg-slate-100 flex items-center border rounded-l"
              >{{ ingress.name }}</span
            >
            <input
              value="0"
              type="number"
              placeholder=""
              class="block w-2/6 border border-gray-300 px-4 py-1 focus:outline-none text-right rounded-r"
            />
          </div>
        </article>

        <article class="">
          <h2 class="text-xl font-bold">Egresos</h2>
          <div
            v-for="egress in roleegress"
            :key="egress.id"
            class="col-2 flex mt-2"
          >
            <span
              class="w-4/6 px-4 bg-slate-100 flex items-center border rounded-l"
              >{{ egress.name }}</span
            >
            <input
              value="0"
              type="number"
              placeholder=""
              class="block w-2/6 border border-gray-300 px-4 py-1 focus:outline-none text-right rounded-r"
              :disabled="egress.name === 'IESS PATRONAL'"
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