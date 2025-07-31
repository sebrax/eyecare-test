<template>
  <div>
    <v-btn color="btn-primary" @click="openDialog">Selecionar Pacote</v-btn>

    <v-dialog v-model="isDialogVisible" max-width="800px" persistent>
      <v-card>
        <v-toolbar flat color="white">
          <v-toolbar-title>
            {{
              isFormVisible
                ? editingPackage
                  ? "Editar Pacote"
                  : "Novo Pacote"
                : "Pacotes de Exames"
            }}
          </v-toolbar-title>
          <v-spacer />
          <v-btn icon @click="closeDialog">
            <v-icon>mdi-close</v-icon>
          </v-btn>
        </v-toolbar>

        <v-card-text>
          <template v-if="!isFormVisible">
            <package-list-table
              :package-list="packageList"
              :is-loading="isLoading"
              :total-packages="totalPackages"
              :table-options.sync="tableOptions"
              :selected-packages.sync="selectedPackages"
              :table-headers="headers"
              :total-pages="totalPages"
              @edit-package="onEditPackage"
              @confirm-delete="confirmDeletePackage"
              @create-new-package="onCreateNewPackage"
              @apply-selected="onApplySelected"
            />
          </template>

          <template v-else>
            <package-form
              :packageData="editingPackage"
              :exams="exams"
              :laterality-options="lateralityOptions"
              @saved="handleFormSaved"
              @cancel="handleFormCancel"
            />
          </template>
        </v-card-text>

        <v-divider />
      </v-card>
    </v-dialog>
    <confirm-dialog
      v-model="confirmDialog"
      title="Confirmar Exclusão"
      :message="confirmMessage"
      icon="mdi-alert-circle"
      icon-color="error"
      @confirm="deleteConfirmed"
    />
  </div>
</template>

<script>
import api from "@/services/api";
import PackageForm from "@/components/Package/PackageForm.vue";
import PackageListTable from "@/components/Package/PackageListTable.vue";
import ConfirmDialog from "@/components/Shared/ConfirmDialog.vue";

export default {
  name: "PackageSelectModal",
  components: { PackageForm, PackageListTable, ConfirmDialog },
  props: {
    lateralityOptions: {
      type: Array,
      default: () => [],
    },
  },
  data() {
    return {
      isDialogVisible: false,
      isFormVisible: false,
      editingPackage: null,
      query: "",
      packageList: [],
      totalPackages: 0,
      isLoading: false,
      confirmDialog: false,
      packageToDelete: null,
      selectedPackages: [],
      exams: [],
      tableOptions: {
        page: 1,
        itemsPerPage: 5,
        sortBy: [],
        sortDesc: [],
      },
      headers: [
        { text: "Selecionar", value: "select", sortable: false },
        { text: "Nome", value: "name" },
        { text: "Exames", value: "exams", sortable: false },
        { text: "Ações", value: "actions", sortable: false },
      ],
      totalPages: 1,
    };
  },
  computed: {
    confirmMessage() {
      return `Tem certeza que deseja excluir o pacote "${this.packageToDelete?.name}"? Esta ação não poderá ser desfeita.`;
    },
  },
  watch: {
    totalPackages(val) {
      this.totalPages = Math.ceil(val / this.tableOptions.itemsPerPage);
    },
    tableOptions: {
      handler() {
        this.totalPages = Math.ceil(
          this.totalPackages / this.tableOptions.itemsPerPage
        );
        this.loadPackages();
      },
      deep: true,
    },
    query: "loadPackages",
  },
  methods: {
    async fetchExams() {
      try {
        const res = await api.get("/exams");
        this.exams = res.data?.data || [];
      } catch (err) {
        console.error("Erro ao carregar exames:", err);
      }
    },

    async loadPackages() {
      this.isLoading = true;
      try {
        const { page, itemsPerPage } = this.tableOptions;
        const response = await api.get("/packages", {
          params: {
            page,
            per_page: itemsPerPage,
            search: this.query,
          },
        });
        this.packageList = response.data.data;
        this.totalPackages = response.data.meta.total;
      } catch (err) {
        console.error("Erro ao carregar pacotes:", err);
      } finally {
        this.isLoading = false;
      }
    },

    async openDialog() {
      this.isDialogVisible = true;
      this.isFormVisible = false;
      this.selectedPackages = [];

      if (this.exams.length === 0) {
        await this.fetchExams();
      }

      this.loadPackages();
    },

    onEditPackage(pkg) {
      this.editingPackage = { ...pkg };
      this.isFormVisible = true;
    },

    onCreateNewPackage() {
      this.editingPackage = null;
      this.isFormVisible = true;
    },

    async handleFormSaved(payload) {
      try {
        if (this.editingPackage) {
          await api.put(`/packages/${this.editingPackage.id}`, payload);
        } else {
          await api.post("/packages", payload);
        }

        this.isFormVisible = false;
        this.editingPackage = null;
        this.loadPackages();
      } catch (err) {
        this.$emit("error", "Erro ao salvar pacote. Tente novamente.");
      }
    },

    handleFormCancel() {
      this.isFormVisible = false;
      this.editingPackage = null;
    },

    onApplySelected() {
      const selectedObjects = this.packageList.filter((pkg) =>
        this.selectedPackages.includes(pkg.id)
      );
      this.$emit("apply", selectedObjects);
      this.closeDialog();
    },

    closeDialog() {
      this.isDialogVisible = false;
      this.isFormVisible = false;
      this.editingPackage = null;
      this.selectedPackages = [];
    },

    confirmDeletePackage(pkg) {
      this.packageToDelete = pkg;
      this.confirmDialog = true;
    },

    async deleteConfirmed() {
      try {
        await api.delete(`/packages/${this.packageToDelete.id}`);
        this.$emit("success", "Pacote excluído com sucesso.");
        this.loadPackages();
      } catch (error) {
        this.$emit("error", "Erro ao excluir o pacote.");
      } finally {
        this.confirmDialog = false;
        this.packageToDelete = null;
      }
    },
  },
};
</script>

<style scoped>
.btn-primary {
  background-color: #0f1e52 !important;
  color: white !important;
}
</style>
