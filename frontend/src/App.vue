<template>
  <v-app>
    <v-app-bar app color="white" flat elevation="1" class="px-4">
      <v-btn icon @click="drawerLeft = !drawerLeft" v-if="isMobile">
        <v-icon>mdi-menu</v-icon>
      </v-btn>

      <v-img
        :src="require('@/assets/logo.png')"
        alt="Logo"
        max-height="110"
        max-width="110"
        class="mr-3"
        contain
      />
      <div class="d-flex align-center mr-6">
        <span class="font-weight-medium text-body-1"
          >Clínica Eyecare Health</span
        >
        <v-icon size="18" class="ml-1">mdi-chevron-down</v-icon>
      </div>
      <v-spacer />
      <div class="d-flex align-center">
        <v-btn
          v-for="(icon, i) in headerIcons"
          :key="`header-icon-${i}`"
          icon
          variant="text"
          class="mx-1"
        >
          <v-icon>{{ icon }}</v-icon>
        </v-btn>
      </div>
      <div class="d-flex align-center ml-6">
        <span class="mr-1">Olá, Da. Sulivan</span>
        <v-icon size="18">mdi-chevron-down</v-icon>
      </div>
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

    <SideNavigationDrawer
      :icons="rightIcons"
      right
      v-model="drawerRight"
      :permanent="!isMobile"
      :temporary="isMobile"
      mini-variant
      :width="56"
      class="sidebar-right"
      icon-color="black"
    />

    <v-main>
      <v-container class="mt-4">
        <router-view />
      </v-container>
    </v-main>
  </v-app>
</template>

<script>
import SideNavigationDrawer from "@/components/Partials/SideNavigationDrawer.vue";

export default {
  name: "App",
  components: {
    SideNavigationDrawer,
  },
  data() {
    return {
      drawerLeft: false,
      drawerRight: false,
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
      rightIcons: [
        "mdi-home",
        "mdi-star",
        "mdi-folder",
        "mdi-settings",
        "mdi-information",
        "mdi-account-circle",
        "mdi-logout",
        "mdi-lock",
      ],
      headerIcons: [
        "mdi-file-document",
        "mdi-package-variant",
        "mdi-send",
        "mdi-calendar",
        "mdi-chart-line",
        "mdi-account",
        "mdi-email",
        "mdi-bell",
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
        this.drawerRight = true;
      } else {
        this.drawerLeft = false;
        this.drawerRight = false;
      }
    },
  },
  mounted() {
    if (this.isMobile) {
      this.drawerLeft = false;
      this.drawerRight = false;
    } else {
      this.drawerLeft = true;
      this.drawerRight = true;
    }
  },
};
</script>

<style scoped>
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
