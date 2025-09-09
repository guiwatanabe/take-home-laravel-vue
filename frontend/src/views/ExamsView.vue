<template>
  <v-row>
    <v-col cols="12" md="4">
      <v-card elevation="2" outlined>
        <v-toolbar dense flat>
          <v-toolbar-title>Paciente</v-toolbar-title>
        </v-toolbar>
        <v-card-text class="pt-2 pb-5">
          <div class="text-h6 black--text">Guilherme Watanabe</div>
          <div class="">29a 9m | Masc</div>
        </v-card-text>
      </v-card>
    </v-col>

    <v-col cols="12" md="8">
      <v-card elevation="2" outlined>
        <v-toolbar dense flat>
          <v-toolbar-title>Solicitação de Exames</v-toolbar-title>
          <v-spacer></v-spacer>
          <v-btn
            icon
            tile
            small
            title="Imprimir Solicitações de Exames"
            :disabled="!canSubmit"
            @click="printExams"
          >
            <v-icon color="primary">mdi-cloud-print-outline</v-icon>
          </v-btn>
          <v-btn
            icon
            tile
            small
            title="Limpar"
            :disabled="!canSubmit"
            @click="removeAllExams"
            class="ms-2"
          >
            <v-icon color="red">mdi-trash-can-outline</v-icon>
          </v-btn>
        </v-toolbar>
        <v-card-text class="pb-md-0">
          <v-row dense>
            <v-col cols="12" md="8">
              <v-autocomplete
                v-model="selectedExam"
                :items="exams"
                :search-input.sync="search"
                :loading="isLoading"
                :disabled="isLoading"
                item-text="name"
                item-value="id"
                placeholder="Selecionar exame"
                outlined
                dense
                return-object
                @change="addExam"
              >
                <template v-slot:item="data">
                  {{ data.item.name }}
                </template>
              </v-autocomplete>
            </v-col>

            <v-col cols="12" md="4">
              <v-btn
                color="primary"
                depressed
                block
                height="40"
                class="text-none font-weight-regular"
                @click="examPackageModal = true"
              >
                Pacote de exames
              </v-btn>
            </v-col>

            <exam-package-modal
              v-model="examPackageModal"
              :exams="exams"
              @add-package="addPackage"
            />
          </v-row>
        </v-card-text>
      </v-card>
    </v-col>

    <v-container fluid>
      <grouped-exam-list
        v-for="pkg in visiblePackages"
        :key="pkg.id"
        :package-id="pkg.id"
        :group-name="pkg.name"
        :exams="examsByPackage(pkg.id)"
        @change-group="changeExamGroup"
        @update-observations="handlePackageObservations"
        @remove-exam="removeExam"
        @remove-group="removePackage"
        @change-package-group="changePackageGroup"
      />
    </v-container>
  </v-row>
</template>

<script>
import api from "@/util/api";
import toaster from "@/util/toaster";
import ExamPackageModal from "@/components/ExamPackageModal.vue";
import GroupedExamList from "@/components/GroupedExamList.vue";

export default {
  name: "ExamsView",
  components: { ExamPackageModal, GroupedExamList },
  data: () => ({
    exams: [],
    examPackageModal: false,
    selectedExam: null,
    isLoading: false,
    search: null,
    selectedPackages: [
      {
        id: 0,
        name: "Exames Avulsos",
        exams: [],
        observations: "",
      },
    ],
  }),
  methods: {
    async fetchExams() {
      this.isLoading = true;
      try {
        const response = await api.get("exams");
        this.exams = response.data.data;
      } catch (error) {
        toaster.open({
          color: "error",
          text: "Erro ao carregar exames.",
        });
      } finally {
        this.isLoading = false;
      }
    },
    getPackageById(packageId) {
      return this.selectedPackages.find((p) => p.id === packageId) || null;
    },
    addPackage(examPackage) {
      if (!examPackage) return;

      if (examPackage.exams.length === 0) {
        toaster.open({
          color: "warning",
          text: "Este pacote não possui nenhum exame cadastrado.",
        });
        return;
      }

      const existingExamIds = this.selectedPackages.flatMap((pkg) =>
        pkg.exams.map((e) => e.id)
      );

      const filteredExams = (examPackage.exams || []).filter(
        (e) => !existingExamIds.includes(e.id)
      );

      if (filteredExams.length === 0) {
        toaster.open({
          color: "warning",
          text: "Todos os exames deste pacote já foram adicionados anteriormente.",
        });
        return;
      }

      const skippedExams = (examPackage.exams || []).filter((e) =>
        existingExamIds.includes(e.id)
      );

      if (skippedExams.length) {
        const skippedExamsList = skippedExams.map((e) => e.name).join(", ");
        toaster.open({
          color: "warning",
          text: `Os seguintes exames deste pacote já foram adicionados anteriormente: ${skippedExamsList}`,
        });
      }

      const existingPackage = this.selectedPackages.find(
        (p) => p.id === examPackage.id
      );

      if (!existingPackage) {
        this.selectedPackages.push({
          ...examPackage,
          exams: filteredExams,
        });
      } else {
        filteredExams.forEach((exam) => {
          const exists = existingPackage.exams.some((e) => e.id === exam.id);
          if (!exists) {
            existingPackage.exams.push(exam);
          }
        });
      }

      this.examPackageModal = false;
    },
    addExam(selectedItem) {
      if (!selectedItem) return;

      const targetPackage = this.getPackageById(0);
      const examAlreadySelected = this.selectedPackages.some((pkg) =>
        pkg.exams.some((e) => e.id === selectedItem.id)
      );

      if (examAlreadySelected) {
        toaster.open({
          color: "warning",
          text: "Este exame já foi adicionado anteriormente.",
        });
      } else {
        targetPackage.exams.push(selectedItem);
      }

      this.$nextTick(() => {
        this.selectedExam = null;
        this.search = null;
      });
    },
    removeExam({ packageId, examId }) {
      const targetPackage = this.getPackageById(packageId);
      if (targetPackage) {
        targetPackage.exams = targetPackage.exams.filter(
          (e) => e.id !== examId
        );

        if (packageId != 0 && !targetPackage.exams.length) {
          this.removePackage(targetPackage.id);
        }
      }
    },
    examsByPackage(packageId) {
      const pkg = this.selectedPackages.find((p) => p.id === packageId);
      return pkg ? pkg.exams : [];
    },
    changeExamGroup({ packageId, examId, newGroup }) {
      const targetPackage = this.getPackageById(packageId);
      if (targetPackage) {
        const exam = targetPackage.exams.find((e) => e.id === examId);
        if (exam) {
          exam.group = newGroup;
        }
      }
    },
    removePackage(packageId) {
      if (packageId === 0) {
        const targetPackage = this.getPackageById(0);
        if (targetPackage) {
          targetPackage.exams = [];
        }
        return;
      }

      this.selectedPackages = this.selectedPackages.filter(
        (p) => p.id !== packageId
      );
    },
    handlePackageObservations({ packageId, observations }) {
      const targetPackage = this.getPackageById(packageId);
      if (targetPackage) {
        targetPackage.observations = observations;
      }
    },
    changePackageGroup({ packageId, newGroup }) {
      const targetPackage = this.getPackageById(packageId);
      if (targetPackage) {
        targetPackage.exams.forEach((exam) => {
          exam.group = newGroup;
        });
      }
    },
    removeAllExams() {
      this.selectedPackages = this.selectedPackages.filter((pkg) => {
        if (pkg.id === 0) {
          pkg.exams = [];
          return true;
        }
        return false;
      });

      toaster.open({
        color: "info",
        text: "Todos os exames foram removidos.",
      });
    },
    async printExams() {
      this.isLoading = true;
      try {
        const response = await api.post("print", this.selectedPackages, {
          responseType: "blob",
        });
        const file = new Blob([response.data], { type: "application/pdf" });
        const fileURL = URL.createObjectURL(file);
        window.open(fileURL);
      } catch (error) {
        toaster.open({
          color: "error",
          text: "Erro ao imprimir exames.",
        });
      } finally {
        this.isLoading = false;
      }
      toaster.open({
        color: "success",
        text: "Documento criado.",
      });
    },
  },
  created() {
    this.fetchExams();
  },
  computed: {
    visiblePackages() {
      return this.selectedPackages.filter((pkg) => {
        return pkg.exams && pkg.exams.length > 0;
      });
    },
    getTotalExams() {
      return this.selectedPackages.reduce(
        (sum, pkg) => sum + (pkg.exams ? pkg.exams.length : 0),
        0
      );
    },
    canSubmit() {
      return this.getTotalExams > 0;
    },
  },
};
</script>
