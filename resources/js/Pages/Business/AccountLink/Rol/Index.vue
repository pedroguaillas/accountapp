<script setup lang="ts">
// Imports
import AccountLinkLayout from "@/Layouts/AccountLinkLayout.vue";
import ModalSelectAccount from "../ModalSelectAccount.vue";
import { MagnifyingGlassIcon } from "@heroicons/vue/24/solid";
import { router } from "@inertiajs/vue3";
import { ref } from "vue";
import axios from "axios";
import { TrashIcon, PencilIcon } from "@heroicons/vue/24/outline";
import { ConfirmationModal, SecondaryButton, PrimaryButton, Table } from "@/Components";
import { Account, RoleEgress, RoleIngress } from "@/types";

// Props
const props = defineProps<{
  accounts: Account[];
  roleIngresses: RoleIngress[];
  roleEgresses: RoleEgress[];
}>();

// Refs
const modal = ref(true);
const modaldelete = ref(true);
const roleId = ref(0);
const roleName = ref("");
const type = ref("");
const accounts = ref(props.accounts);

const toggle = () => {
  modal.value = !modal.value;
};

const toggle1 = () => {
  modaldelete.value = !modaldelete.value;
};

const editRole = (roleIdEdit: number, roleNameEdit: string, typeEdit: string) => {
  // Filtrar cuentas según el roleName
  accounts.value = Array.isArray(props.accounts)
    ? props.accounts.filter((acc) => {
      const prefixes =
        roleNameEdit === "account_counterpart_id"
          ? ["2"] // Convertimos "2" en un array para tratarlo igual
          : ["5", "1"];

      return prefixes.some((prefix) => acc.code.startsWith(prefix));
    })
    : [];

  // Actualizar los valores según el tipo
  roleId.value = roleIdEdit;
  roleName.value = roleNameEdit;
  type.value = typeEdit;

  // Alternar el estado de la modal
  toggle();
};

const selectAccount = (accountId: number) => {
  router.put(
    route("settingaccount.rol.update", roleId.value),
    { name: roleName.value, account_id: accountId, type: type.value },
    { preserveState: true }
  );
  toggle();
};

const removeVinculation = (roleIdD: number, roleNameD: string, typeD: string) => {
  roleId.value = roleIdD;
  roleName.value = roleNameD;
  type.value = typeD;
  toggle1();
};

const handleInputChange = () => {
  router.put(
    route("settingaccount.rol.update", roleId.value),
    { name: roleName.value, account_id: null, type: type.value },
    { preserveState: true }
  );
};
</script>

<template>
  <AccountLinkLayout title="Vinculacion de Cuentas">
    <h2>Ingresos</h2>
    <Table>
      <thead>
        <tr>
          <th class="w-10">N.</th>
          <th class="text-left">DESCRIPCION</th>
          <th class="text-left">PARTIDA</th>
          <th class="text-left">CONTRAPARTIDA</th>
        </tr>
      </thead>
      <tbody class="h-6">
        <tr v-for="(roleIngress, i) in props.roleIngresses" :key="`roleIngress-${i}`">
          <td>{{ i + 1 }}</td>
          <td class="text-left">{{ roleIngress.name }}</td>
          <td>
            <div class="flex h-8">
              <input type="text" :value="roleIngress.ap_info ?? ''"
                class="block w-full rounded-l border border-gray-300 px-4 py-2 focus:outline-none" disabled />
              <button class="px-1 py-1 border border-red-400" @click="() => roleIngress.id &&
                removeVinculation(
                  roleIngress.id,
                  'account_departure_id',
                  'ingress'
                )
              ">
                <TrashIcon class="size-6 text-red-400" />
              </button>
              <button @click="() => roleIngress.id &&
                editRole(roleIngress.id, 'account_departure_id', 'ingress')
              " class="bg-slate-500 rounded-r text-white px-3 py-2 hover:bg-slate-600 focus:outline-none">
                <MagnifyingGlassIcon class="size-4 text-white stroke-[3px]" />
              </button>
            </div>
          </td>
          <td>
            <div class="flex h-8">
              <input type="text" :value="roleIngress.ac_info ?? ''"
                class="block w-full rounded-l border border-gray-300 px-4 py-2 focus:outline-none" disabled />

              <button class="px-1 py-1 border border-red-400" @click="()=> roleIngress.id &&
                removeVinculation(
                  roleIngress.id,
                  'account_counterpart_id',
                  'ingress'
                )
                ">
                <TrashIcon class="size-6 text-red-400" />
              </button>
              <button @click="()=> roleIngress.id &&
                editRole(roleIngress.id, 'account_counterpart_id', 'ingress')
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
          <th class="text-left">PARTIDA</th>
          <th class="text-left">CONTRAPARTIDA</th>
        </tr>
      </thead>
      <tbody class="h-6">
        <tr v-for="(roleEgress, i) in props.roleEgresses" :key="`roleEgress-${i}`">
          <td>{{ i + 1 }}</td>
          <td class="text-left">{{ roleEgress.name }}</td>
          <td>
            <div class="flex h-8">
              <input type="text" :value="roleEgress.ap_info ?? ''"
                class="block w-full rounded-l border border-gray-300 px-4 py-2 focus:outline-none" disabled />
              <button class="px-1 py-1 border border-red-400" @click="()=>roleEgress.id &&
                removeVinculation(
                  roleEgress.id,
                  'account_departure_id',
                  'egress'
                )
                ">
                <TrashIcon class="size-6 text-red-400" />
              </button>
              <button @click="()=>roleEgress.id &&
                editRole(roleEgress.id, 'account_departure_id', 'egress')
                " class="bg-slate-500 rounded-r text-white px-3 py-2 hover:bg-slate-600 focus:outline-none">
                <MagnifyingGlassIcon class="size-4 text-white stroke-[3px]" />
              </button>
            </div>
          </td>
          <td>
            <div class="flex h-8">
              <input type="text" :value="roleEgress.ac_info ?? ''"
                class="block w-full rounded-l border border-gray-300 px-4 py-2 focus:outline-none" disabled />
              <button class="px-1 py-1 border border-red-400" @click="()=> roleEgress.id &&
                removeVinculation(
                  roleEgress.id,
                  'account_counterpart_id',
                  'egress'
                )
                ">
                <TrashIcon class="size-6 text-red-400" />
              </button>
              <button @click="()=>roleEgress.id &&
                editRole(roleEgress.id, 'account_counterpart_id', 'egress')
                " class="bg-slate-500 rounded-r text-white px-3 py-2 hover:bg-slate-600 focus:outline-none">
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
