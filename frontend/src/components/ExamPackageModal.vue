<template>
  <v-row justify="center" class="">
    <v-dialog
      v-model="dialog"
      @input="$emit('input', $event)"
      max-width="640px"
    >
      <v-card class="pb-5">
        <v-toolbar flat outlined>
          <v-toolbar-title>Pacote de exames</v-toolbar-title>
          <v-spacer></v-spacer>
          <v-btn icon tile small class="mr-1" @click="$emit('input', false)">
            <v-icon color="primary">mdi-window-close</v-icon>
          </v-btn>
        </v-toolbar>

        <v-card-text>
          <v-container>
            <v-row dense>
              <v-col cols="12" md="8">
                <v-text-field
                  placeholder="Pesquisar pacotes"
                  outlined
                  clearable
                  dense
                  hide-details
                  prepend-inner-icon="mdi-magnify"
                  v-model="searchExamPackages"
                ></v-text-field>
              </v-col>
              <v-col cols="12" md="4">
                <v-btn
                  small
                  block
                  color="primary"
                  outlined
                  height="40"
                  class="text-none"
                  @click="openEditModal"
                >
                  Novo Pacote de Exames
                </v-btn>
              </v-col>
            </v-row>

            <v-row>
              <v-col cols="12">
                <v-data-table
                  :headers="examPackagesHeaders"
                  :items="examPackages"
                  :page.sync="page"
                  :items-per-page="itemsPerPage"
                  :search="searchExamPackages"
                  :loading="isLoading"
                  @page-count="pageCount = $event"
                  class="elevation-0"
                  show-select
                  single-select
                  v-model="selectedExamPackages"
                  hide-default-footer
                >
                  <template v-slot:loading>
                    <v-skeleton-loader type="table-row"></v-skeleton-loader>
                  </template>
                  <template v-slot:[`item.observations`]="{ item }">
                    <span class="text-caption">{{ item.observations }}</span>
                  </template>
                  <template v-slot:[`item.actions`]="{ item }">
                    <v-icon class="mr-2" @click="editItem(item)">
                      mdi-pencil
                    </v-icon>
                    <v-icon @click="deleteItem(item)" color="red">
                      mdi-trash-can-outline
                    </v-icon>
                  </template>
                </v-data-table>
              </v-col>
              <v-col cols="12" class="d-flex justify-end">
                <v-pagination v-model="page" :length="pageCount"></v-pagination>
              </v-col>
            </v-row>
          </v-container>
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn
            color="primary"
            class="text-none"
            ref="addPackageButton"
            @click="addPackage(selectedExamPackages)"
          >
            Usar Pacote na Consulta
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
    <EditExamPackageModal
      ref="editModal"
      v-model="showEditModal"
      :exams="exams"
      @package-created="handlePackageUpdate"
      @package-updated="handlePackageUpdate"
    />
  </v-row>
</template>

<script>
import api from "@/util/api";
import toaster from "@/util/toaster";
import EditExamPackageModal from "./EditExamPackageModal.vue";

export default {
  name: "ExamPackageModal",
  components: { EditExamPackageModal },
  data: () => ({
    isLoading: false,
    selectedExamPackages: [],
    examPackages: [],
    searchExamPackages: "",
    page: 1,
    pageCount: 0,
    itemsPerPage: 5,
    showEditModal: false,
    examPackagesHeaders: [
      {
        text: "Pacote de Exame",
        value: "name",
        sortable: true,
        align: "start",
      },
      {
        text: "Observação",
        value: "observations",
        sortable: false,
        align: "start",
      },
      {
        text: "",
        value: "actions",
        sortable: false,
        align: "end",
        width: "20%",
      },
    ],
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
  },
  methods: {
    async deleteItem(item) {
      try {
        const url = `packages/${item.id}`;
        const response = await api.delete(url);

        if (response.status == 200) {
          toaster.open({
            color: "success",
            text: `Pacote ${response.data.name} excluído.`,
          });

          this.$emit("package-deleted", item);
        } else {
          toaster.open({
            color: "error",
            text: "Erro ao excluir pacote.",
          });
        }
      } catch (error) {
        toaster.open({
          color: "error",
          text: "Erro ao excluir pacote.",
        });
      }
    },
    addPackage(examPackage) {
      if (!examPackage[0]) {
        toaster.open({
          color: "warning",
          text: "Selecione um pacote para adicionar.",
        });
        return;
      }
      this.$emit("add-package", examPackage[0]);
      this.selectedExamPackages = [];
    },
    openEditModal() {
      this.showEditModal = true;
    },
    editItem(item) {
      this.showEditModal = true;
      this.$nextTick(() => {
        this.$refs.editModal.localExamPackage = JSON.parse(
          JSON.stringify(item)
        );
      });
    },
    async fetchPackages() {
      this.isLoading = true;
      try {
        const response = await api.get("packages");
        this.examPackages = response.data.data;
      } catch (error) {
        toaster.open({
          color: "error",
          text: "Erro ao carregar pacotes. Tente recarregar a página.",
        });
      } finally {
        this.isLoading = false;
      }
    },
    handlePackageUpdate() {
      this.selectedExamPackages = [];
      this.fetchPackages();
    },
  },
  mounted() {
    this.fetchPackages();
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
};
</script>

<style scoped lang="scss"></style>
