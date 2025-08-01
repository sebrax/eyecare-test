<template>
  <v-app>
    <v-app-bar app color="white" elevation="1" class="px-4">
      <v-btn icon @click="drawerLeft = !drawerLeft" v-if="isMobile">
        <v-icon>mdi-menu</v-icon>
      </v-btn>

      <AppLogo class="mr-10" />

      <div class="d-flex align-center mr-6">
        <span class="font-weight-medium text-body-1 grey--text text--darken-1"
          >Clínica Eyecare Health</span
        >
        <v-icon size="18" class="ml-1">mdi-chevron-down</v-icon>
      </div>
      <v-spacer />
      <v-btn text class="grey--text text--darken-1">
        <span class="avatar primary lighten-2">SB</span>
        <span class="mr-1 text-capitalize">Olá, Dr. Sulivan</span>
        <v-icon size="18">mdi-chevron-down</v-icon>
      </v-btn>
    </v-app-bar>

    <SideNavigationDrawer
      :icons="icons"
      v-model="drawerLeft"
      :permanent="!isMobile"
      :temporary="isMobile"
      mini-variant
      :width="56"
      class="left-drawer"
    >
      <template #footer>
        <div class="drawer-footer white--text text-caption">v3.2.8</div>
      </template>
    </SideNavigationDrawer>

    <v-main>
      <v-container class="mt-4">
        <router-view />
      </v-container>
    </v-main>
  </v-app>
</template>

<script>
import SideNavigationDrawer from "@/components/Partials/SideNavigationDrawer.vue";
import AppLogo from "@/components/Layout/AppLogo.vue";

export default {
  name: "App",
  components: {
    SideNavigationDrawer,
    AppLogo,
  },
  data() {
    return {
      drawerLeft: false,
      icons: [
        "mdi-file-document",
        "mdi-package-variant",
        "mdi-send",
        "mdi-account",
        "mdi-calendar",
        "mdi-chart-line",
        "mdi-cog",
        "mdi-help-circle",
        "mdi-bell",
        "mdi-email",
        "mdi-map-marker",
        "mdi-heart",
        "mdi-notebook",
        "mdi-shield",
      ],
    };
  },
  computed: {
    isMobile() {
      return this.$vuetify.breakpoint.smAndDown;
    },
  },
  watch: {
    isMobile(val) {
      if (!val) {
        this.drawerLeft = true;
      } else {
        this.drawerLeft = false;
      }
    },
  },
  mounted() {
    if (this.isMobile) {
      this.drawerLeft = false;
    } else {
      this.drawerLeft = true;
    }
  },
};
</script>

<style scoped>
.avatar {
  font-size: 1rem;
  padding: 7px;
  border-radius: 100px;
  color: white;
}
.border-right {
  border-right: 1px solid #e0e0e0;
}
.border-left {
  border-left: 1px solid #e0e0e0;
}
.left-drawer {
  background-color: #0f1e52 !important;
}

.drawer-footer {
  padding: 8px 0;
  text-align: center;
}
</style>
