<template>
  <v-dialog v-model="dialog" max-width="640px">
    <v-card>
      <v-toolbar flat outlined>
        <v-toolbar-title>{{
          localExamPackage.id
            ? "Editar Pacote de Exames"
            : "Novo Pacote de Exames"
        }}</v-toolbar-title>
        <v-spacer></v-spacer>
        <v-btn icon @click="closeModal">
          <v-icon>mdi-close</v-icon>
        </v-btn>
      </v-toolbar>
      <v-card-text>
        <v-row dense class="mt-2">
          <v-col cols="12">
            <span>Nome do Pacote de Exames</span>
            <v-text-field
              v-model="localExamPackage.name"
              single-line
              outlined
              dense
              :error="!!validationErrors.name"
              :error-messages="validationErrors.name"
            ></v-text-field>
          </v-col>

          <v-col cols="12">
            <span>Observações</span>
            <v-text-field
              v-model="localExamPackage.observations"
              single-line
              outlined
              dense
              :error="!!validationErrors.observations"
              :error-messages="validationErrors.observations"
            ></v-text-field>
          </v-col>

          <v-col cols="12">
            <span>Adicionar Exames</span>
            <v-autocomplete
              v-model="selectedExam"
              :items="exams"
              :search-input.sync="search"
              :loading="isLoading"
              :disabled="isLoading"
              item-text="name"
              item-value="id"
              placeholder="Selecionar exames"
              outlined
              dense
              return-object
              @change="addExamToPackage"
              :error="!!validationErrors.exams"
              :error-messages="validationErrors.exams"
            >
              <template v-slot:item="data">
                {{ data.item.name }}
              </template>
            </v-autocomplete>
          </v-col>

          <v-col cols="12" v-if="localExamPackage.exams.length">
            <div style="max-height: 200px; overflow-y: auto">
              <v-list dense>
                <v-subheader>Exames</v-subheader>

                <v-list-item
                  v-for="(exam, index) in localExamPackage.exams"
                  :key="exam.id"
                >
                  <v-list-item-content>
                    <v-list-item-title>{{ exam.name }}</v-list-item-title>
                  </v-list-item-content>
                  <v-list-item-action>
                    <v-btn icon @click="removeExam(index)">
                      <v-icon color="red">mdi-delete</v-icon>
                    </v-btn>
                  </v-list-item-action>
                </v-list-item>
              </v-list>
            </div>
          </v-col>
        </v-row>
      </v-card-text>
      <v-card-actions>
        <v-spacer></v-spacer>
        <v-btn color="primary" class="text-none" @click="saveExamPackage">
          Salvar
        </v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script>
import api from "@/util/api";
import toaster from "@/util/toaster";

export default {
  name: "EditExamPackageModal",
  data: () => ({
    isLoading: false,
    selectedExam: null,
    search: null,
    validationErrors: {},
    localExamPackage: {
      id: null,
      name: "",
      exams: [],
      observations: "",
    },
  }),
  props: {
    value: {
      type: Boolean,
      default: false,
    },
    exams: {
      type: Array,
      default: () => [],
    },
    examPackage: {
      type: Object,
      default: () => ({
        id: null,
        name: "",
        exams: [],
        observations: "",
      }),
    },
  },
  computed: {
    dialog: {
      get() {
        return this.value;
      },
      set(val) {
        this.$emit("input", val);
      },
    },
  },
  methods: {
    closeModal() {
      this.$emit("input", false);
      this.localExamPackage = {
        id: null,
        name: "",
        exams: [],
        observations: "",
      };
    },
    async saveExamPackage() {
      try {
        const url = this.localExamPackage.id
          ? `packages/${this.localExamPackage.id}`
          : "packages";

        const method = this.localExamPackage.id ? "put" : "post";
        const payload = {
          ...this.localExamPackage,
          exams: this.localExamPackage.exams.map((exam) => exam.id),
        };
        const response = await api[method](url, payload);

        toaster.open({
          color: "success",
          text: `Pacote ${response.data.name} ${
            method == "put" ? "salvo" : "criado"
          } com sucesso.`,
        });

        this.$emit(method == "put" ? "package-updated" : "package-created");
        this.closeModal();
      } catch (error) {
        if (error.response && error.response.status === 422) {
          this.validationErrors = error.response.data.errors;
        } else {
          toaster.open({
            color: "error",
            text: "Erro ao salvar o pacote de exames.",
          });
        }
      }
    },
    addExamToPackage(selectedItem) {
      if (!selectedItem) return;

      const examAlreadySelected = this.localExamPackage.exams.some(
        (exam) => exam.id === selectedItem.id
      );

      if (!examAlreadySelected) {
        this.localExamPackage.exams.push(selectedItem);
      }

      this.$nextTick(() => {
        this.selectedExam = null;
        this.search = null;
      });
    },
    removeExam(index) {
      this.localExamPackage.exams.splice(index, 1);
    },
  },
  watch: {
    value(val) {
      if (val) {
        this.validationErrors = {};
        this.localExamPackage = JSON.parse(JSON.stringify(this.examPackage));
        this.fetchExams();
      } else {
        this.localExamPackage = {
          id: null,
          name: "",
          exams: [],
          observations: "",
        };
        this.selectedExam = null;
        this.search = null;
        this.validationErrors = {};
      }
    },
    examPackage: {
      handler(newVal) {
        if (this.value) {
          this.localExamPackage = { ...newVal };
        }
      },
      deep: true,
    },
  },
};
</script>
