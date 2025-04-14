<script setup lang="ts">
// Imports
import AccountLinkLayout from "@/Layouts/AccountLinkLayout.vue";
import ModalSelectAccount from "../ModalSelectAccount.vue";
import { MagnifyingGlassIcon } from "@heroicons/vue/24/solid";
import { router } from "@inertiajs/vue3";
import { ref } from "vue";
import { TrashIcon, PencilIcon } from "@heroicons/vue/24/outline";
import { ConfirmationModal, SecondaryButton, PrimaryButton, Table } from "@/Components";
import { Account, ActiveType } from "@/types";

// Props
const props = defineProps<{
  accounts: Account[];
  activeTypes: ActiveType[];
}>();

// Refs
const modal = ref(true);
const modaldelete = ref(true);
const activeTypeId = ref(0);
const activeTypeName = ref("");
const accounts = ref(props.accounts);

const toggle = () => {
  modal.value = !modal.value;
};

const toggle1 = () => {
  modaldelete.value = !modaldelete.value;
};

const editActiveType = (activeTypeIdEdit: number, activeTypeNameEdit: string) => {
  accounts.value = Array.isArray(props.accounts)
    ? props.accounts.filter((acc) => {
      const prefix =
        activeTypeNameEdit === "account_dep_spent_id" ? "5" : "1";
      return acc.code.startsWith(prefix);
    })
    : [];
  activeTypeId.value = activeTypeIdEdit;
  activeTypeName.value = activeTypeNameEdit;
  toggle();
};

const selectAccount = (accountId: number) => {
  router.put(
    route("settingaccount.update", activeTypeId.value),
    { name: activeTypeName.value, account_id: accountId },
    { preserveState: true }
  );
  toggle();
};

const removeVinculation = (activeTypeIdD: number, activeTypeNameD: string) => {
  activeTypeId.value = activeTypeIdD;
  activeTypeName.value = activeTypeNameD;
  toggle1();
};

const handleInputChange = () => {
  router.put(
    route("settingaccount.update", activeTypeId.value),
    { name: activeTypeName.value, account_id: null },
    { preserveState: true }
  );
};
</script>

<template>
  <AccountLinkLayout title="Vinculacion de Cuentas">
    <Table>
      <thead>
        <tr>
          <th class="w-10">N.</th>
          <th class="text-left">DESCRIPCION</th>
          <th class="text-left">CUENTA ACTIVO FIJO</th>
          <th class="text-left">CUENTA DEP AF</th>
          <th class="text-left">CUENTA GASTO DEP AF</th>
        </tr>
      </thead>
      <tbody class="h-6">
        <tr v-for="(activeType, i) in props.activeTypes" :key="`activeType-${i}`">
          <td>{{ i + 1 }}</td>
          <td class="text-left">{{ activeType.name }}</td>
          <td>
            <div class="flex h-8">
              <input type="text" :value="activeType.a_info ?? ''"
                class="block w-full rounded-l border border-gray-300 px-4 py-2 focus:outline-none" disabled />

              <button class=" px-1 py-1 border border-red-400"
                @click="() => activeType.id && removeVinculation(activeType.id, 'account_id')">
                <TrashIcon class="size-6 text-red-400" />
              </button>
              <button @click="() => activeType.id && editActiveType(activeType.id, 'account_id')"
                class="bg-slate-500 rounded-r text-white px-3 py-2 hover:bg-slate-600 focus:outline-none">
                <MagnifyingGlassIcon class="size-4 text-white stroke-[3px]" />
              </button>
            </div>
          </td>
          <td>
            <div class="flex h-8">
              <input type="text" :value="activeType.ad_info ?? ''"
                class="block w-full rounded-l border border-gray-300 px-4 py-2 focus:outline-none" disabled />

              <button class=" px-1 py-1 border border-red-400"
                @click="() => activeType.id && removeVinculation(activeType.id, 'account_dep_id')">
                <TrashIcon class="size-6 text-red-400" />
              </button>
              <button @click="() => activeType.id && editActiveType(activeType.id, 'account_dep_id')"
                class="bg-slate-500 rounded-r text-white px-3 py-2 hover:bg-slate-600 focus:outline-none">
                <MagnifyingGlassIcon class="size-4 text-white stroke-[3px]" />
              </button>
            </div>
          </td>
          <td>
            <div class="flex h-8">
              <input type="text" :value="activeType.ads_info ?? ''"
                class="block w-full rounded-l border border-gray-300 px-4 py-2 focus:outline-none" disabled />
              <button class=" px-1 py-1 border border-red-400" @click="()=>activeType.id &&
                removeVinculation(activeType.id, 'account_dep_spent_id')
                ">
                <TrashIcon class="size-6 text-red-400" />
              </button>

              <button @click="()=> activeType.id && editActiveType(activeType.id, 'account_dep_spent_id')"
                class="bg-slate-500 rounded-r text-white px-3 py-2 hover:bg-slate-600 focus:outline-none">
                <MagnifyingGlassIcon class="size-4 text-white stroke-[3px]" />
              </button>
            </div>
          </td>
        </tr>
      </tbody>
    </Table>
  </AccountLinkLayout>

  <ModalSelectAccount :show="modal" :filteredAccountsFromMain="accounts" @close="toggle"
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
