<template>
  <v-data-table
    :headers="headers"
    :items="items"
    :loading="loading"
    loading-text="Carregando exames..."
    dense
    class="elevation-1"
    item-key="id"
  >
    <template #item.group="{ item }">
      <v-chip small>{{ item.group || "—" }}</v-chip>
    </template>
    <template #item.laterality="{ item }">
      {{ getLateralityLabel(item.laterality) }}
    </template>
    <template #item.comment="{ item }">
      {{ item.comment || "—" }}
    </template>
    <template #item.actions="{ item }">
      <v-btn icon small @click="$emit('edit', item)" aria-label="Editar exame">
        <v-icon small color="primary">mdi-pencil</v-icon>
      </v-btn>
      <v-btn
        icon
        small
        color="red"
        @click="$emit('delete', item)"
        aria-label="Excluir exame"
      >
        <v-icon small>mdi-delete</v-icon>
      </v-btn>
    </template>
    <template #no-data> Nenhum exame encontrado. </template>
  </v-data-table>
</template>

<script>
export default {
  name: "ExamTable",
  props: {
    headers: Array,
    items: Array,
    loading: Boolean,
    page: Number,
    getLateralityLabel: Function,
  },
};
</script>
