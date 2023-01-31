<template>
    <div class="fd-choice">
        <h3 class="fd-choice__title">
            Deploy to
        </h3>
        <div class="fd-choice__wrapper">
            <Card
                v-for="provider in providers"
                :key="provider"
                class="fd-choice__card"
                :class="
                    isProviderDisabled(provider)
                        ? 'fd-choice__card_disabled'
                        : null
                "
                col="none"
                padding="medium"
                @click.native="
                    isProviderDisabled(provider)
                        ? false
                        : openFastDeployModal(provider)
                "
            >
                <svg-vue :icon="provider" class="fd-choice__img" />
                <div class="fd-choice__descr">{{ provider }}</div>
            </Card>
        </div>
    </div>
</template>

<script>
import { mapState, mapMutations } from "vuex";

import Card from "@/Components/Global/Card";

export default {
    props: ["vps_keys"],
    components: {
        Card
    },
    data: function() {
        return {
            providers: ["DigitalOcean", "Vultr", "Hetzner", "AWS", "Custom"]
        };
    },
    computed: {
        ...mapState({
            activeFdConfig: state => state.fdConfig.activeFdConfig
        })
    },
    mounted() {},
    methods: {
        ...mapMutations({
            setActiveProfiles: "fdConfig/setActiveProfiles",
            setActiveProvider: "fdConfig/setActiveProvider"
        }),
        isProviderDisabled(provider) {
            if (this.activeFdConfig.id === 0) return true;

            return provider === "Custom"
                ? false
                : this._.findIndex(this.vps_keys, ["provider", provider]) ===
                      -1;
        },
        async openFastDeployModal(provider) {
            this.setActiveProfiles(
                this._.filter(this.vps_keys, ["provider", provider])
            );
            this.setActiveProvider(provider);

            if (provider == "Custom") {
                this.$bus.emit("openCustomFastDeployModal");
            } else {
                this.$bus.emit("openFastDeployModal");
            }
        }
    }
};
</script>
