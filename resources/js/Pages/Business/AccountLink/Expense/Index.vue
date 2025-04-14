<script setup lang="ts">
// Imports
import AccountLinkLayout from "@/Layouts/AccountLinkLayout.vue";
import ModalSelectAccount from "../ModalSelectAccount.vue";
import { MagnifyingGlassIcon } from "@heroicons/vue/24/solid";
import { router, usePage } from "@inertiajs/vue3";
import { ref, computed } from "vue";
import { TrashIcon, PencilIcon } from "@heroicons/vue/24/outline";
import { ConfirmationModal, SecondaryButton, PrimaryButton, Table } from "@/Components";
import { Account } from "@/types";
import { Expense } from "@/types/expense";

// Props
const props = defineProps<{
  accounts: Account[];
  expenses: Expense[];
}>();

// Refs
const modal = ref(true);
const modaldelete = ref(true);
const expenseId = ref(0);
const expenseName = ref("");
const accounts = ref(props.accounts);
const page = usePage();
const errors = computed(() => page.props.errors);

const toggle = () => {
  modal.value = !modal.value;
};

const toggle1 = () => {
  modaldelete.value = !modaldelete.value;
};

const editExpenseAccount = (expenseIdEdit: number, expenseNameEdit: string) => {
  // Filtrar cuentas según el roleName
  accounts.value = Array.isArray(props.accounts)
    ? props.accounts.filter((acc) => {
      const prefixes = expenseNameEdit === "account_id" ? ["1", "2", "5"] : [];

      return prefixes.some((prefix) => acc.code.startsWith(prefix));
    })
    : [];

  // Actualizar los valores según el tipo de cuenta
  expenseId.value = expenseIdEdit;
  expenseName.value = expenseNameEdit;

  // Alternar el estado de la modal
  toggle();
};

const selectAccount = (accountId: number) => {
  console.log(expenseName.value);
  router.put(
    route("settingaccount.expenses.update", expenseId.value),
    { name: expenseName.value, account_id: accountId },
    { preserveState: true }
  );
  toggle();
};

const removeVinculation = (expenseIdD: number, expenseNameD: string) => {
  expenseId.value = expenseIdD;
  expenseName.value = expenseNameD;
  toggle1();
};

const handleInputChange = () => {
  router.put(
    route("settingaccount.expenses.update", expenseId.value),
    { name: expenseName.value, account_id: null },
    { preserveState: true }
  );

};
</script>

<template>
  <AccountLinkLayout title="Vinculación de Cuentas">
    <div v-if="errors?.warning" class="bg-red-500 text-white rounded mb-4 p-2">
      {{ errors.warning }}
    </div>
    <h2>Gastos</h2>
    <Table>
      <thead>
        <tr>
          <th class="w-10">N.</th>
          <th class="text-left">GASTO</th>
          <th class="text-left">CUENTA ASOCIADA</th>
        </tr>
      </thead>
      <tbody class="h-6">
        <tr v-for="(expense, i) in props.expenses" :key="`expense-${i}`">
          <td>{{ i + 1 }}</td>
          <td class="text-left">
            {{ expense.name }}
          </td>
          <td>
            <div class="flex h-8">
              <input type="text" :value="expense.ap_info ?? ''"
                class="block w-full rounded-l border border-gray-300 px-4 py-2 focus:outline-none" disabled />
              <button class="px-1 py-1 border border-red-400" @click="() => expense.id &&
                removeVinculation(
                  expense.id,
                  'account_id',
                )
              ">
                <TrashIcon class="size-6 text-red-400" />
              </button>
              <button @click="() => expense.id &&
                editExpenseAccount(expense.id, 'account_id')
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
