<script setup lang="ts">
// Imports
import { ref, reactive, watch } from "vue";
import AdminLayout from "@/Layouts/AdminLayout.vue";
import FormModal from "./FormModal.vue";
import { router, useForm } from "@inertiajs/vue3";
import { TextInput, Paginate, Table, SecondaryButton, PrimaryButton, ConfirmationModal } from "@/Components";
import { TrashIcon, PencilIcon } from "@heroicons/vue/24/solid";
import { Errors, Filters, GeneralRequest, Person } from "@/types";

// Props
const props = defineProps<{
  people: GeneralRequest<Person>; // Paginación de los bancos
  filters: Filters; // Filtros aplicados
}>();

// Refs
const modal = ref(false);
const modalDelete = ref(false);
const deleteId = ref(0);
const search = ref(props.filters.search); // Término de búsqueda

const toggleDelete = () => {
  modalDelete.value = !modalDelete.value;
};

// Inicializador de objetos
const initialPerson = {
  identification: "",
  name: "",
  email: "",
  phone: "",
  address: "",
};

// Reactives
const person = useForm<Person>({ ...initialPerson });
const errorForm = reactive<Errors>({});

const newPerson = () => {
  resetErrorForm();
  if (person.id !== undefined) {
    delete person.id;
  }
  Object.assign(person, initialPerson);
  toggle();
};

const resetErrorForm = () => {
  for (const key in errorForm) {
    errorForm[key] = "";
  }
};

const toggle = () => {
  modal.value = !modal.value;
};

const save = () => {
  // Determinar el método HTTP y la ruta correspondiente
  const isUpdate = Boolean(person.id);
  const routeMethod = isUpdate ? "put" : "post";
  const routeName = isUpdate
    ? route("people.update", { id: person.id }) // Ruta para actualización
    : route("people.store"); // Ruta para creación

  // Preparar la solicitud
  person[routeMethod](routeName, {
    onSuccess: () => {
      toggle(); // Cerrar modal o reiniciar estados
      resetErrorForm(); // Limpiar errores del formulario
      router.reload({ only: ["people"] }); // Recargar datos
    },
    onError: (error: Errors) => {
      resetErrorForm(); // Asegurarte de limpiar los errores previos
      if (error) {
        // Iterar sobre los errores recibidos del servidor
        Object.entries(error).forEach(([key, value]) => {
          errorForm[key] = value;// Mostrar el primer error asociado a cada campo
        });
      } else {
        console.error("Error desconocido:", error);
        alert("Ocurrió un error inesperado. Por favor, intente nuevamente.");
      }
    },
  });
};

const update = (personEdit: Person) => {
  resetErrorForm();
  Object.keys(personEdit).forEach((key) => {
    person[key] = personEdit[key];
  });
  toggle();
};

const removePerson = (personId: number) => {
  toggleDelete();
  deleteId.value = personId;
};

const deletePerson = () => {
  router.delete(route("people.delete", deleteId.value), {
    onSuccess: () => {
      toggleDelete();
    },
    onError: (error) => {
      console.error("Error al eliminar la persona", error);
    },
  });
};

watch(
  search,

  async (newQuery) => {
    const url = route("people.index");
    try {
      await router.get(
        url,
        { search: newQuery, page: props.people.current_page }, // Mantener la página actual
        { preserveState: true }
      );
    } catch (error) {
      console.error("Error al filtrar:", error);
    }
  },
  { immediate: false }
);
</script>

<template>
  <AdminLayout title="Persona">
    <!-- Card -->
    <div class="p-4 bg-white rounded drop-shadow-md">
      <!-- Card Header -->
      <div class="flex flex-col sm:flex-row justify-between items-center">
        <h2 class="text-sm sm:text-lg font-bold w-full pb-2 sm:pb-0">
          personas
        </h2>
        <div class="w-full flex sm:justify-end">
          <TextInput v-model="search" type="search" class="block sm:mr-2 h-8 w-full" placeholder="Buscar ..." />
        </div>
        <button @click="newPerson"
          class="mt-2 sm:mt-0 px-2 bg-success dark:bg-green-600 hover:bg-successhover text-2xl text-white rounded font-bold">
          +
        </button>
      </div>

      <Table>
        <thead>
          <tr class="[&>th]:py-2">
            <th class="w-1">N°</th>
            <th class="text-left p-2">CEDULA</th>
            <th class="text-left">NOMBRE</th>
            <th class="text-left">DIRECCION</th>

            <th></th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(person, i) in props.people.data" :key="person.id" class="border-t [&>td]:py-2">
            <td>{{ i + 1 }}</td>
            <td class="text-left p-2">{{ person.identification }}</td>
            <td class="text-left">{{ person.name }}</td>
            <td class="text-left">{{ person.address }}</td>

            <td class="flex justify-end">
              <div class="relative inline-flex gap-1">
                <button class="rounded px-1 py-1 bg-danger hover:bg-dangerhover text-white"
                  @click="() => person.id && removePerson(person.id)">
                  <TrashIcon class="size-6 text-white" />
                </button>
                <button class="rounded px-2 py-1 bg-primary hover:bg-primaryhover text-white" @click="update(person)">
                  <PencilIcon class="size-4 text-white" />
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </Table>
    </div>
    <Paginate :page="props.people" />
  </AdminLayout>

  <FormModal :show="modal" :person="person" :error="errorForm" @close="toggle" @save="save" />

  <ConfirmationModal :show="modalDelete">
    <template #title> ELIMINAR PERSONAS</template>
    <template #content> Esta seguro de eliminar la persona? </template>
    <template #footer>
      <SecondaryButton @click="modalDelete = !modalDelete" class="mr-2">Cancelar</SecondaryButton>
      <PrimaryButton type="button" @click="deletePerson">Aceptar</PrimaryButton>
    </template>
  </ConfirmationModal>
</template>
