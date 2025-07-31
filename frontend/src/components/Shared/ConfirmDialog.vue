<template>
  <v-dialog v-model="internalModel" max-width="420">
    <v-card class="pa-4">
      <v-card-title class="headline font-weight-bold">
        <v-icon :color="iconColor" large class="mr-2" v-if="icon">
          {{ icon }}
        </v-icon>
        {{ title }}
      </v-card-title>

      <v-card-text class="text-subtitle-1 text--primary">
        {{ message }}
      </v-card-text>

      <v-card-actions class="justify-end">
        <v-btn text color="grey" @click="cancel">Cancelar</v-btn>
        <v-btn color="red darken-1" dark @click="confirm">Confirmar</v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script>
export default {
  name: "ConfirmDialog",
  props: {
    value: {
      type: Boolean,
      required: true,
    },
    title: {
      type: String,
      default: "Confirmação",
    },
    message: {
      type: String,
      default: "Você tem certeza que deseja continuar?",
    },
    icon: {
      type: String,
      default: "mdi-alert-circle",
    },
    iconColor: {
      type: String,
      default: "red darken-1",
    },
  },
  computed: {
    internalModel: {
      get() {
        return this.value;
      },
      set(val) {
        this.$emit("input", val);
      },
    },
  },
  methods: {
    cancel() {
      this.internalModel = false;
      this.$emit("cancel");
    },
    confirm() {
      this.internalModel = false;
      this.$emit("confirm");
    },
  },
};
</script>
