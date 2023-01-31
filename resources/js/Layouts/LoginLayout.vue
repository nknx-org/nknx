<template>
    <div class="login">
        <div class="login__left">
            <div class="container">
                <div class="row">
                    <div class="col-lg-8">
                        <div class="login__header">
                            <inertia-link href="/"
                                ><svg-vue
                                    class="login__header-logo"
                                    icon="logo-full"
                            /></inertia-link>
                        </div>
                        <div class="login__descr">
                            <div class="login__descr-title">
                                Node management made simple.
                            </div>
                            <div class="login__descr-subtitle">
                                Deploy and manage hundreds of nodes at once with
                                NKNx fast deploy.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="login__img-container">
                <img src="/assets/images/Nodes.png" class="login__img" />
            </div>
        </div>
        <div class="login__right">
            <div class="container">
                <svg-vue class="login__logo" icon="logo-full" />

                <div class="row">
                    <div class="col-12">
                        <slot></slot>
                    </div>
                </div>
            </div>
        </div>
        <Snackbar />
    </div>
</template>

<script>
import Snackbar from "@/Components/Global/Snackbar";

export default {
    components: { Snackbar },
    data: () => ({}),
    computed: {},
    watch: {},
    methods: {},
    mounted() {}
};
</script>
