<template>
    <div class="container policy">
        <div class="row">
            <div class="col-12">
                <inertia-link href="/"
                    ><svg-vue class="policy__logo" icon="logo-full"
                /></inertia-link>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div>
                    <Card class="policy__card">
                        <div v-html="policy"></div>
                    </Card>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import JetAuthenticationCardLogo from "@/Jetstream/AuthenticationCardLogo";
import Card from "@/Components/Global/Card.vue";

export default {
    props: ["policy"],

    components: {
        JetAuthenticationCardLogo,
        Card
    }
};
</script>
