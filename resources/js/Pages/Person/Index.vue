<script setup>
// Imports
import { ref, reactive, watch } from "vue";
import AdminLayout from "@/Layouts/AdminLayout.vue";
import FormModal from "./FormModal.vue";
import { router, useForm } from "@inertiajs/vue3";
import Table from "@/Components/Table.vue";
import axios from "axios";
import ConfirmationModal from "@/Components/ConfirmationModal.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import Paginate from "@/Components/Paginate.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import { TrashIcon, PencilIcon,ListBulletIcon } from "@heroicons/vue/24/solid";

// Props
const props = defineProps({
  people: { type: Object, default: () => ({}) },
  filters: { type: Object, default: () => ({}) },
});

// Refs
const modal = ref(false);
const modal1 = ref(false);
const deleteid = ref(0);
const search = ref(props.filters.search); // Término de búsqueda
const loading = ref(false); // Estado de carga

const toggle1 = () => {
  modal1.value = !modal1.value;
};

// Inicializador de objetos
const initialPerson = {
  identification:"",
  name: "",
  email: "",
  phone:"",
  address:"",

};

// Reactives
const person = useForm({ ...initialPerson });
const errorForm = reactive({ ...initialPerson });

const newPerson = () => {
  if (person.id !== undefined) {
    delete person.id;
  }
  Object.assign(person, initialPerson);
  toggle();
};

const resetErrorForm = () => {
  Object.assign(errorForm, initialPerson);
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
    onError: (error) => {
      resetErrorForm(); // Asegurarte de limpiar los errores previos
      if (error.response?.data?.errors) {
        // Iterar sobre los errores recibidos del servidor
        Object.entries(error.response.data.errors).forEach(([key, value]) => {
          errorForm[key] = value[0]; // Mostrar el primer error asociado a cada campo
        });
      } else {
        console.error("Error desconocido:", error);
        alert("Ocurrió un error inesperado. Por favor, intente nuevamente.");
      }
    },
  });
};

const update = (personEdit) => {
  resetErrorForm();
  Object.keys(personEdit).forEach((key) => {
    person[key] = personEdit[key];
  });
  toggle();
};

const removePerson = (personId) => {
  toggle1();
  deleteid.value = personId;
};

const deletePerson = () => {
  axios
    .delete(route("people.delete", deleteid.value)) // Eliminar centro de costos
    .then(() => {
      // Después de eliminar el centro de costos, redirigir a la ruta deseada
      router.visit(route("people.index"));
    })
    .catch((error) => {
      console.error("Error al eliminar la persona", error);
    });
};

watch(
  search,

  async (newQuery) => {
    const url = route("people.index");
    loading.value = true;
    try {
      await router.get(
        url,
        { search: newQuery, page: props.people.current_page }, // Mantener la página actual
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
  const url = route("people.index"); // Ruta hacia el backend
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
  <AdminLayout title="Persona">
    <!-- Card -->
    <div class="p-4 bg-white rounded drop-shadow-md">
      <!-- Card Header -->
      <div class="flex flex-col sm:flex-row justify-between items-center">
        <h2 class="text-sm sm:text-lg font-bold w-full pb-2 sm:pb-0">
          personas
        </h2>
        <div class="w-full flex sm:justify-end">
          <TextInput
            v-model="search"
            type="search"
            class="block sm:mr-2 h-8 w-full"
            placeholder="Buscar ..."
          />
        </div>
        <button
          @click="newPerson"
          class="mt-2 sm:mt-0 px-2 bg-success dark:bg-green-600 hover:bg-successhover text-2xl text-white rounded font-bold"
        >
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
          <tr
            v-for="(person, i) in props.people.data"
            :key="person.id"
            class="border-t [&>td]:py-2"
          >
            <td>{{ i + 1 }}</td>
            <td class="text-left p-2">{{ person.identification }}</td>
            <td class="text-left">{{ person.name }}</td>
            <td class="text-left">{{ person.address}}</td>

            <td class="flex justify-end">
              <div class="relative inline-flex gap-1">
                <button
                  class="rounded px-1 py-1 bg-danger hover:bg-dangerhover text-white"
                  @click="removePerson(person.id)"
                >
                  <TrashIcon class="size-6 text-white" />
                </button>
                <button
                  class="rounded px-2 py-1 bg-primary hover:bg-primaryhover text-white"
                  @click="update(person)"
                >
                  <PencilIcon class="size-4 text-white" />
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </Table>
    </div>
    <Paginate :page="props.people" @page-change="handlePageChange" />
  </AdminLayout>

  <FormModal
    :show="modal"
    :person="person"
    :error="errorForm"
    @close="toggle"
    @save="save"
  />

  <ConfirmationModal :show="modal1">
    <template #title> ELIMINAR PERSONAS</template>
    <template #content> Esta seguro de eliminar la persona? </template>
    <template #footer>
      <SecondaryButton @click="modal1 = !modal1" class="mr-2"
        >Cancelar</SecondaryButton
      >
      <PrimaryButton type="button" @click="deletePerson">Aceptar</PrimaryButton>
    </template>
  </ConfirmationModal>
</template>
