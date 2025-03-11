<script setup>
// Imports
import Table from "@/Components/Table.vue";
import AccountLinkLayout from "@/Layouts/AccountLinkLayout.vue";
import ModalSelectAccount from "../ModalSelectAccount.vue";
import { MagnifyingGlassIcon } from "@heroicons/vue/24/solid";
import ConfirmationModal from "@/Components/ConfirmationModal.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import { Link, router } from "@inertiajs/vue3";
import { ref } from "vue";
import axios from "axios";
import { TrashIcon, PencilIcon } from "@heroicons/vue/24/outline";

// Props
const props = defineProps({
  accounts: { type: Array, default: () => [] },
  boxes: { type: Array, default: () => [] },
  ingres: { type: Array, default: () => [] },
  egres: { type: Array, default: () => [] },
});

// Refs
const modal = ref(true);
const modaldelete = ref(true);
const boxId = ref(0);
const boxName = ref("");
const boxtable=ref("");

const toggle = () => {
  modal.value = !modal.value;
};

const toggle1 = () => {
  modaldelete.value = !modaldelete.value;
};

const editBoxAccount = (boxIdEdit, boxNameEdit,boxTableEdit) => {
  // Actualizar los valores según el tipo de cuenta
  boxId.value = boxIdEdit;
  boxName.value = boxNameEdit;
  boxtable.value=boxTableEdit;

  // Alternar el estado de la modal
  toggle();
};

const selectAccount = (accountId) => {
  console.log(boxName.value);
  router.put(
    route("settingaccount.box.update", boxId.value),
    { name: boxName.value, account_id: accountId,table:boxtable.value },
    { preserveState: true }
  );
  toggle();
};

const removeVinculation = (boxIdD, boxNameD,boxtableD) => {
  boxId.value = boxIdD;
  boxName.value = boxNameD;
  boxtable.value=boxtableD;
  toggle1();
};

const handleInputChange = () => {
  axios
    .put(
      route("settingaccount.box.update", boxId.value),
      { name: boxName.value, account_id: null,table:boxtable.value },
      { preserveState: true }
    ) // Eliminar centro de costos
    .then(() => {
      // Después de eliminar el centro de costos, redirigir a la ruta deseada
      router.visit(route("setting.account.box.index"));
    })
    .catch((error) => {
      console.error("Error al eliminar la vinculacion", error);
    });
};
</script>

<template>
  <AccountLinkLayout title="Vinculación de Cuentas">
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
              <input
                type="text"
                :value="box.ap_info ?? ''"
                class="block w-full rounded-l border border-gray-300 px-4 py-2 focus:outline-none"
                disabled
              />
              <button
                class="px-1 py-1 border border-red-400"
                @click="removeVinculation(box.id, 'account_id', 'Box')"
              >
                <TrashIcon class="size-6 text-red-400" />
              </button>
              <button
                @click="editBoxAccount(box.id, 'account_id', 'Box')"
                class="bg-slate-500 rounded-r text-white px-3 py-2 hover:bg-slate-600 focus:outline-none"
              >
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
              <input
                type="text"
                :value="ingress.ap_info ?? ''"
                class="block w-full rounded-l border border-gray-300 px-4 py-2 focus:outline-none"
                disabled
              />
              <button
                class="px-1 py-1 border border-red-400"
                @click="
                  removeVinculation(ingress.id, 'account_id', 'MovementType')
                "
              >
                <TrashIcon class="size-6 text-red-400" />
              </button>
              <button
                @click="
                  editBoxAccount(ingress.id, 'account_id', 'MovementType')
                "
                class="bg-slate-500 rounded-r text-white px-3 py-2 hover:bg-slate-600 focus:outline-none"
              >
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
              <input
                type="text"
                :value="egress.ap_info ?? ''"
                class="block w-full rounded-l border border-gray-300 px-4 py-2 focus:outline-none"
                disabled
              />
              <button
                class="px-1 py-1 border border-red-400"
                @click="
                  removeVinculation(egress.id, 'account_id', 'MovementType')
                "
              >
                <TrashIcon class="size-6 text-red-400" />
              </button>
              <button
                @click="editBoxAccount(egress.id, 'account_id', 'MovementType')"
                class="bg-slate-500 rounded-r text-white px-3 py-2 hover:bg-slate-600 focus:outline-none"
              >
                <MagnifyingGlassIcon class="size-4 text-white stroke-[3px]" />
              </button>
            </div>
          </td>
        </tr>
      </tbody>
    </Table>
  </AccountLinkLayout>

  <ModalSelectAccount
    :show="modal"
    :filteredAccountsFromMain="props.accounts"
    @close="toggle"
    @selectAccount="selectAccount"
  />

  <ConfirmationModal :show="modaldelete">
    <template #title> ELIMINAR VINCULACION </template>
    <template #content>
      Esta seguro de eliminar la vinculación de la cuenta?
    </template>
    <template #footer>
      <SecondaryButton class="mr-2" type="button" @click="toggle1()"
        >Cancelar</SecondaryButton
      >
      <PrimaryButton type="button" @click="handleInputChange()"
        >Aceptar</PrimaryButton
      >
    </template>
  </ConfirmationModal>
</template>
