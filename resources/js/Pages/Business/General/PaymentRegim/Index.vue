<script setup lang="ts">
import { ref } from "vue";
import GeneralSetting from "@/Layouts/GeneralSetting.vue";
import ModalPaymentRegim from "./ModalPaymentRegim.vue";
import { router } from "@inertiajs/vue3";
import { Table } from "@/Components";
import { TrashIcon, PencilIcon } from "@heroicons/vue/24/solid";
import { PayRegim } from "@/types";

// Recibes 'paymethods' desde Inertia
const props = defineProps<{
  globalPaymentRegims: PayRegim[];
  paymentRegims: PayRegim[];
}>();

const modal = ref(false);

const toggle = () => {
  modal.value = !modal.value;
};

const selectedPaymentRegim = (paymentRegim:PayRegim) => {
  router.post(route("busssines.setting.paymentregims.store"), paymentRegim, {
    preserveState: true,
  });
  toggle();
};
</script>

<template>
  <GeneralSetting title="Iess">
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
              <th>Region</th>
              <th class="text-left">XIII</th>
              <th class="text-left">XVI</th>
              <th class="text-left">FONDOS DE RESERVA</th>
              <th>ESTADO</th>
              <th class="w-1"></th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="(paymentRegim, i) in props.paymentRegims"
              :key="paymentRegim.id"
              class="border-t [&>td]:py-2"
            >
              <td>{{ i + 1 }}</td>
              <td>{{ paymentRegim.region }}</td>
              <td class="text-left">{{ paymentRegim.months_xiii }}</td>
              <td class="text-left">{{ paymentRegim.months_xiv }}</td>
              <td>{{ paymentRegim.months_reserve_funds }}</td>
              <td>
                <span class="rounded px-2 py-1 m-0 text-white bg-success">
                  Activo
                </span>
              </td>

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

  <ModalPaymentRegim
    :show="modal"
    :globalPaymentRegims="props.globalPaymentRegims"
    @close="toggle"
    @selectedPaymentRegim="selectedPaymentRegim"
  />
</template>
