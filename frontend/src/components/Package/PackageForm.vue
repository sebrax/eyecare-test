<template>
  <v-card class="pa-8 mx-auto" max-width="700" elevation="3" rounded>
    <v-card-title class="d-flex justify-space-between align-center">
      <div>
        <h2 class="font-weight-bold mb-1">
          {{ isEdit ? "Editar Pacote" : "Adicionar Pacote" }}
        </h2>
        <div class="subtitle-2 grey--text">
          {{
            isEdit
              ? "Atualize os dados do pacote"
              : "Preencha os dados para criar um novo pacote"
          }}
        </div>
      </div>

      <v-btn icon @click="$emit('cancel')" aria-label="Fechar">
        <v-icon>mdi-close</v-icon>
      </v-btn>
    </v-card-title>

    <v-divider class="my-4" />

    <v-form ref="form" v-model="valid" @submit.prevent="submit" lazy-validation>
      <v-row dense>
        <v-col cols="12" sm="6">
          <v-text-field
            v-model="form.name"
            label="Nome do Pacote"
            outlined
            dense
            :rules="[requiredRule('Nome é obrigatório')]"
            required
            class="mb-4"
          />
        </v-col>

        <v-col cols="12" sm="6">
          <v-textarea
            v-model="form.observations"
            label="Observações"
            outlined
            dense
            auto-grow
            class="mb-4"
          />
        </v-col>

        <v-col cols="12">
          <v-autocomplete
            v-model="form.exams"
            :items="exams"
            item-text="name"
            item-value="id"
            label="Exames do Pacote"
            outlined
            multiple
            clearable
            chips
            deletable-chips
            :rules="[requiredRule('Selecione ao menos um exame')]"
            required
            class="mb-2"
            :menu-props="{ maxHeight: '300px' }"
          />

          <div v-if="form.exams.length" class="mb-4">
            <strong>Exames selecionados:</strong>
            <v-chip
              v-for="exId in form.exams"
              :key="exId"
              small
              class="ma-1"
              color="primary"
              text-color="black"
              outlined
            >
              {{ getExamNameById(exId) }}
            </v-chip>
          </div>

          <v-simple-table v-if="form.exams.length">
            <thead>
              <tr>
                <th>Nome</th>
                <th>Lateralidade</th>
                <th>Comentário</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="exam in selectedExamObjects" :key="exam.id">
                <td>{{ exam.name }}</td>
                <td>{{ getLateralityLabel(exam.laterality) || "—" }}</td>
                <td>{{ exam.comment || "—" }}</td>
              </tr>
            </tbody>
          </v-simple-table>
        </v-col>
      </v-row>

      <v-row justify="end" align="center" dense>
        <v-col cols="auto">
          <v-btn text color="grey darken-1" @click="$emit('cancel')">
            Cancelar
          </v-btn>
        </v-col>

        <v-col cols="auto">
          <v-btn class="btn-primary" depressed :disabled="!valid" type="submit">
            {{ isEdit ? "Salvar Alterações" : "Cadastrar Pacote" }}
          </v-btn>
        </v-col>
      </v-row>
    </v-form>

    <v-snackbar
      v-model="snackbar.show"
      :color="snackbar.color"
      timeout="3000"
      top
      right
    >
      {{ snackbar.message }}
      <template #action="{ attrs }">
        <v-btn text v-bind="attrs" @click="snackbar.show = false">Fechar</v-btn>
      </template>
    </v-snackbar>
  </v-card>
</template>

<script>
export default {
  name: "PackageForm",
  props: {
    packageData: {
      type: Object,
      default: null,
    },
    exams: {
      type: Array,
      default: () => [],
    },
    lateralityOptions: {
      type: Array,
      default: () => [],
    },
  },
  data() {
    return {
      valid: false,
      form: {
        name: "",
        observations: "",
        exams: [],
      },
      snackbar: {
        show: false,
        message: "",
        color: "error",
      },
    };
  },
  computed: {
    isEdit() {
      return !!(this.packageData && this.packageData.id);
    },
    selectedExamObjects() {
      return this.exams.filter((e) => this.form.exams.includes(e.id));
    },
  },
  watch: {
    packageData: {
      immediate: true,
      handler(pkg) {
        if (pkg) {
          this.form.name = pkg.name || "";
          this.form.observations = pkg.observations || "";
          this.form.exams = pkg.exams ? pkg.exams.map((e) => e.id) : [];
        } else {
          this.resetForm();
        }
      },
    },
  },
  methods: {
    requiredRule(msg) {
      return (v) => (Array.isArray(v) ? v.length > 0 : !!v) || msg;
    },
    getExamNameById(id) {
      const exam = this.exams.find((e) => e.id == id);
      return exam ? exam.name : "Exame desconhecido";
    },
    getLateralityLabel(value) {
      const opt = this.lateralityOptions.find((opt) => opt.value === value);
      return opt?.label || "—";
    },
    resetForm() {
      this.form = {
        name: "",
        observations: "",
        exams: [],
      };
      this.$refs.form?.resetValidation();
      this.valid = false;
    },
    submit() {
      if (!this.valid) return;

      const payload = {
        name: this.form.name,
        observations: this.form.observations,
        exams: this.form.exams,
      };

      try {
        this.$emit("saved", payload);
        this.snackbar.message = this.isEdit
          ? "Pacote atualizado com sucesso!"
          : "Pacote criado com sucesso!";
        this.snackbar.color = "success";
        this.snackbar.show = true;
      } catch (error) {
        this.snackbar.message =
          "Ocorreu um erro ao salvar o pacote. Por favor, tente novamente.";
        this.snackbar.color = "error";
        this.snackbar.show = true;
        console.error(error);
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
.exams-summary {
  margin-top: 0.3rem;
}
.v-autocomplete.v-input.dense .v-input__control {
  flex-wrap: wrap;
  min-height: auto !important;
  padding-top: 20px;
}
.v-autocomplete.v-input.dense .v-chip {
  margin: 2px 4px 2px 0;
}
</style>
