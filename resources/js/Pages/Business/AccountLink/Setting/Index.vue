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
  activeTypes: { type: Array, default: () => [] },
  accounts: { type: Array, default: () => [] },
});

// Refs
const modal = ref(true);
const activeTypeId = ref(0);
const activeTypeName = ref("");
const accounts = ref(props.accounts);

const toggle = () => {
  modal.value = !modal.value;
};

const editActiveType = (activeTypeIdEdit, activeTypeNameEdit) => {
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

const selectAccount = (accountId) => {
  router.put(
    route("settingaccount.update", activeTypeId.value),
    { name: activeTypeName.value, account_id: accountId },
    { preserveState: true }
  );
  toggle();
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
        <tr
          v-for="(activeType, i) in props.activeTypes"
          :key="`activeType-${i}`"
        >
          <td>{{ i + 1 }}</td>
          <td class="text-left">{{ activeType.name }}</td>
          <td>
            <div class="flex h-8">
              <input
                type="text"
                :value="activeType.a_info ?? ''"
                class="block w-full rounded-l border border-gray-300 px-4 py-2 focus:outline-none"
              />
              <button
                @click="editActiveType(activeType.id, 'account_id')"
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
                :value="activeType.ad_info ?? ''"
                class="block w-full rounded-l border border-gray-300 px-4 py-2 focus:outline-none"
              />
              <button
                @click="editActiveType(activeType.id, 'account_dep_id')"
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
                :value="activeType.ads_info ?? ''"
                class="block w-full rounded-l border border-gray-300 px-4 py-2 focus:outline-none"
              />
              <button
                @click="editActiveType(activeType.id, 'account_dep_spent_id')"
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
