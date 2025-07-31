<template>
  <div>
    <v-text-field
      v-model="localSearchQuery"
      placeholder="Pesquisar pacotes"
      append-icon="mdi-magnify"
      clearable
      class="mb-4"
      @input="onSearchInput"
    />

    <v-data-table
      :headers="tableHeaders"
      :items="packageList"
      :options.sync="localTableOptions"
      :server-items-length="totalPackages"
      :loading="isLoading"
      class="elevation-1"
      item-key="id"
      @update:options="onTableOptionsChange"
    >
      <template v-slot:item.select="{ item }">
        <v-checkbox
          v-model="localSelectedPackages"
          :value="item.id"
          @change="onSelectedChange"
        />
      </template>

      <template v-slot:item.exams="{ item }">
        <div class="d-flex flex-wrap">
          <v-chip
            v-for="exam in item.exams || []"
            :key="exam.id"
            small
            class="ma-1"
            color="#0f1e52"
            text-color="white"
          >
            {{ exam.name }}
          </v-chip>
        </div>
      </template>

      <template v-slot:item.actions="{ item }">
        <v-btn icon small @click="$emit('edit-package', item)">
          <v-icon>mdi-pencil</v-icon>
        </v-btn>
        <v-btn icon small @click="$emit('confirm-delete', item)">
          <v-icon color="error">mdi-delete</v-icon>
        </v-btn>
      </template>
    </v-data-table>

    <v-card-actions>
      <v-pagination
        v-model="localTableOptions.page"
        :length="totalPages"
        @input="onPageChange"
      />
      <v-spacer />
      <v-btn color="secondary" @click="$emit('create-new-package')">
        Criar Pacote
      </v-btn>
      <v-btn
        color="success"
        :disabled="localSelectedPackages.length === 0"
        @click="applySelected"
      >
        Aplicar Pacotes Selecionados
      </v-btn>
    </v-card-actions>
  </div>
</template>

<script>
export default {
  name: "PackageListTable",
  props: {
    packageList: {
      type: Array,
      default: () => [],
    },
    tableHeaders: {
      type: Array,
      required: true,
    },
    tableOptions: {
      type: Object,
      required: true,
    },
    totalPackages: {
      type: Number,
      required: true,
    },
    isLoading: {
      type: Boolean,
      default: false,
    },
    selectedPackages: {
      type: Array,
      default: () => [],
    },
    totalPages: {
      type: Number,
      required: true,
    },
  },
  data() {
    return {
      localSearchQuery: "",
      localTableOptions: { ...this.tableOptions },
      localSelectedPackages: [...this.selectedPackages],
    };
  },
  watch: {
    tableOptions(newVal) {
      this.localTableOptions = { ...newVal };
    },
    selectedPackages(newVal) {
      this.localSelectedPackages = [...newVal];
    },
  },
  methods: {
    onSearchInput() {
      this.$emit("update:search-query", this.localSearchQuery);
    },
    onTableOptionsChange(options) {
      this.localTableOptions = options;
      this.$emit("update:table-options", options);
    },
    onPageChange(page) {
      this.localTableOptions.page = page;
      this.$emit("update:table-options", this.localTableOptions);
    },
    onSelectedChange() {
      this.$emit("update:selected-packages", this.localSelectedPackages);
    },
    applySelected() {
      this.$emit("apply-selected");
    },
  },
};
</script>

<style scoped>
.ma-1 {
  margin: 4px !important;
}
</style>
