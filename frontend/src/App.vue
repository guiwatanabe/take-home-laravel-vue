<template>
  <v-app>
    <!-- Top App Bar -->
    <v-app-bar app flat outlined light>
      <div class="d-flex">
        <v-img
          src="logo-full.png"
          contain
          width="100"
          height="30"
          style="opacity: 0.6"
          class="mr-3 mt-1"
        ></v-img>

        <v-menu offset-y transition="slide-y-transition">
          <template v-slot:activator="{ on, attrs }">
            <v-btn text class="text-none" v-bind="attrs" v-on="on">
              <span class="mr-1 grey--text font-weight-medium">Clínica Um</span>
              <v-icon right color="accent">mdi-chevron-down</v-icon>
            </v-btn>
          </template>
          <v-list>
            <v-list-item>
              <v-list-item-title>Trocar</v-list-item-title>
            </v-list-item>
          </v-list>
        </v-menu>
      </div>

      <v-spacer></v-spacer>

      <v-btn icon>
        <v-icon>mdi-bell-outline</v-icon>
      </v-btn>
      <v-btn icon>
        <v-icon>mdi-cog-outline</v-icon>
      </v-btn>

      <v-menu offset-y transition="slide-y-transition">
        <template v-slot:activator="{ on, attrs }">
          <v-btn text class="mr-2 text-none" v-bind="attrs" v-on="on">
            <v-icon left>mdi-account-circle</v-icon>
            <span class="mr-1 grey--text font-weight-medium"
              >Dr. Doutor Doutor</span
            >
            <v-icon right color="accent">mdi-chevron-down</v-icon>
          </v-btn>
        </template>
        <v-list>
          <v-list-item>
            <v-list-item-title>Perfil</v-list-item-title>
          </v-list-item>
          <v-list-item>
            <v-list-item-title>Preferências</v-list-item-title>
          </v-list-item>
          <v-divider></v-divider>
          <v-list-item>
            <v-list-item-title>Encerrar Sessão</v-list-item-title>
          </v-list-item>
        </v-list>
      </v-menu>
    </v-app-bar>

    <!-- Mini Navigation Drawer -->
    <v-navigation-drawer v-model="drawer" app permanent mini-variant>
      <v-list-item style="height: 64px">
        <v-img src="logo-icon.png" contain></v-img>
      </v-list-item>

      <v-list dense nav>
        <v-list-item link class="v-list-item--active">
          <v-tooltip right>
            <template v-slot:activator="{ on, attrs }">
              <v-list-item-icon v-bind="attrs" v-on="on">
                <v-icon>mdi-test-tube</v-icon>
              </v-list-item-icon>
            </template>
            <span>Exames</span>
          </v-tooltip>
          <v-list-item-content>
            <v-list-item-title> Exames </v-list-item-title>
          </v-list-item-content>
        </v-list-item>
      </v-list>
    </v-navigation-drawer>

    <!-- Main Content Area -->
    <v-main>
      <v-container fluid>
        <router-view />
        <v-snackbar
          v-if="toast"
          v-model="toast.show"
          :color="toast.color"
          :timeout="toast.timeout"
          top
          right
        >
          {{ toast.text }}

          <template v-slot:action="{ attrs }">
            <v-btn text v-bind="attrs" @click="toast.close"
              ><v-icon>mdi-window-close</v-icon></v-btn
            >
          </template>
        </v-snackbar>
      </v-container>
    </v-main>
  </v-app>
</template>

<script>
import toaster from "@/util/toaster";

export default {
  name: "App",
  data: () => ({
    toast: toaster,
    drawer: true,
  }),
};
</script>
