<template>
  <v-card class="pa-8 mx-auto" max-width="520" elevation="3" rounded>
    <v-card-title class="d-flex justify-space-between align-center">
      <div>
        <h2 class="font-weight-bold mb-1">
          {{ isEdit ? "Editar Exame" : "Adicionar Exame" }}
        </h2>
        <div class="subtitle-2 grey--text">
          {{
            isEdit
              ? "Atualize os dados do exame"
              : "Preencha os dados para criar um novo exame"
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
            label="Nome do Exame"
            outlined
            dense
            :rules="[requiredRule('Nome é obrigatório')]"
            required
            class="mb-4"
          />
        </v-col>

        <v-col cols="12" sm="6">
          <v-select
            v-model="form.laterality"
            :items="lateralityOptions"
            label="Lateralidade"
            item-value="value"
            item-text="label"
            outlined
            dense
            clearable
            class="mb-4"
          />
        </v-col>

        <v-col cols="12">
          <v-text-field
            v-model="form.comment"
            label="Comentário"
            outlined
            dense
            :rules="[requiredRule('Comentário é obrigatório')]"
            required
            class="mb-4"
          />
        </v-col>

        <v-col cols="12">
          <v-select
            v-model="form.group"
            :items="groupOptions"
            label="Grupo"
            item-value="value"
            item-text="label"
            outlined
            dense
            :rules="[requiredRule('Grupo é obrigatório')]"
            required
            class="mb-6"
          />
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
            {{ isEdit ? "Salvar Alterações" : "Cadastrar Exame" }}
          </v-btn>
        </v-col>
      </v-row>
    </v-form>

    <v-snackbar
      v-model="snackbar.show"
      :timeout="3000"
      top
      right
      :color="snackbar.color"
    >
      {{ snackbar.message }}
      <template #action="{ attrs }">
        <v-btn text v-bind="attrs" @click="snackbar.show = false">Fechar</v-btn>
      </template>
    </v-snackbar>
  </v-card>
</template>

<script>
import api from "@/services/api";

export default {
  name: "ExamForm",
  props: {
    exam: {
      type: Object,
      default: null,
    },
    lateralityOptions: {
      type: Array,
      default: () => [],
    },
    groupOptions: {
      type: Array,
      default: () => [],
    },
  },
  data() {
    return {
      valid: false,
      form: {
        name: "",
        laterality: null,
        comment: "",
        group: null,
      },
      snackbar: {
        show: false,
        message: "",
        color: "success",
      },
      validationErrors: {},
    };
  },
  computed: {
    isEdit() {
      return !!(this.exam && this.exam.id);
    },
  },
  watch: {
    exam: {
      immediate: true,
      handler(val) {
        if (val) {
          this.form = {
            name: val.name || "",
            laterality: val.laterality || null,
            comment: val.comment || "",
            group: val.group || this.groupOptions[0]?.value || null,
          };
        } else {
          this.resetForm();
        }
      },
    },

    groupOptions: {
      immediate: true,
      handler(newGroups) {
        if (!this.isEdit && (!this.form.group || !newGroups.length)) {
          this.form.group = newGroups[0]?.value || null;
        }
      },
    },
  },
  methods: {
    resetForm() {
      this.form = {
        name: "",
        laterality: null,
        comment: "",
        group: this.groupOptions[0]?.value || null,
      };
      this.$refs.form?.resetValidation();
      this.valid = false;
      this.validationErrors = {};
    },

    requiredRule(msg) {
      return (v) => !!v || msg;
    },

    async submit() {
      if (!this.valid) return;

      try {
        let response;
        if (this.isEdit) {
          response = await api.put(`/exams/${this.exam.id}`, this.form);
        } else {
          response = await api.post("/exams", this.form);
        }

        const message = response.data?.message || "Salvo com sucesso!";
        this.snackbar.message = message;
        this.snackbar.color = "success";
        this.snackbar.show = true;

        if (!this.isEdit) {
          this.resetForm();
        }

        setTimeout(() => {
          this.$emit("saved");
        }, 3000);
      } catch (error) {
        if (error.response && error.response.status === 422) {
          const errors = error.response.data.errors || {};
          const messages = Object.values(errors).flat().join(" | ");

          this.snackbar.message = messages || "Erro de validação.";
          this.snackbar.color = "error";
          this.snackbar.show = true;

          this.validationErrors = errors;
        } else {
          this.snackbar.message = "Erro ao salvar exame. Tente novamente.";
          this.snackbar.color = "error";
          this.snackbar.show = true;
          console.error(error);
        }
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
