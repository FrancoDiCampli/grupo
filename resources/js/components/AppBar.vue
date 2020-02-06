<template>
    <div>
        <!-- Navbar superior -->
        <v-app-bar color="primary" dark flat app fixed>
            <!-- Titulo de la aplicacion -->
            <v-toolbar-title style="cursor: pointer;"
                >Controller Auth</v-toolbar-title
            >
            <v-spacer></v-spacer>

            <!-- Menu del usuario -->
            <v-menu offset-y v-if="$store.state.auth.user != null">
                <template v-slot:activator="{ on }">
                    <v-avatar
                        color="secondary"
                        style="cursor: pointer;"
                        v-on="on"
                    >
                        <img
                            v-if="$store.state.auth.user.user.foto != null"
                            :src="$store.state.auth.user.user.foto"
                            width="100%"
                            height="auto"
                        />
                        <span
                            v-else-if="$store.state.auth.user.user.name"
                            class="text-uppercase"
                            >{{ $store.state.auth.user.user.name[0] }}</span
                        >
                    </v-avatar>
                </template>
                <v-list>
                    <v-list-item @click="sidenav = true">
                        <v-list-item-title>Perfil</v-list-item-title>
                    </v-list-item>
                    <slot></slot>
                </v-list>
            </v-menu>
        </v-app-bar>

        <!-- Sidenav de usuario -->
        <v-slide-x-reverse-transition>
            <v-col
                cols="12"
                sm="5"
                lg="4"
                v-show="sidenav"
                class="sidenav pa-0"
            >
                <v-card tile class="sidenav-overflow">
                    <v-toolbar color="secondary" dark flat>
                        <v-toolbar-title>Perfil</v-toolbar-title>
                        <v-spacer></v-spacer>
                        <v-btn @click="sidenav = false" icon>
                            <v-icon>fas fa-arrow-right</v-icon>
                        </v-btn>
                    </v-toolbar>
                    <Account v-if="$store.state.auth.user != null"></Account>
                </v-card>
            </v-col>
        </v-slide-x-reverse-transition>
    </div>
</template>

<script>
import Account from "./auth/Account";

export default {
    data: () => ({
        sidenav: false
    }),

    components: {
        Account
    }
};
</script>

<style>
.sidenav {
    position: fixed;
    right: 0;
    width: 100%;
    z-index: 9;
    height: 100vh;
}

.sidenav-overflow {
    height: 100vh;
    max-height: 100vh;
    overflow: auto;
}

.sidenav-overflow::-webkit-scrollbar {
    width: 6px;
}

.sidenav-overflow::-webkit-scrollbar-thumb {
    background-color: rgba(189, 189, 189, 0.75);
}

.drawer-action {
    height: 63.5px;
}

.drawer-action-icon {
    cursor: pointer;
    margin-top: 20px !important;
}

@media (max-width: 960px) {
    .drawer-action {
        height: 55.5px;
    }

    .drawer-action-icon {
        margin-top: 16px !important;
    }
}

.drawer-routes {
    margin-top: -8px;
}
</style>
