<script setup>
// Imports
import Table from "@/Components/Table.vue";
import AccountLinkLayout from "@/Layouts/AccountLinkLayout.vue";
import ModalSelectAccount from "../ModalSelectAccount.vue";
import { MagnifyingGlassIcon } from "@heroicons/vue/24/solid";
import { Link, router } from "@inertiajs/vue3";
import { ref } from "vue";

// Props
const props = defineProps({
  roleIngresses: { type: Array, default: () => [] },
  roleEgresses: { type: Array, default: () => [] },
  accounts: { type: Array, default: () => [] },
});

// Refs
const modal = ref(true);
const roleId = ref(0);
const roleName = ref("");
const type=ref("");
const accounts = ref(props.accounts);

const toggle = () => {
  modal.value = !modal.value;
};

const editRole = (roleIdEdit, roleNameEdit, typeEdit) => {
  // Filtrar cuentas según el roleName
  accounts.value = Array.isArray(props.accounts)
    ? props.accounts.filter((acc) => {
        const prefix = roleNameEdit === "account_spent_id"
          ? "5"
          : roleNameEdit === "account_pasive_id"
          ? "2"
          : "1";
        return acc.code.startsWith(prefix);
      })
    : [];

  // Actualizar los valores según el tipo
  roleId.value= roleIdEdit;
  roleName.value = roleNameEdit;
  type.value=typeEdit;

  // Alternar el estado de la modal
  toggle();
};

const selectAccount = (accountId) => {
  router.put(
    route("settingaccount.rol.update", roleId.value),
    { name: roleName.value, account_id: accountId,type: type.value },
    { preserveState: true }
  );
  toggle();
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
          <th class="text-left">CUENTA ACTIVO</th>
          <th class="text-left">CUENTA PASIVO</th>
          <th class="text-left">CUENTA GASTO</th>
        </tr>
      </thead>
      <tbody class="h-6">
        <tr
          v-for="(roleIngress, i) in props.roleIngresses"
          :key="`roleIngress-${i}`"
        >
          <td>{{ i + 1 }}</td>
          <td class="text-left">{{ roleIngress.name }}</td>
          <td>
            <div class="flex h-8">
              <input
                type="text"
                :value="roleIngress.aa_info ?? ''"
                class="block w-full rounded-l border border-gray-300 px-4 py-2 focus:outline-none"
              />
              <button
                @click="editRole(roleIngress.id, 'account_active_id','ingress')"
                class="bg-slate-500 rounded-r text-white px-3 py-2 hover:bg-slate-600 focus:outline-none"
              >
                <MagnifyingGlassIcon class="size-4 text-white stroke-[3px]" />
              </button>
            </div>
          </td>
          <td>
            <div class="flex h-8">
              <input
                type="text"
                :value="roleIngress.ap_info ?? ''"
                class="block w-full rounded-l border border-gray-300 px-4 py-2 focus:outline-none"
              />
              <button
                @click="editRole(roleIngress.id, 'account_pasive_id','ingress')"
                class="bg-slate-500 rounded-r text-white px-3 py-2 hover:bg-slate-600 focus:outline-none"
              >
                <MagnifyingGlassIcon class="size-4 text-white stroke-[3px]" />
              </button>
            </div>
          </td>
          <td>
            <div class="flex h-8">
              <input
                type="text"
                :value="roleIngress.ass_info ?? ''"
                class="block w-full rounded-l border border-gray-300 px-4 py-2 focus:outline-none"
              />
              <button
                @click="editRole(roleIngress.id, 'account_spent_id','ingress')"
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
          <th class="text-left">CUENTA ACTIVO</th>
          <th class="text-left">CUENTA PASIVO</th>
          <th class="text-left">CUENTA GASTO</th>
        </tr>
      </thead>
      <tbody class="h-6">
        <tr
          v-for="(roleEgress, i) in props.roleEgresses"
          :key="`roleEgress-${i}`"
        >
          <td>{{ i + 1 }}</td>
          <td class="text-left">{{ roleEgress.name }}</td>
          <td>
            <div class="flex h-8">
              <input
                type="text"
                :value="roleEgress.aa_info ?? ''"
                class="block w-full rounded-l border border-gray-300 px-4 py-2 focus:outline-none"
              />
              <button
                @click="editRole(roleEgress.id, 'account_active_id','egress')"
                class="bg-slate-500 rounded-r text-white px-3 py-2 hover:bg-slate-600 focus:outline-none"
              >
                <MagnifyingGlassIcon class="size-4 text-white stroke-[3px]" />
              </button>
            </div>
          </td>
          <td>
            <div class="flex h-8">
              <input
                type="text"
                :value="roleEgress.ap_info ?? ''"
                class="block w-full rounded-l border border-gray-300 px-4 py-2 focus:outline-none"
              />
              <button
                @click="editRole(roleEgress.id, 'account_pasive_id','egress')"
                class="bg-slate-500 rounded-r text-white px-3 py-2 hover:bg-slate-600 focus:outline-none"
              >
                <MagnifyingGlassIcon class="size-4 text-white stroke-[3px]" />
              </button>
            </div>
          </td>
          <td>
            <div class="flex h-8">
              <input
                type="text"
                :value="roleEgress.ass_info ?? ''"
                class="block w-full rounded-l border border-gray-300 px-4 py-2 focus:outline-none"
              />
              <button
                @click="editRole(roleEgress.id, 'account_spent_id','egress')"
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
  :filteredAccountsFromMain="accounts"
  @close="toggle"
  @selectAccount="selectAccount"
/>

</template>
