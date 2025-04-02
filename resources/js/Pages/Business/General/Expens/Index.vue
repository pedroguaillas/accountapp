<script setup>
import { ref, reactive } from "vue";
import GeneralSetting from "@/Layouts/GeneralSetting.vue";
import ModalExpens from "./ModalExpens.vue";
import { router, useForm } from "@inertiajs/vue3";
import Table from "@/Components/Table.vue";
import { TrashIcon, PencilIcon } from "@heroicons/vue/24/solid";

// Recibes 'paymethods' desde Inertia
const props = defineProps({
  expenses: { type: Array, default: () => [] },
});

const modal = ref(false);

const initialExpense = {
  name: "",
};
const expense = useForm(initialExpense);
const errorForm = reactive({});

const toggle = () => {
  modal.value = !modal.value;
};

const save = () => {
  expense.post(route("busssines.setting.expenses.store"), {
    preserveState: true,
    onSuccess: () => toggle(),
    onError: (errors) => console.error(errors),
  });
};
</script>

<template>
  <GeneralSetting title="Gastos">
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
              <th class="text-left">NOMBRE</th>
              <th>ESTADO</th>
              <th class="w-1"></th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="(expense, i) in props.expenses"
              :key="expense.id"
              class="border-t [&>td]:py-2"
            >
              <td>{{ i + 1 }}</td>
              <td class="text-left">{{ expense.name }}</td>
              <td>
                <span class="rounded px-2 py-1 m-0 text-white bg-success">
                  Activo
                </span>
              </td>

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

  <ModalExpens
    :show="modal"
    :expense="expense"
    :error="errorForm"
    @close="toggle"
    @save="save"
  />
</template>
