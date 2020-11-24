<template>
    <div>
         <v-row justify="center">
            <img src="img/logo.png" width="400" height="400" />
        </v-row>
        <h1 class="text-center secondary--text">Bienvenido</h1>
        <br />
    </div>
</template>

<script>
export default {
    methods: {
        exitTo(route) {
            window.open(route);
        },
    }
};
</script>

<style lang="scss" scoped>
.cliente-layout {
    height: 80vh;
}

.cliente-btn {
    cursor: pointer;
}

.cliente-icon {
    color: white !important;
    font-size: 78px !important;
}
</style>