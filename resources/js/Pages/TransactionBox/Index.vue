<script setup>
// Imports
import { ref, watch } from "vue";
import AdminLayout from "@/Layouts/AdminLayout.vue";
import { router, Link } from "@inertiajs/vue3";
import Table from "@/Components/Table.vue";
import axios from "axios";
import ConfirmationModal from "@/Components/ConfirmationModal.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import Paginate from "@/Components/Paginate.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import { TrashIcon, PencilIcon } from "@heroicons/vue/24/solid";

// Props
const props = defineProps({
  transactionboxes: { type: Object, default: () => ({}) },
  filters: { type: Object, default: () => ({}) },
});

// Refs
const modal1 = ref(false);
const deleteid = ref(0);
const search = ref(props.filters.search); // Término de búsqueda
const loading = ref(false); // Estado de carga

const toggle1 = () => {
  modal1.value = !modal1.value;
};

const removeTransactionBox = (transactionId) => {
  toggle1();
  deleteid.value = transactionId;
};

const deleteTransaction = () => {
  console.log(deleteid.value);
  axios
    .delete(route("transaction.boxes.delete", deleteid.value))
    .then(() => {
      router.visit(route("transaction.boxes.index"));
    })
    .catch((error) => {
      if (error.response) {
        console.error("Error del backend:", error.response.data);
        alert(
          error.response.data.error ||
            "Ocurrió un error al eliminar la transacción."
        );
      }
    });
};

watch(
  search,
  async (newQuery) => {
    const url = route("transaction.boxes.index");
    loading.value = true;
    try {
      await router.get(
        url,
        { search: newQuery, page: props.transactionboxes.current_page }, // Mantener la página actual
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
  const url = route("transaction.boxes.index");
  loading.value = true;
  try {
    await router.get(
      url,
      { page, search: search.value },
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
  <AdminLayout title="Movimientos de cajas">
    <!-- Card -->
    <div class="p-4 bg-white rounded drop-shadow-md">
      <!-- Card Header -->
      <div class="flex flex-col sm:flex-row justify-between items-center">
        <h2 class="text-sm sm:text-lg font-bold w-full pb-2 sm:pb-0">
          Movimientos de cajas
        </h2>
        <div class="w-full flex sm:justify-end">
          <TextInput
            v-model="search"
            type="search"
            class="block sm:mr-2 h-8 w-full"
            placeholder="Buscar ..."
          />
        </div>
        <Link
          :href="route('transaction.boxes.create')"
          class="mt-2 sm:mt-0 px-2 bg-success dark:bg-green-600 hover:bg-successhover text-2xl text-white rounded font-bold"
        >
          +
        </Link>
      </div>

      <Table>
        <thead>
          <tr class="[&>th]:py-2">
            <th class="w-1">N°</th>
            <th class="text-left p-1">CAJA</th>
            <th class="text-left p-1">DESCRIPCION</th>
            <th class="text-left p-1">TIPO</th>
            <th class="text-right p-1">MONTO</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="(transaction, i) in props.transactionboxes.data"
            :key="transaction.id"
            class="border-t [&>td]:py-2"
          >
            <td>{{ i + 1 }}</td>
            <td class="text-left p-1">
              {{ transaction.name }}
            </td>
            <td class="text-left p-1">{{ transaction.description }}</td>
            <td class="text-left p-1">{{ transaction.type }}</td>
            <td class="text-right p-1">{{ transaction.amount }}</td>
            
          </tr>
        </tbody>
      </Table>
    </div>
    <Paginate :page="props.transactionboxes" @page-change="handlePageChange" />
  </AdminLayout>

  <ConfirmationModal :show="modal1">
    <template #title> ELIMINAR MOVIMIENTO DE CAJA</template>
    <template #content>
      ¿Está seguro de eliminar este movimiento de caja?
    </template>
    <template #footer>
      <SecondaryButton @click="modal1 = !modal1" class="mr-2"
        >Cancelar</SecondaryButton
      >
      <PrimaryButton type="button" @click="deleteTransaction"
        >Aceptar</PrimaryButton
      >
    </template>
  </ConfirmationModal>
</template>
