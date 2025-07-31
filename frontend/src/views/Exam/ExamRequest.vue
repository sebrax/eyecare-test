<template>
  <v-container fluid class="exams-page">
    <h1 class="page-title mb-3">Solicitação de Exames</h1>
    <v-card class="exams-card pa-6">
      <v-row align="center" justify="space-between" class="mb-4">
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
            @change="addSingleExam"
            class="search-input"
            aria-label="Buscar exame"
          />
        </v-col>

        <v-col cols="12" md="6" class="d-flex align-center justify-end">
          <package-select-modal
            @apply="addExamPackages"
            :laterality-options="lateralityOptions"
          />
          <v-btn class="ml-2" to="/exames" aria-label="Gerenciar exames">
            Gerenciar exames
          </v-btn>

          <v-btn
            icon
            class="ml-2"
            :loading="isPdfLoading"
            @click="printCustomPdf"
            aria-label="Imprimir PDF customizado"
          >
            <v-icon>mdi-printer</v-icon>
          </v-btn>

          <v-btn
            icon
            class="ml-2"
            @click="clearAllGroups"
            :disabled="!examGroups.length"
            aria-label="Limpar todos os grupos de exames"
          >
            <v-icon color="red">mdi-delete</v-icon>
          </v-btn>
        </v-col>
      </v-row>

      <section
        v-for="(group, index) in examGroups"
        :key="group.title + index"
        class="mb-6 exam-block"
        aria-label="Grupo de exames"
      >
        <div class="d-flex justify-space-between align-center mb-3">
          <strong class="text-blue">{{ group.title }}</strong>

          <div class="d-flex align-center">
            <v-select
              v-model="group.printOption"
              :items="printOptionListWithSelect"
              item-value="value"
              item-text="label"
              dense
              outlined
              hide-details
              class="mr-4"
              placeholder="Buscar impressão"
              aria-label="Opções de impressão do grupo"
            />

            <v-btn
              text
              small
              color="error"
              @click="removeGroup(index)"
              aria-label="Remover grupo de exames"
            >
              Remover pacote
            </v-btn>
          </div>
        </div>

        <v-simple-table
          dense
          class="exam-table"
          aria-describedby="Tabela de exames do grupo"
        >
          <thead>
            <tr>
              <th class="col-exam">Exame</th>
              <th class="col-laterality">Lateralidade</th>
              <th class="col-comment">Comentário</th>
              <th class="col-print">Impressão</th>
              <th class="col-action" aria-hidden="true"></th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="(exam, eIndex) in group.exams"
              :key="exam.id || eIndex"
              :aria-label="`Exame ${exam.name}`"
            >
              <td>{{ exam.name }}</td>
              <td>
                <v-select
                  v-model="exam.laterality"
                  :items="lateralityOptions"
                  item-value="value"
                  item-text="label"
                  dense
                  outlined
                  hide-details
                  class="input-compact"
                  placeholder="Lateralidade"
                  aria-label="Selecione lateralidade"
                />
              </td>
              <td>
                <v-text-field
                  v-model="exam.comment"
                  placeholder="Descrever"
                  dense
                  outlined
                  hide-details
                  class="input-comment"
                  aria-label="Comentário do exame"
                />
              </td>
              <td>
                <v-select
                  v-model="exam.printOption"
                  :items="printOptionList"
                  item-value="value"
                  item-text="label"
                  dense
                  outlined
                  hide-details
                  class="input-compact"
                  aria-label="Opções de impressão do exame"
                />
              </td>
              <td>
                <v-btn
                  icon
                  small
                  @click="removeExam(index, eIndex)"
                  aria-label="Remover exame"
                >
                  <v-icon color="red">mdi-delete</v-icon>
                </v-btn>
              </td>
            </tr>
          </tbody>
        </v-simple-table>

        <v-textarea
          v-model="group.observation"
          label="Observação"
          dense
          outlined
          hide-details
          rows="2"
          max-rows="3"
          class="compact-textarea mt-4"
          aria-label="Observação do grupo"
        />
      </section>
    </v-card>
  </v-container>
</template>

<script>
import api from "@/services/api";
import PackageSelectModal from "@/components/Modal/Package/PackageSelectModal.vue";

export default {
  name: "ExamRequest",
  components: { PackageSelectModal },

  data() {
    return {
      examsList: [],
      selectedExam: null,
      examGroups: [],
      lateralityOptions: [],
      printOptionList: [],
      isPdfLoading: false,
    };
  },

  computed: {
    hasSingleExams() {
      return this.examGroups.some((group) => group.title === "Exames Avulsos");
    },
  },

  created() {
    this.loadExamsAndConfigs();
  },

  methods: {
    async loadExamsAndConfigs() {
      try {
        const response = await api.get("/exams");
        this.examsList = response.data?.data || response.data || [];

        const config = response.data?.config || {};
        this.lateralityOptions = this.toOptionsArray(config.lateralityOptions);
        this.printOptionList = this.toOptionsArray(config.groupOptions);
        this.printOptionListWithSelect = [
          { value: null, label: "Selecione Impressão" },
          ...this.toOptionsArray(config.groupOptions),
        ];
      } catch (error) {
        console.error("Erro ao carregar exames e configurações:", error);
      }
    },

    toOptionsArray(configObj = {}) {
      return Object.entries(configObj).map(([value, label]) => ({
        value,
        label,
      }));
    },

    addSingleExam(exam) {
      if (!exam) return;

      const normalized = this.normalizeExam(exam);
      const idx = this.examGroups.findIndex(
        (g) => g.title === "Exames Avulsos"
      );

      if (idx === -1) {
        this.examGroups.push({
          title: "Exames Avulsos",
          exams: [normalized],
          printOption: normalized.printOption,
          observation: "",
        });
      } else {
        this.examGroups[idx].exams.push(normalized);
      }

      this.selectedExam = null;
    },

    normalizeExam(exam) {
      if (typeof exam === "string") {
        return {
          name: exam,
          laterality: null,
          comment: "",
          printOption: this.printOptionList[0]?.value || null,
        };
      }
      return {
        ...exam,
        laterality: exam.laterality ?? null,
        comment: exam.comment ?? "",
        printOption: this.printOptionList[0]?.value || null,
      };
    },

    addExamPackages(packages) {
      packages.forEach((pkg) => {
        const defaultPrint =
          this.printOptionList.find((opt) => opt.value === pkg.name)?.value ||
          this.printOptionList[0]?.value ||
          null;

        this.examGroups.push({
          title: pkg.name,
          exams: pkg.exams.map((exam) => ({
            ...exam,
            laterality: exam.laterality ?? null,
            comment: exam.comment ?? exam.comments ?? "",
            printOption: defaultPrint,
          })),
          printOption: defaultPrint,
          observation: pkg.observations || pkg.observation || "",
        });
      });
    },

    removeGroup(index) {
      this.examGroups.splice(index, 1);
    },

    removeExam(groupIndex, examIndex) {
      this.examGroups[groupIndex].exams.splice(examIndex, 1);
    },

    clearAllGroups() {
      this.examGroups = [];
    },

    async printSingleExamsPdf() {
      this.isPdfLoading = true;
      try {
        const response = await api.post(
          "/exams/pdf/download",
          { avulsos: 1 },
          { responseType: "blob" }
        );

        const fileURL = window.URL.createObjectURL(
          new Blob([response.data], { type: "application/pdf" })
        );
        window.open(fileURL);
        setTimeout(() => window.URL.revokeObjectURL(fileURL), 60000);
      } catch (error) {
        console.error("Erro ao gerar PDF avulsos:", error);
      } finally {
        this.isPdfLoading = false;
      }
    },

    async printCustomPdf() {
      this.isPdfLoading = true;
      try {
        const response = await api.post(
          "/exams/pdf/download",
          {
            groups: this.examGroups.map((group) => ({
              title: group.title,
              printOption: group.printOption,
              observation: group.observation,
              exams: group.exams.map((exam) => ({
                id: exam.id,
                name: exam.name,
                laterality: exam.laterality,
                comment: exam.comment,
                printOption: exam.printOption,
              })),
            })),
          },
          { responseType: "blob" }
        );

        const fileURL = window.URL.createObjectURL(
          new Blob([response.data], { type: "application/pdf" })
        );
        window.open(fileURL);
        setTimeout(() => window.URL.revokeObjectURL(fileURL), 60000);
      } catch (error) {
        console.error("Erro ao gerar PDF:", error);
      } finally {
        this.isPdfLoading = false;
      }
    },
  },
};
</script>

<style scoped>
.exams-page {
  max-width: 1200px;
  margin: 0 auto;
}

.search-input {
  max-width: 400px;
}

.exam-block {
  border: 1px solid #e0e0e0;
  border-radius: 6px;
  padding: 16px;
}

.text-blue {
  color: #0f1e52;
  font-weight: 600;
}

.exam-table {
  width: 100%;
  table-layout: fixed;
  border-collapse: separate;
  border-spacing: 0 8px;
}

.exam-table th,
.exam-table td {
  vertical-align: middle;
  padding: 8px 12px;
}

.col-exam {
  min-width: 150px;
  max-width: 20px;
}

.col-laterality,
.col-print {
  width: 160px;
}

.col-comment {
  min-width: 290px;
}

.col-action {
  width: 40px;
}

.input-compact,
.input-comment {
  width: 100% !important;
  min-width: 160px;
  border-radius: 8px;
  transition: box-shadow 0.3s, border-color 0.3s;
}

.input-compact.v-input--is-focused .v-input__control,
.input-comment.v-input--is-focused .v-input__control {
  border-color: #0f1e52 !important;
  box-shadow: 0 0 7px rgba(15, 30, 82, 0.6);
}

.input-comment {
  max-width: 900px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.compact-textarea .v-input__control {
  min-height: 60px;
  max-height: 90px;
  padding-top: 4px;
  padding-bottom: 4px;
}

.compact-textarea textarea {
  font-size: 14px;
  line-height: 1.3;
  padding: 4px 8px;
  resize: vertical;
}

::v-deep .v-input__control,
::v-deep .v-input__slot,
::v-deep .v-input__control input,
::v-deep .v-input__control textarea,
::v-deep .v-select__selections {
  font-size: 12px !important;
  font-family: inherit !important;
}
</style>
