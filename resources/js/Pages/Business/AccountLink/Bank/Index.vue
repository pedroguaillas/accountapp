<script setup>
// Imports
import Table from "@/Components/Table.vue";
import AccountLinkLayout from "@/Layouts/AccountLinkLayout.vue";
import ModalSelectAccount from "../ModalSelectAccount.vue";
import { MagnifyingGlassIcon } from "@heroicons/vue/24/solid";
import ConfirmationModal from "@/Components/ConfirmationModal.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import { Link, router,usePage } from "@inertiajs/vue3";
import { ref, computed } from "vue";
import axios from "axios";
import { TrashIcon, PencilIcon } from "@heroicons/vue/24/outline";

// Props
const props = defineProps({
  accountbanck: { type: Array, default: () => [] },
  bankingres: { type: Array, default: () => [] },
  bankegres: { type: Array, default: () => [] },
  accounts: { type: Array, default: () => [] },
  boxes:{ type: Array, default: () => [] },
});

// Refs
const modal = ref(true);
const modaldelete = ref(true);
const bankId = ref(0);
const bankName = ref("");
const accounts = ref(props.accounts);
const table = ref();
const page = usePage();
const errors = computed(() => page.props.errors);

const toggle = () => {
  modal.value = !modal.value;
};

const toggle1 = () => {
  modaldelete.value = !modaldelete.value;
};

const editBankAccount = (bankIdEdit, bankNameEdit, tableEdit) => {
  // Filtrar cuentas según el roleName
  accounts.value = Array.isArray(props.accounts)
    ? props.accounts.filter((acc) => {
        const prefixes = bankNameEdit === "account_id" ? ["1", "2", "5"] : [];

        return prefixes.some((prefix) => acc.code.startsWith(prefix));
      })
    : [];

  // Actualizar los valores según el tipo de cuenta
  bankId.value = bankIdEdit;
  bankName.value = bankNameEdit;
  table.value = tableEdit;
  console.log(table.value);

  // Alternar el estado de la modal
  toggle();
};

const selectAccount = (accountId) => {
  console.log(bankName.value);
  router.put(
    route("settingaccount.bank.update", bankId.value),
    { name: bankName.value, account_id: accountId, table: table.value },
    { preserveState: true }
  );
  toggle();
};

const removeVinculation = (bankIdD, bankNameD, tableD) => {
  bankId.value = bankIdD;
  bankName.value = bankNameD;
  table.value = tableD;
  toggle1();
};

const handleInputChange = () => {
  axios
    .put(
      route("settingaccount.bank.update", bankId.value),
      { name: bankName.value, account_id: null, table: table.value },
      { preserveState: true }
    ) // Eliminar centro de costos
    .then(() => {
      // Después de eliminar el centro de costos, redirigir a la ruta deseada
      router.visit(route("setting.account.bank.index"));
    })
    .catch((error) => {
      console.error("Error al eliminar la vinculacion", error);
    });
};
</script>

<template>
  <AccountLinkLayout title="Vinculación de Cuentas">
      <div v-if="errors?.warning" class="bg-red-500 text-white rounded mb-4 p-2">
      {{ errors.warning }}
    </div>
    <h2>CUENTAS BANCARIAS</h2>
    <Table>
      <thead>
        <tr>
          <th class="w-10">N.</th>
          <th class="text-left">CUENTA</th>
          <th class="text-left">CUENTA ASOCIADA</th>
        </tr>
      </thead>
      <tbody class="h-6">
        <tr
          v-for="(accountbanck, i) in props.accountbanck"
          :key="`accountbanck-${i}`"
        >
          <td>{{ i + 1 }}</td>
          <td class="text-left">
            {{ accountbanck.account_number + " " + accountbanck.bankname }}
          </td>
          <td>
            <div class="flex h-8">
              <input
                type="text"
                :value="accountbanck.ap_info ?? ''"
                class="block w-full rounded-l border border-gray-300 px-4 py-2 focus:outline-none"
                disabled
              />
              <button
                class="px-1 py-1 border border-red-400"
                @click="
                  removeVinculation(
                    accountbanck.id,
                    'account_id',
                    'BankAccount'
                  )
                "
              >
                <TrashIcon class="size-6 text-red-400" />
              </button>
              <button
                @click="
                  editBankAccount(accountbanck.id, 'account_id', 'BankAccount')
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
        <tr
          v-for="(bankingIngress, i) in props.bankingres"
          :key="`bankingIngress-${i}`"
        >
          <td>{{ i + 1 }}</td>
          <td class="text-left">{{ bankingIngress.name }}</td>
          <td>
            <div class="flex h-8">
              <input
                type="text"
                :value="bankingIngress.ap_info ?? ''"
                class="block w-full rounded-l border border-gray-300 px-4 py-2 focus:outline-none"
                disabled
              />
              <button
                class="px-1 py-1 border border-red-400"
                @click="
                  removeVinculation(
                    bankingIngress.id,
                    'account_id',
                    'MovementType'
                  )
                "
              >
                <TrashIcon class="size-6 text-red-400" />
              </button>
              <button
                @click="
                  editBankAccount(
                    bankingIngress.id,
                    'account_id',
                    'MovementType'
                  )
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
        <tr
          v-for="(bankingEgress, i) in props.bankegres"
          :key="`bankingEgress-${i}`"
        >
          <td>{{ i + 1 }}</td>
          <td class="text-left">{{ bankingEgress.name }}</td>
          <td>
            <div class="flex h-8">
              <input
                type="text"
                :value="bankingEgress.ap_info ?? ''"
                class="block w-full rounded-l border border-gray-300 px-4 py-2 focus:outline-none"
                disabled
              />
              <button
                class="px-1 py-1 border border-red-400"
                @click="
                  removeVinculation(
                    bankingEgress.id,
                    'account_id',
                    'MovementType'
                  )
                "
              >
                <TrashIcon class="size-6 text-red-400" />
              </button>
              <button
                @click="
                  editBankAccount(
                    bankingEgress.id,
                    'account_id',
                    'MovementType'
                  )
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
  </AccountLinkLayout>

  <ModalSelectAccount
    :show="modal"
    :filteredAccountsFromMain="accounts"
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
