<template>
  <v-navigation-drawer
    :app="true"
    :permanent="true"
    :mini-variant="true"
    :right="right"
    width="56"
    :class="drawerClasses"
  >
    <v-list dense nav>
      <v-list-item
        v-for="(icon, i) in icons"
        :key="icon + '-' + i"
        link
        :class="{ 'justify-center': right }"
      >
        <v-list-item-icon>
          <v-icon :color="iconColor">{{ icon }}</v-icon>
        </v-list-item-icon>
      </v-list-item>
    </v-list>
    <v-spacer />
    <slot name="footer" />
  </v-navigation-drawer>
</template>

<script>
export default {
  name: "SideNavigationDrawer",
  props: {
    icons: {
      type: Array,
      required: true,
    },
    right: {
      type: Boolean,
      default: false,
    },
    border: {
      type: Boolean,
      default: false,
    },
    iconColor: {
      type: String,
      default: "white",
    },
  },
  computed: {
    drawerClasses() {
      return {
        "border-right": this.border && !this.right,
        "border-left": this.border && this.right,
      };
    },
  },
};
</script>
