<script setup>
// Imports
import { ref, reactive, watch } from "vue";
import AdminLayout from "@/Layouts/AdminLayout.vue";
import { router, useForm, Link } from "@inertiajs/vue3";
import axios from "axios";
import Table from "@/Components/Table.vue";
import TextInput from "@/Components/TextInput.vue";
import Paginate from "@/Components/Paginate.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import { TrashIcon, PencilIcon } from "@heroicons/vue/24/solid";

//Props
const props = defineProps({
  expenses: { type: Object, default: () => ({}) },
  filters: { type: Object, default: () => ({}) },
});

// Refs
const modal = ref(false);
const search = ref(props.filters.search); // Término de búsqueda
const loading = ref(false); // Estado de carga
const modal1 = ref(false);

const date = new Date().toISOString().split("T")[0];
//El inicializador de objetos
const initialExpense = {
  description: "",
  amount: "",
  expense_id: "",
  payment_type: "",
  payment_method_id:"",
  document:"",
};

// Reactives
const expense = useForm({ ...initialExpense,date });
const errorForm = reactive({});
const deleteid = ref(0);

const newExpense = () => {
  // Reinicio el formularios con valores vacios
  if (expense.id !== undefined) {
    delete expense.id;
  }
  Object.assign(expense, { ...initialExpense, date });
  resetErrorForm();
  // Muestro el modal
  toggle();
};

const resetErrorForm = () => {
  Object.assign(errorForm, initialExpense);
};

const toggle = () => {
  modal.value = !modal.value;
};

const toggle1 = () => {
  modal1.value = !modal1.value;
};

const save = () => {

};

watch(
  search,

  async (newQuery) => {
    const url = route("transaction.expenses.index");
    loading.value = true;

    try {
      await router.get(
        url,
        { search: newQuery, page: props.expenses.current_page }, // Mantener la página actual
        { preserveState: true }
      );
    } catch (error) {
      console.error("Error al filtrar:", error);
    } finally {
      loading.value = false;
    }
  },
  { immediate: false }
);

// Función para manejar el cambio de página
const handlePageChange = async (page) => {
  const url = route("transaction.expenses.index"); // Ruta hacia el backend
  loading.value = true;

  try {
    await router.get(
      url,
      { page, search: search.value }, // Incluye tanto la página como el término de búsqueda
      { preserveState: true }
    );
  } catch (error) {
    console.error("Error al paginar:", error);
  } finally {
    loading.value = false;
  }
};
</script>

<template>
  <AdminLayout title="Gastos">
    <!-- Card -->
    <div class="p-4 bg-white rounded drop-shadow-md">
      <!-- Card Header -->
      <div class="flex flex-col sm:flex-row justify-between items-center">
        <h2 class="text-sm sm:text-lg font-bold w-full pb-2 sm:pb-0">
          Gastos
        </h2>
        <div class="w-full flex justify-end">
          <TextInput
            v-model="search"
            type="text"
            class="mt-1 block w-[50%] mr-2 h-8"
            minlength="3"
            maxlength="300"
            required
            placeholder="Buscar..."
          />
        </div>
         <Link
          :href="route('transaction.expenses.create')"
          class="mt-2 sm:mt-0 px-2 bg-success dark:bg-green-600 hover:bg-successhover text-2xl text-white rounded font-bold"
        >
          +
        </Link>
      </div>

      <!-- Resposive -->
      <div class="w-full overflow-x-auto">
        <!-- Tabla -->
        <Table>
          <thead>
            <tr class="[&>th]:py-2">
              <th class="w-1">N°</th>
              <th class="text-left">GASTO</th>
              <th class="text-left">DESCRIPCION</th>
              <th class="text-right pr-4">MONTO</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="(expense, i) in props.expenses.data"
              :key="expense.id"
              class="border-t [&>td]:py-2"
            >
              <td>{{ i + 1 }}</td>
              <td class="text-left">{{ expense.name }}</td>
              <td class="text-left">{{ expense.description }}</td>
              <td class="text-right pr-4">{{ expense.amount }}</td>
            </tr>
          </tbody>
        </Table>
      </div>
    </div>
    <!-- <Paginate :page="props.expenses" @page-change="handlePageChange" /> -->
  </AdminLayout>
</template>