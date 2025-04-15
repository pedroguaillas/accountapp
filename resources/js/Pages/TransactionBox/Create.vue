<script setup lang="ts">
import { reactive, computed, ref } from "vue";
import AdminLayout from "@/Layouts/AdminLayout.vue";
import SearchPerson from "./SearchPerson.vue";
import { TextInput, InputError, InputLabel, DynamicSelect } from "@/Components";
import { useForm, router } from "@inertiajs/vue3";
import { Cash, Errors, MovementType, Person, TransactionBox } from "@/types";

const props = defineProps<{
  cashSessions: Cash[];
  movementtypes: MovementType[];
  people: Person[];
  countperson: number;
}>();

const initialTransaction = {
  cash_session_id: "",
  beneficiary_id: "",
  movement_type_id: "",
  amount: "",
  description: "",
  state_transaction: "pendiente",
  box_id: "",
  document: "",
  type: "" // Agrega la propiedad requerida 'type'
};

const cash_session_id =
  props.cashSessions.length === 1 ? String(props.cashSessions[0].id) : "";

const beneficiary_id =
  props.people.length === 1 ? String(props.people[0].id) : "";

const transaction = useForm<TransactionBox>({
  ...initialTransaction,
  cash_session_id,
  beneficiary_id,
});

const errorForm = reactive<Record<string, string>>({});

const TypeOptions = [
  { value: "Ingreso", label: "Ingreso" },
  { value: "Egreso", label: "Egreso" },
];

const movementTypeOptions = props.movementtypes.map((type) => ({
  value: type.id ?? 0,
  label: type.name,
  type: type.type,
  code: type.code,
}));

const selectedBoxType = computed(() => {
  const selectedSession = props.cashSessions.find(
    (session) => String(session.id) === transaction.cash_session_id
  );
  return selectedSession ? selectedSession.box_type : null;
});

const selectedMovementTypecode = computed(() => {
  const selectedSession = props.movementtypes.find(
    (session) => String(session.id) === transaction.movement_type_id
  );
  return selectedSession ? selectedSession.code : null;
});

const selectedType = ref("");

const filteredMovementTypes = computed(() => {
  if (!movementTypeOptions) return []; // siempre devuelve array

  if (selectedBoxType.value === "general") {
    return movementTypeOptions.filter(
      (type) =>
        type.type === selectedType.value && !["DCG", "RFA"].includes(type.code)
    );
  } else if (selectedBoxType.value === "chica") {
    return movementTypeOptions.filter(
      (type) =>
        type.type === selectedType.value && ["RFA"].includes(type.code)
    );
  } else if (selectedBoxType.value === "otras") {
    return movementTypeOptions.filter(
      (type) =>
        type.type === selectedType.value && ["DCG"].includes(type.code)
    );
  }

  return [];
});

const peopleOptions = props.people.map((person) => ({
  value: person.id ?? 0,
  label: person.name,
}));

const cashSessionOptions = props.cashSessions.map((session) => ({
  value: session.id ?? 0,
  label: `${session.id} - ${session.name}`, // 🔥 Aquí la interpolación es correcta
  type: session.box_type,
}));

const filteredCashSessions = computed(() => {
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
    onError: (errors: Errors) => {
      console.error("Errores:", errors);
      Object.keys(errors).forEach((key) => {
        errorForm[key] = (errors[key] as string[])[0];
      });
    },
  });
};

const handlePersonSelect = (person: Person) => {
  transaction.beneficiary_id = person.id?.toString();  // <-- conversión segura
};

</script>

<template>
  <AdminLayout title="Registro de Transacciones">
    <div class="p-4 bg-white rounded drop-shadow-md">
      <h2 class="text-lg font-bold">Registrar Transacción</h2>
      <div class="mt-4">
        <!--fila 1-->
        <div class="sm:flex gap-4 mt-4">
          <div class="w-full">
            <InputLabel for="cash_session_id" value="Sesión de Caja" />
            <DynamicSelect v-model="transaction.cash_session_id" :options="cashSessionOptions"
              placeholder="Seleccione una sesión" class="mt-1 w-full" />
            <InputError :message="errorForm.cash_session_id" class="mt-2" />
          </div>

          <div class="w-full mt-4 sm:mt-0">
            <InputLabel for="type" value="Tipo" />
            <DynamicSelect v-model="selectedType" :options="TypeOptions" placeholder="Seleccione un tipo"
              class="mt-1 w-full" />
          </div>
        </div>

        <!--fila 2-->
        <div class="sm:flex gap-4 mt-4">
          <div class="w-full">
            <InputLabel for="movement_type" value="Tipo de Movimiento" />
            <DynamicSelect v-model="transaction.movement_type_id" :options="filteredMovementTypes"
              placeholder="Seleccione un tipo de movimiento" class="mt-1 w-full" />
            <InputError :message="errorForm.movement_type_id" class="mt-2" />
          </div>

          <div class="w-full mt-4 sm:mt-0">
            <InputLabel for="type" value="Lista de Cajas" />
            <DynamicSelect v-model="transaction.box_id" :options="filteredCashSessions"
              placeholder="Seleccione una caja" class="mt-1 w-full" />
          </div>
        </div>

        <!--fila 3-->
        <div class="sm:flex gap-4 mt-4">
          <div class="w-full">
            <InputLabel for="document" value="Numero de comprobante" />
            <TextInput v-model="transaction.document" type="number" class="mt-1 w-full" min="0" step="0.01" />
            <InputError :message="errorForm.document" class="mt-2" />
          </div>

          <div class="w-full mt-4 sm:mt-0">
            <InputLabel for="amount" value="Monto" />
            <TextInput v-model="transaction.amount" type="number" class="mt-1 w-full" min="0" step="0.01" />
            <InputError :message="errorForm.amount" class="mt-2" />
          </div>
        </div>

        <!--fila 4-->
        <div class="sm:flex gap-4 mt-4">
          <div class="w-full">
            <InputLabel for="description" value="Descripción" />
            <TextInput v-model="transaction.description" type="text" class="mt-1 w-full" minlength="3"
              maxlength="300" />
            <InputError :message="errorForm.description" class="mt-2" />
          </div>
          <!-- beneficiario-->
          <div v-if="props.countperson > 0" class="w-full mt-4 sm:mt-0">
            <InputLabel for="person_id" value="Beneficiario/Remitente" />
            <DynamicSelect v-if="props.countperson < 5" class="mt-1 w-full" v-model="transaction.beneficiary_id"
              :options="peopleOptions" autofocus />
              <SearchPerson
              v-else-if="people.length > 5"
              @selectPerson="handlePersonSelect"
            />
              <InputError :message="errorForm.beneficiary_id" class="mt-2" />
          </div>
          <div v-else class="col-span-6 sm:col-span-4">
            <p class="text-red-500 mt-2">
              ⚠️ No hay personas disponibles para seleccionar como beneficiario
              o remitente.
            </p>
          </div>
        </div>
      </div>

      <div class="mt-4 text-right">
        <button @click="save" :disabled="transaction.processing"
          class="px-4 py-2 bg-primary hover:bg-primaryhover text-white rounded">
          Guardar
        </button>
      </div>
    </div>
  </AdminLayout>
</template>