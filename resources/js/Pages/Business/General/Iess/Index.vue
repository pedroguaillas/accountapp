<script setup lang="ts">
import { ref } from "vue";
import GeneralSetting from "@/Layouts/GeneralSetting.vue";
import ModalIess from "./ModalIess.vue";
import { router } from "@inertiajs/vue3";
import { TrashIcon, PencilIcon } from "@heroicons/vue/24/solid";
import { Table } from "@/Components";
import { Iess } from "@/types";

// Recibes 'paymethods' desde Inertia
const props = defineProps<{
  globalIess: Iess[];
  iesses: Iess[];
}>();

const modal = ref(false);

const toggle = () => {
  modal.value = !modal.value;
};

const selectedIess = (iess: Iess) => {
  router.post(route("busssines.setting.iess.store"), iess, {
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
              <th>CODIGO</th>
              <th class="text-left">NOMBRE</th>
              <th class="text-left">TIPO</th>
              <th>ESTADO</th>
              <th>PORCENTAJE</th>
              <th class="w-1"></th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="(iess, i) in props.iesses"
              :key="iess.id"
              class="border-t [&>td]:py-2"
            >
              <td>{{ i + 1 }}</td>
              <td>{{ iess.code }}</td>
              <td class="text-left">{{ iess.name }}</td>
              <td class="text-left">{{ iess.type }}</td>
              <td>
                <span class="rounded px-2 py-1 m-0 text-white bg-success">
                  Activo
                </span>
              </td>
              <td>{{ iess.percentage }}</td>

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

  <ModalIess
    :show="modal"
    :iesses="props.globalIess"
    @close="toggle"
    @selectedIess="selectedIess"
  />
</template>
