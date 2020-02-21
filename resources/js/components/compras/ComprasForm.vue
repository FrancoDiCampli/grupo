<template>
    <div>
        <h1>Hola mundo</h1>
    </div>
</template>

<script>
import ClickOutside from "vue-click-outside";

export default {
    data: () => ({
        // GENERAL
        focus: null,
        inProcess: false,
        searchInProcess: false,
        disabled: {
            articulo: true,
            detalles: true,
            movimientos: false,
            lote: true
        },
        errorAlert: false,
        rules: {
            required: value => !!value || "Este campo es obligatorio",
            cantidadMaxima: value =>
                value <= Number(cantidadMaxima) ||
                "La cantidad no puede superar el stock existente"
        },
        // HEADER
        PuntoVenta: null,
        // DIALOGS
        fechaDialog: false
    }),

    directives: {
        ClickOutside
    },

    async mounted() {
        this.inProcess = true;
        this.getPoint();
        this.inProcess = false;
    },

    methods: {
        // GENERAL
        nullFocus() {
            this.focus = null;
        },

        // HEADER
        getPoint: async function() {
            let data;
            await axios
                .get("/api/config")
                .then(response => {
                    data = response.data;
                })
                .catch(error => {
                    console.log(error);
                });

            this.PuntoVenta = data.puntoventa;
        }
    }
};
</script>

<style lang="scss"></style>
