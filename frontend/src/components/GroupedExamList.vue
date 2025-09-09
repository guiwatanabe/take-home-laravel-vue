<template>
  <v-card class="mb-5">
    <v-toolbar dense flat>
      <v-toolbar-title ref="groupNameTitle">{{ groupName }}</v-toolbar-title>
      <v-spacer />
      <div class="d-flex align-center">
        <span class="mr-3">Impressão:</span>
        <v-select
          :items="allGroups"
          dense
          outlined
          hide-details
          placeholder="Selecionar"
          @change="changeGroupsGroup"
        />
      </div>

      <v-btn
        small
        text
        color="red"
        class="text-none ml-3"
        ref="removePackageButton"
        @click="$emit('remove-group', packageId)"
        >Remover pacote</v-btn
      >
    </v-toolbar>

    <v-row>
      <v-col cols="12">
        <v-data-table :items="exams" :headers="headers" hide-default-footer>
          <template v-slot:[`item.laterality`]="{ item }">
            <v-select
              v-model="item.laterality"
              :items="lateralityValues"
              placeholder="Lateralidade"
              item-text="description"
              item-value="value"
              dense
              outlined
              hide-details
            />
          </template>

          <template v-slot:[`item.comment`]="{ item }">
            <v-text-field
              v-model="item.comment"
              dense
              outlined
              hide-details
              placeholder="Digite um comentário"
            />
          </template>

          <template v-slot:[`item.group`]="{ item }">
            <v-select
              v-model="item.group"
              :items="allGroups"
              dense
              outlined
              hide-details
              @change="changeGroup(item)"
            />
          </template>

          <template v-slot:[`item.actions`]="{ item }">
            <v-icon @click="removeExam(item)" color="red">
              mdi-trash-can-outline
            </v-icon>
          </template>
        </v-data-table>
      </v-col>

      <v-col cols="12">
        <v-text-field
          v-model="observations"
          dense
          outlined
          hide-details
          placeholder="Observação"
          @input="updateObservations"
          class="px-3"
        />
      </v-col>
    </v-row>
  </v-card>
</template>

<script>
export default {
  props: {
    packageId: Number,
    groupName: String,
    exams: {
      type: Array,
      default: () => [],
    },
  },
  data: () => ({
    observations: null,
    allGroups: [
      "Individual",
      "Grupo 1",
      "Grupo 2",
      "Grupo 3",
      "Grupo 4",
      "Grupo 5",
    ],
    lateralityValues: [
      {
        value: "OD",
        description: "Olho direito",
      },
      {
        value: "OE",
        description: "Olho esquerdo",
      },
      {
        value: "AO",
        description: "Ambos os olhos",
      },
    ],
    headers: [
      { text: "Exame", value: "name", sortable: false },
      { text: "Lateralidade", value: "laterality", sortable: false },
      { text: "Comentário", value: "comment", sortable: false },
      { text: "Impressão", value: "group", sortable: false },
      { text: "", value: "actions", sortable: false },
    ],
    packageGroup: "Individual",
  }),
  methods: {
    changeGroup(exam) {
      this.$emit("change-group", {
        packageId: this.packageId,
        examId: exam.id,
        newGroup: exam.group,
      });
    },
    removeExam(exam) {
      this.$emit("remove-exam", { packageId: this.packageId, examId: exam.id });
    },
    changeGroupsGroup(group) {
      this.$emit("change-package-group", {
        packageId: this.packageId,
        newGroup: group,
      });
    },
    updateObservations() {
      this.$emit("update-observations", {
        packageId: this.packageId,
        observations: this.observations,
      });
    },
  },
};
</script>
