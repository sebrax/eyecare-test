<template>
  <v-container fluid class="exams-page">
    <h1 class="page-title mb-3">Gerenciar Exames</h1>

    <!-- Cabeçalho com busca e botão -->
    <v-row class="mb-4" justify="space-between">
      <v-col cols="12" md="6">
        <v-autocomplete
          v-model="selectedExam"
          :items="examsList"
          item-text="name"
          label="Buscar exame"
          return-object
          clearable
          dense
          solo
          rounded
          prepend-inner-icon="mdi-magnify"
          @change="handleSelectExam"
        />
      </v-col>
      <v-col cols="12" md="6" class="d-flex justify-end">
        <v-btn class="btn-primary" dark small @click="openCreateDialog">
          <v-icon left small>mdi-plus</v-icon>
          Novo Exame
        </v-btn>
      </v-col>
    </v-row>

    <!-- Tabela -->
    <exam-table
      :headers="headers"
      :items="filteredExams"
      :loading="loading"
      :page.sync="page"
      :getLateralityLabel="getLateralityLabel"
      @edit="openEditDialog"
      @delete="confirmDeleteExam"
    />

    <!-- Dialog de formulário -->
    <v-dialog v-model="dialogCreate" max-width="500px" persistent>
      <exam-form
        :exam="currentExam"
        :laterality-options="lateralityOptions"
        :group-options="groupOptions"
        @saved="handleSaved"
        @cancel="closeDialog"
      />
    </v-dialog>

    <!-- Confirmação de exclusão -->
    <confirm-dialog
      v-model="confirmDeleteDialog"
      :exam="examToDelete"
      @confirm="deleteExamById"
      @cancel="cancelDelete"
    />
  </v-container>
</template>

<script>
import api from "@/services/api";
import ExamForm from "@/components/Exam/ExamForm.vue";
import ExamTable from "@/components/Exam/ExamListTable.vue";
import ConfirmDialog from "@/components/Shared/ConfirmDialog.vue";

export default {
  name: "ExamList",
  components: {
    ExamForm,
    ExamTable,
    ConfirmDialog,
  },
  data() {
    return {
      examsList: [],
      selectedExam: null,
      exams: [],
      loading: false,
      page: 1,
      dialogCreate: false,
      currentExam: null,
      confirmDeleteDialog: false,
      examToDelete: null,
      lateralityOptions: [],
      groupOptions: [],
      headers: [
        { text: "Nome", value: "name" },
        { text: "Lateralidade", value: "laterality" },
        { text: "Comentário", value: "comment" },
        { text: "Grupo", value: "group" },
        { text: "Ações", value: "actions", sortable: false, align: "center" },
      ],
    };
  },
  computed: {
    filteredExams() {
      if (!this.selectedExam) return this.exams;
      return this.exams.filter((e) => e.id === this.selectedExam.id);
    },
  },
  created() {
    this.fetchExamsAndConfigs();
  },
  methods: {
    async fetchExamsAndConfigs() {
      this.loading = true;
      try {
        const response = await api.get("/exams");
        const data = response.data?.data || [];
        this.examsList = this.exams = data;

        const config = response.data?.config || {};
        this.lateralityOptions = this.mapConfigToOptions(
          config.lateralityOptions
        );
        this.groupOptions = this.mapConfigToOptions(config.groupOptions);
      } finally {
        this.loading = false;
      }
    },
    mapConfigToOptions(obj = {}) {
      return Object.entries(obj).map(([value, label]) => ({ value, label }));
    },
    getLateralityLabel(value) {
      return (
        this.lateralityOptions.find((opt) => opt.value === value)?.label || "—"
      );
    },
    openCreateDialog() {
      this.currentExam = null;
      this.dialogCreate = true;
    },
    openEditDialog(exam) {
      this.currentExam = { ...exam };
      this.dialogCreate = true;
    },
    closeDialog() {
      this.dialogCreate = false;
    },
    confirmDeleteExam(exam) {
      this.examToDelete = exam;
      this.confirmDeleteDialog = true;
    },
    cancelDelete() {
      this.confirmDeleteDialog = false;
      this.examToDelete = null;
    },
    async deleteExamById() {
      this.loading = true;
      try {
        await api.delete(`/exams/${this.examToDelete.id}`);
        await this.fetchExamsAndConfigs();
      } catch (e) {
        console.error("Erro ao excluir:", e);
      } finally {
        this.loading = false;
        this.confirmDeleteDialog = false;
      }
    },
    async handleSaved() {
      this.closeDialog();
      await this.fetchExamsAndConfigs();
    },
    handleSelectExam(exam) {
      this.selectedExam = exam;
    },
  },
};
</script>

<style scoped>
.exams-page {
  max-width: 1200px;
  margin: 0 auto;
}
.btn-primary {
  background-color: #0f1e52 !important;
  color: white !important;
}
</style>
