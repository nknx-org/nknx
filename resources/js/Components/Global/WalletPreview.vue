<template>
    <div
        class="wallet-preview"
        :class="{
            'wallet-preview_active': activeWallet.address === wallet.address
        }"
        @click="toggleActiveWallet(wallet)"
    >
        <div class="wallet-preview__data">
            <div class="wallet-preview__label">
                {{ label }}
            </div>
            <div class="wallet-preview__value">
                {{ balance }}
                <span class="wallet-preview__currency">NKN</span>
            </div>
            <div class="wallet-preview__converter">
                ${{ balanceUsd | commaNumber }} USD
            </div>
        </div>
        <div class="wallet-preview__chart"></div>
    </div>
</template>

<script>
import { mapState, mapMutations } from "vuex";

export default {
    components: {},
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
        ...mapState({
            activeWallet: state => state.wallets.activeWallet
        }),
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
        toggleActiveWallet(wallet) {
            this.setActiveWallet(wallet);
        }
    }
};
</script>
