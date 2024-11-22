<script setup>
// Imports
import DialogModal from "@/Components/DialogModal.vue";
import InputError from "@/Components/InputError.vue";
import TextInput from "@/Components/TextInput.vue";
import DynamicSelect from "@/Components/DynamicSelect.vue";
import InputLabel from "@/Components/InputLabel.vue";

// Props
const props = defineProps({
  company: { type: Object, default: () => ({}) },
  error: { type: Object, default: () => ({}) },
  show: { type: Boolean, default: false },
  economyActivities: { type: Array, default: () => [] },
  contributorTypes: { type: Array, default: () => [] },
});

// Emits
defineEmits(["close", "save"]);

const economyActivityOptions = props.economyActivities.map(
  (economyActivity) => ({
    value: economyActivity.id,
    label: economyActivity.name,
  })
);

const contributorTypeOptions = props.contributorTypes.map(
  (contributorType) => ({
    value: contributorType.id,
    label: contributorType.name,
  })
);
</script>

<template>
  <DialogModal :show="show" maxWidth="lg" @close="$emit('close')">
    <template #title>
      {{ `${company.id === undefined ? "Añadir" : "Editar"} RUC` }}
    </template>
    <template #content>
      <div class="mt-4">
        <form class="w-2xl grid grid-cols-1 gap-3">
          <div class="col-span-6 sm:col-span-4">
            <InputLabel for="ruc" value="RUC" />

            <TextInput
              v-model="company.ruc"
              type="text"
              class="mt-1 block w-full"
              placeholder="RUC"
              minlength="13"
              maxlength="13"
            />
            <InputError :message="error.ruc" class="mt-2" />
          </div>

          <div class="col-span-6 sm:col-span-4">
            <InputLabel for="company" value="Compania" />
            <TextInput
              v-model="company.company"
              type="text"
              class="mt-1 block w-full"
              placeholder="Razon social"
              minlength="3"
              maxlength="300"
            />
            <InputError :message="error.company" class="mt-2" />
          </div>

          <div class="col-span-6 sm:col-span-4">
            <InputLabel
              for="economic_activity_id"
              value="Actividad economica"
            />
            <DynamicSelect
              class="mt-1 block w-full"
              v-model="company.economic_activity_id"
              :options="economyActivityOptions"
              autofocus
            />
            <InputError :message="error.economic_activity_id" class="mt-2" />
          </div>

          <div class="col-span-6 sm:col-span-4">
            <InputLabel
              for="contributor_type_id"
              value="Tipo de contribuyente"
            />
            <DynamicSelect
              class="mt-1 block w-full"
              v-model="company.contributor_type_id"
              :options="contributorTypeOptions"
              autofocus
            />
            <InputError :message="error.contributor_type_id" class="mt-2" />
          </div>
        </form>
      </div>
    </template>
    <template #footer>
      <button
        @click="$emit('save')"
        class="px-6 py-2 ml-2 bg-blue-600 dark:bg-blue-600 text-blue-100 dark:text-blue-200 rounded"
      >
        Guardar
      </button>
    </template>
  </DialogModal>
</template>