<script setup lang="ts">
import { ref, watch, computed } from "vue";
import { TextInput, Table, DialogModal } from "@/Components";
import { PeopleResponse, Person } from "@/types";

const props = defineProps<{
  people: PeopleResponse; // Paginación de los bancos
  show: boolean;
  search: string;
  page: number;
}>();

const totalPages = computed(() => props.people?.last_page ?? 1);
const searchLocal = ref(props.search);

watch(
  () => props.search,
  (newValue) => {
    searchLocal.value = newValue;
  }
);

const emit = defineEmits([
  "close",
  "selectPerson",
  "nextPage",
  "prevPage",
  "update:search",
]);
</script>

<template>
  <DialogModal :show="show" maxWidth="lg" @close="emit('close')">
    <template #title>Seleccionar una persona</template>
    <template #content>
      <div class="w-full flex sm:justify-between">
        <!-- Campo de búsqueda -->
        <TextInput v-model="searchLocal" type="text" placeholder="Buscar persona..."
          class="w-full p-2 border border-gray-300 rounded-md mb-2 text-sm"
          @input="emit('update:search', searchLocal)" />
      </div>

      <!-- Tabla -->
      <Table class="mt-4 text-xs sm:text-sm table-auto w-full text-center text-gray-700">
        <thead>
          <tr class="[&>th]:py-2">
            <th class="w-1">N°</th>
            <th class="text-left">IDENTIFICACIÓN</th>
            <th class="text-left">NOMBRE</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(person, index) in props.people.data" :key="person.id"
            class="border-t [&>td]:py-2 cursor-pointer hover:bg-gray-100" @click="emit('selectPerson', person)">
            <td>{{ index + 1 }}</td>
            <td class="text-left">{{ person.identification }}</td>
            <td class="text-left">{{ person.name }}</td>
          </tr>
        </tbody>
      </Table>

      <!-- Paginación -->
      <div class="flex justify-between items-center mt-4">
        <button @click="emit('prevPage')" class="px-3 py-1 border rounded bg-gray-200 hover:bg-gray-300">
          Anterior
        </button>
        <span class="text-sm">Página {{ props.people.current_page }} de {{ totalPages }}</span>
        <button @click="emit('nextPage')" class="px-3 py-1 border rounded bg-gray-200 hover:bg-gray-300">
          Siguiente
        </button>
      </div>
    </template>
  </DialogModal>
</template>
