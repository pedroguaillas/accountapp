<script setup lang="ts">
import { ref } from "vue";
import GeneralSetting from "@/Layouts/GeneralSetting.vue";
import ModalIva from "./ModalIva.vue";
import { router } from "@inertiajs/vue3";
import { TrashIcon, PencilIcon } from "@heroicons/vue/24/solid";
import { Table } from "@/Components";
import { Iva } from "@/types";

// Recibes 'paymethods' desde Inertia
const props = defineProps<{
  globalIvas: Iva[];
  ivas: Iva[];
}>();

const modal = ref(false);

const toggle = () => {
  modal.value = !modal.value;
};

const selectedIva = (iva:Iva) => {
  router.post(route("busssines.setting.ivas.store"), iva, {
    preserveState: true,
  });
  toggle();
};
</script>

<template>
  <GeneralSetting title="Ivas">
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
              <th>PORCENTAJE</th>
              <th class="w-1"></th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="(iva, i) in props.ivas"
              :key="iva.id"
              class="border-t [&>td]:py-2"
            >
              <td>{{ i + 1 }}</td>
              <td>{{ iva.code }}</td>
              <td class="text-left">{{ iva.name }}</td>
              <td>
                <span class="rounded px-2 py-1 m-0 text-white bg-success">
                  Activo
                </span>
              </td>
              <td>{{ iva.percentage }}</td>

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

  <ModalIva
    :show="modal"
    :ivas="props.globalIvas"
    @close="toggle"
    @selectedIva="selectedIva"
  />
</template>
