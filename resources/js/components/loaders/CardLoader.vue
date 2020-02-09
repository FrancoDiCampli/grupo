<template>
    <div>
        <v-boilerplate class="mb-6" type="card, list-item-two-line"></v-boilerplate>
    </div>
</template>

<script>
export default {
    inject: ["theme"],

    components: {
        VBoilerplate: {
            functional: true,

            render(h, { data, props, children }) {
                return h(
                    "v-skeleton-loader",
                    {
                        ...data,
                        props: {
                            boilerplate: true,
                            elevation: 2,
                            ...props
                        }
                    },
                    children
                );
            }
        }
    }
};
</script>

<style>
</style>