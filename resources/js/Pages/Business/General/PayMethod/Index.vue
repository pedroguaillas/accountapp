<script setup>
import { ref, computed } from "vue";
import axios from "axios";
import GeneralSetting from "@/Layouts/GeneralSetting.vue";
import ModalPayMethod from "./ModalPayMethod.vue";
import { router } from "@inertiajs/vue3";
import Table from "@/Components/Table.vue";
import { TrashIcon, PencilIcon } from "@heroicons/vue/24/solid";

// Recibes 'paymethods' desde Inertia
const props = defineProps({
  globalPayMethods: { type: Array, default: () => [] },
  payMethods: { type: Array, default: () => [] },
});

const modal = ref(false);

const toggle = () => {
  modal.value = !modal.value;
};

const selectedPayMethod = (payMethod) => {
  router.post(route("busssines.setting.paymethods.store"), payMethod, {
    preserveState: true,
  });
  toggle();
};
</script>

<template>
  <GeneralSetting title="Métodos de pago">
    <div class="p-4 bg-white rounded drop-shadow-md">
      <div class="w-full flex sm:justify-end">
        <button
          @click="toggle"
          class="mt-2 sm:mt-0 px-2 bg-success dark:bg-green-600 hover:bg-successhover text-2xl text-white rounded font-bold"
        >
          +
        </button>
      </div>

      <div class="w-full overflow-x-auto">
        <!-- Tabla -->
        <Table>
          <thead>
            <tr class="[&>th]:py-2">
              <th class="w-1">N°</th>
              <th>CODIGO</th>
              <th class="text-left">NOMBRE</th>
              <th>ESTADO</th>
              <th>VALOR MAXIMO</th>
              <th class="w-1"></th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="(payMethod, i) in props.payMethods"
              :key="payMethod.id"
              class="border-t [&>td]:py-2"
            >
              <td>{{ i + 1 }}</td>
              <td>{{ payMethod.code }}</td>
              <td class="text-left">{{ payMethod.name }}</td>
              <td>
                <span class="rounded px-2 py-1 m-0 text-white bg-success">
                  Activo
                </span>
              </td>
              <td>{{ payMethod.max }}</td>

              <td class="flex justify-end">
                <div class="relative inline-flex gap-1">
                  <button
                    class="rounded px-1 py-1 bg-danger hover:bg-dangerhover text-white"
                  >
                    <TrashIcon class="size-6 text-white" />
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </Table>
      </div>
    </div>
  </GeneralSetting>

  <ModalPayMethod
    :show="modal"
    :payMethods="props.globalPayMethods"
    @close="toggle"
    @save="save"
    @selectedPayMethod="selectedPayMethod"
  />
</template>
