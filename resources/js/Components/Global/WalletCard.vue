<template>
    <Card
        class="wallet-card"
        :col="$mq === 'xl' ? '3' : $mq === 'llg' ? '6' : '12'"
        :hover="true"
        :overflow="false"
        @click.native="openWallet"
    >
        <div class="wallet-card__grid">
            <div class="wallet-card__icon">
                <svg-vue icon="wallet"></svg-vue>
            </div>
            <div class="wallet-card__data">
                <div class="wallet-card__header">
                    <div class="wallet-card__title text_wrap_none">
                        {{ label }}
                    </div>
                    <v-popover
                        offset="4"
                        @click.native.stop
                        class="justify-self-end"
                    >
                        <div class="wallet-card__actions tooltip-target">
                            <svg-vue
                                class="wallet-card__actions-icon"
                                icon="more"
                            ></svg-vue>
                        </div>
                        <template slot="popover">
                            <div class="popover_actions">
                                <div class="popover__actions">
                                    <div
                                        class="popover__actions-item"
                                        @click="openDeleteWalletModal(wallet)"
                                    >
                                        <svg-vue
                                            class="popover__actions-icon"
                                            icon="trash"
                                        ></svg-vue>
                                        Remove
                                    </div>
                                </div>
                            </div>
                        </template>
                    </v-popover>
                </div>

                <div class="wallet-card__balance">
                    {{ balance }}
                    <span class="wallet-card__currency">NKN</span>
                </div>
                <div class="wallet-card__price">
                    ${{ balanceUsd | commaNumber }} USD
                </div>
            </div>
        </div>
    </Card>
</template>

<script>
import { mapMutations } from "vuex";

import { mixin as clickaway } from "vue-clickaway";

import Card from "@/Components/Global/Card.vue";

export default {
    components: { Card },
    mixins: [clickaway],
    props: {
        wallet: {
            type: Object,
            default: () => {}
        }
    },
    data: () => {
        return {};
    },
    computed: {
        label() {
            return this.wallet.label
                ? this.wallet.label
                : `Wallet #${this.wallet.id}`;
        },
        balance() {
            return Number(this.wallet.balance).toFixed(2);
        },
        balanceUsd() {
            return (this.wallet.balance * this.$page.props.prices.usd).toFixed(
                2
            );
        }
    },
    destroyed() {},
    mounted() {},
    methods: {
        ...mapMutations({
            setActiveWallet: "wallets/setActiveWallet"
        }),
        openWallet() {
            this.setActiveWallet(this.wallet);
            this.$inertia.get(`/wallets/${this.wallet.id}`);
        },
        openDeleteWalletModal(wallet) {
            this.setActiveWallet(wallet);
            this.$bus.$emit("removeWalletModal");
        }
    }
};
</script>
