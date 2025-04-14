<script setup lang="ts">
// Imports
import AccountLinkLayout from "@/Layouts/AccountLinkLayout.vue";
import ModalSelectAccount from "../ModalSelectAccount.vue";
import { MagnifyingGlassIcon } from "@heroicons/vue/24/solid";
import { router, usePage } from "@inertiajs/vue3";
import { ref, computed } from "vue";
import axios from "axios";
import { TrashIcon, PencilIcon } from "@heroicons/vue/24/outline";
import { ConfirmationModal, SecondaryButton, PrimaryButton, Table } from "@/Components";
import { Account, Box, MovementType } from "@/types";

// Props
const props = defineProps<{
  ingres: MovementType[];
  egres: MovementType[];
  accounts: Account[];
  boxes: Box[];
}>();

// Refs
const modal = ref(true);
const modaldelete = ref(true);
const boxId = ref(0);
const boxName = ref("");
const boxtable = ref("");
const page = usePage();
const errors = computed(() => page.props.errors);

const toggle = () => {
  modal.value = !modal.value;
};

const toggle1 = () => {
  modaldelete.value = !modaldelete.value;
};

const editBoxAccount = (boxIdEdit: number, boxNameEdit: string, boxTableEdit: string) => {
  // Actualizar los valores según el tipo de cuenta
  boxId.value = boxIdEdit;
  boxName.value = boxNameEdit;
  boxtable.value = boxTableEdit;

  // Alternar el estado de la modal
  toggle();
};

const selectAccount = (accountId: number) => {
  router.put(
    route("settingaccount.box.update", boxId.value),
    { name: boxName.value, account_id: accountId, table: boxtable.value },
    { preserveState: true }
  );
  toggle();
};

const removeVinculation = (boxIdD: number, boxNameD: string, boxtableD: string) => {
  boxId.value = boxIdD;
  boxName.value = boxNameD;
  boxtable.value = boxtableD;
  toggle1();
};

const handleInputChange = () => {
  router.put(
    route("settingaccount.box.update", boxId.value),
    { name: boxName.value, account_id: null, table: boxtable.value },
    { preserveState: true }
  );

};
</script>

<template>
  <AccountLinkLayout title="Vinculación de Cuentas">
    <div v-if="errors?.warning" class="bg-red-500 text-white rounded mb-4 p-2">
      {{ errors.warning }}
    </div>
    <h2>CAJAS</h2>
    <Table>
      <thead>
        <tr>
          <th class="w-10">N.</th>
          <th class="text-left">CAJAS</th>
          <th class="text-left">CUENTA ASOCIADA</th>
        </tr>
      </thead>
      <tbody class="h-6">
        <tr v-for="(box, i) in props.boxes" :key="`box-${i}`">
          <td>{{ i + 1 }}</td>
          <td class="text-left">
            {{ box.name }}
          </td>
          <td>
            <div class="flex h-8">
              <input type="text" :value="box.ap_info ?? ''"
                class="block w-full rounded-l border border-gray-300 px-4 py-2 focus:outline-none" disabled />
              <button class="px-1 py-1 border border-red-400"
                @click="() => box.id && removeVinculation(box.id, 'account_id', 'Box')">
                <TrashIcon class="size-6 text-red-400" />
              </button>
              <button @click="() => box.id && editBoxAccount(box.id, 'account_id', 'Box')"
                class="bg-slate-500 rounded-r text-white px-3 py-2 hover:bg-slate-600 focus:outline-none">
                <MagnifyingGlassIcon class="size-4 text-white stroke-[3px]" />
              </button>
            </div>
          </td>
        </tr>
      </tbody>
    </Table>

    <h2>Ingresos</h2>
    <Table>
      <thead>
        <tr>
          <th class="w-10">N.</th>
          <th class="text-left">DESCRIPCION</th>
          <th class="text-left">CUENTA ASOCIADA</th>
        </tr>
      </thead>
      <tbody class="h-6">
        <tr v-for="(ingress, i) in props.ingres" :key="`igress-${i}`">
          <td>{{ i + 1 }}</td>
          <td class="text-left">{{ ingress.name }}</td>
          <td>
            <div class="flex h-8">
              <input type="text" :value="ingress.ap_info ?? ''"
                class="block w-full rounded-l border border-gray-300 px-4 py-2 focus:outline-none" disabled />
              <button class="px-1 py-1 border border-red-400" @click="() => ingress.id &&
                removeVinculation(ingress.id, 'account_id', 'MovementType')
              ">
                <TrashIcon class="size-6 text-red-400" />
              </button>
              <button @click="() => ingress.id &&
                editBoxAccount(ingress.id, 'account_id', 'MovementType')
              " class="bg-slate-500 rounded-r text-white px-3 py-2 hover:bg-slate-600 focus:outline-none">
                <MagnifyingGlassIcon class="size-4 text-white stroke-[3px]" />
              </button>
            </div>
          </td>
        </tr>
      </tbody>
    </Table>

    <h2>Egresos</h2>
    <hr class="mt-1" />
    <Table>
      <thead>
        <tr>
          <th class="w-10">N.</th>
          <th class="text-left">DESCRIPCION</th>
          <th class="text-left">CUENTA ASOCIADA</th>
        </tr>
      </thead>
      <tbody class="h-6">
        <tr v-for="(egress, i) in props.egres" :key="`egress-${i}`">
          <td>{{ i + 1 }}</td>
          <td class="text-left">{{ egress.name }}</td>
          <td>
            <div class="flex h-8">
              <input type="text" :value="egress.ap_info ?? ''"
                class="block w-full rounded-l border border-gray-300 px-4 py-2 focus:outline-none" disabled />
              <button class="px-1 py-1 border border-red-400" @click="()=>egress.id &&
                removeVinculation(egress.id, 'account_id', 'MovementType')
                ">
                <TrashIcon class="size-6 text-red-400" />
              </button>
              <button @click="()=> egress.id && editBoxAccount(egress.id, 'account_id', 'MovementType')"
                class="bg-slate-500 rounded-r text-white px-3 py-2 hover:bg-slate-600 focus:outline-none">
                <MagnifyingGlassIcon class="size-4 text-white stroke-[3px]" />
              </button>
            </div>
          </td>
        </tr>
      </tbody>
    </Table>
  </AccountLinkLayout>

  <ModalSelectAccount :show="modal" :filteredAccountsFromMain="props.accounts" @close="toggle"
    @selectAccount="selectAccount" />

  <ConfirmationModal :show="modaldelete">
    <template #title> ELIMINAR VINCULACION </template>
    <template #content>
      Esta seguro de eliminar la vinculación de la cuenta?
    </template>
    <template #footer>
      <SecondaryButton class="mr-2" type="button" @click="toggle1()">Cancelar</SecondaryButton>
      <PrimaryButton type="button" @click="handleInputChange()">Aceptar</PrimaryButton>
    </template>
  </ConfirmationModal>
</template>
