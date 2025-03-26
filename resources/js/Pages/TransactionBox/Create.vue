<script setup>
import { reactive, computed, ref } from "vue";
import AdminLayout from "@/Layouts/AdminLayout.vue";
import TextInput from "@/Components/TextInput.vue";
import SearchPerson from "./SearchPerson.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import DynamicSelect from "@/Components/DynamicSelect.vue";
import { useForm, router } from "@inertiajs/vue3";

const props = defineProps({
  cashSessions: { type: Array, default: () => [] },
  movementtypes: { type: Array, default: () => [] },
  people: { type: Array, default: () => [] },
  countperson: { type: Number, default: () => 0 },
});

const initialTransaction = {
  cash_session_id: 0,
  movement_type_id: 0,
  amount: "",
  beneficiary_id: "",
  description: "",
  state_transaction: "pendiente",
  box_id: "",
  document: "",
};
const cash_session_id =
  props.cashSessions.length === 1 ? props.cashSessions[0].id : 0;

const beneficiary_id = props.people.length === 1 ? props.people[0].id : 0;

const transaction = useForm({
  ...initialTransaction,
  cash_session_id,
  beneficiary_id,
});
const errorForm = reactive({});

const TypeOptions = [
  { value: "Ingreso", label: "Ingreso" },
  { value: "Egreso", label: "Egreso" },
];

const movementTypeOptions = props.movementtypes.map((type) => ({
  value: type.id,
  label: type.name,
  type: type.type,
  code: type.code,
}));

const selectedBoxType = computed(() => {
  const selectedSession = props.cashSessions.find(
    (session) => session.id == transaction.cash_session_id
  );
  return selectedSession ? selectedSession.box_type : null;
});

const selectedMovementTypecode = computed(() => {
  const selectedSession = props.movementtypes.find(
    (session) => session.id == transaction.movement_type_id
  );
  return selectedSession ? selectedSession.code : null;
});

const selectedType = ref("");

const filteredMovementTypes = computed(() => {
  if (selectedBoxType.value === "general") {
    return movementTypeOptions.filter(
      (type) =>
        type.type === selectedType.value && !["DCG", "RFA"].includes(type.code)
    );
  } else {
    if (selectedBoxType.value === "chica") {
      return movementTypeOptions.filter(
        (type) =>
          type.type === selectedType.value && ["RFA"].includes(type.code)
      );
    } else {
      if (selectedBoxType.value === "otras") {
        return movementTypeOptions.filter(
          (type) =>
            type.type === selectedType.value && ["DCG"].includes(type.code)
        );
      }
    }
  }
});

const peopleOptions = props.people.map((person) => ({
  value: person.id,
  label: person.name,
}));

const cashSessionOptions = props.cashSessions.map((session) => ({
  value: session.id,
  label: `${session.id} - ${session.name}`, // 🔥 Aquí la interpolación es correcta
  type: session.box_type,
}));

const filteredCashSessions = computed(() => {
  console.log();
  if (
    selectedBoxType.value === "general" &&
    selectedMovementTypecode.value === "RCC"
  ) {
    return cashSessionOptions.filter((type) => type.type === "chica");
  } else {
    if (
      selectedBoxType.value === "general" &&
      selectedMovementTypecode.value === "PF"
    ) {
      return cashSessionOptions.filter((type) => type.type === "otras");
    }
  }
  return cashSessionOptions;
});

const save = () => {
  transaction.post(route("transaction.boxes.store"), {
    onSuccess: () => {
      console.log("Guardado con éxito");
      router.visit(route("transaction.boxes.index"));
    },
    onError: (errors) => {
      console.error("Errores:", errors);
      Object.keys(errors).forEach((key) => {
        errorForm[key] = errors[key];
      });
    },
  });
};
</script>

<template>
  <AdminLayout title="Registro de Transacciones">
    <div class="p-4 bg-white rounded drop-shadow-md">
      <h2 class="text-lg font-bold">Registrar Transacción</h2>
      <div class="mt-4">
        <div class="col-span-6 sm:col-span-4">
          <InputLabel for="cash_session_id" value="Sesión de Caja" />
          <DynamicSelect
            v-model="transaction.cash_session_id"
            :options="cashSessionOptions"
            placeholder="Seleccione una sesión"
            class="mt-2 block w-full"
          />
          <InputError :message="errorForm.cash_session_id" class="mt-2" />
        </div>

        <div class="col-span-6 sm:col-span-4">
          <InputLabel for="type" value="Tipo" />
          <DynamicSelect
            v-model="selectedType"
            :options="TypeOptions"
            placeholder="Seleccione un tipo"
            class="mt-2 block w-full"
          />
        </div>

        <div class="col-span-6 sm:col-span-4">
          <InputLabel for="movement_type" value="Tipo de Movimiento" />
          <DynamicSelect
            v-model="transaction.movement_type_id"
            :options="filteredMovementTypes"
            placeholder="Seleccione un tipo de movimiento"
            class="mt-2 block w-full"
          />
          <InputError :message="errorForm.movement_type_id" class="mt-2" />
        </div>

        <div
          v-if="
            selectedMovementTypecode === 'RCC' ||
            selectedMovementTypecode === 'PF'
          "
          class="col-span-6 sm:col-span-4"
        >
          <InputLabel for="type" value="Lista de Cajas" />
          <DynamicSelect
            v-model="transaction.box_id"
            :options="filteredCashSessions"
            placeholder="Seleccione una caja"
            class="mt-2 block w-full"
          />
        </div>

        <div
          v-if="selectedBoxType === 'chica'"
          class="col-span-6 sm:col-span-4"
        >
          <InputLabel for="document" value="Numero de comprobante" />
          <TextInput
            v-model="transaction.document"
            type="number"
            class="mt-1 block w-full"
            min="0"
            step="0.01"
          />
          <InputError :message="errorForm.document" class="mt-2" />
        </div>

        <div class="col-span-6 sm:col-span-4">
          <InputLabel for="amount" value="Monto" />
          <TextInput
            v-model="transaction.amount"
            type="number"
            class="mt-1 block w-full"
            min="0"
            step="0.01"
          />
          <InputError :message="errorForm.amount" class="mt-2" />
        </div>

        <div class="col-span-6 sm:col-span-4">
          <InputLabel for="description" value="Descripción" />
          <TextInput
            v-model="transaction.description"
            type="text"
            class="mt-1 block w-full"
            minlength="3"
            maxlength="300"
          />
          <InputError :message="errorForm.description" class="mt-2" />
        </div>
        <!-- beneficiario-->
        <div v-if="props.countperson > 0" class="col-span-6 sm:col-span-4">
          <InputLabel for="person_id" value="Beneficiario/Remitente" />
          <DynamicSelect
            v-if="props.countperson < 5"
            class="mt-2 block w-full"
            v-model="transaction.beneficiary_id"
            :options="peopleOptions"
            autofocus
          />
          <SearchPerson
            v-else-if="people.length > 5"
            @selectPerson="handlePersonSelect"
          />
          <InputError :message="errorForm.beneficiary_id" class="mt-2" />
        </div>
        <div v-else class="col-span-6 sm:col-span-4">
          <p class="text-red-500 mt-2">
            ⚠️ No hay personas disponibles para seleccionar como beneficiario o
            remitente.
          </p>
        </div>
      </div>

      <div class="mt-4 text-right">
        <button
          @click="save"
          :disabled="transaction.processing"
          class="px-4 py-2 bg-primary hover:bg-primaryhover text-white rounded"
        >
          Guardar
        </button>
      </div>
    </div>
  </AdminLayout>
</template>