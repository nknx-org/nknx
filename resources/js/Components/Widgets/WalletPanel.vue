<template>
    <Card col="12" padding="none">
        <div class="wallet-panel">
            <div class="wallet-panel__left">
                <div
                    class="wallet-panel__label"
                    v-text="wallet.label || `Wallet ${wallet.id}`"
                ></div>
                <div class="wallet-panel__value">
                    {{ parseFloat(wallet.balance).toFixed(2) | commaNumber }}
                    <span class="wallet-panel__currency">NKN</span>
                </div>
                <div
                    v-clipboard:copy="wallet.address"
                    class="wallet-panel__address"
                >
                    {{ wallet.address }}
                    <svg-vue class="wallet-panel__copy" icon="copy"></svg-vue>
                </div>
            </div>
            <div class="wallet-panel__button" @click="openWalletQrModal">
                <svg-vue class="wallet-panel__qr" icon="qr"></svg-vue>
                Receive
            </div>
        </div>
    </Card>
</template>

<script>
import { mapState } from "vuex";

import Card from "@/Components/Global/Card";

export default {
    components: {
        Card
    },
    data: () => {
        return {};
    },
    computed: {
        ...mapState({
            wallet: state => state.wallets.activeWallet
        })
    },
    destroyed() {},
    mounted: function() {},
    methods: {
        openWalletQrModal() {
            this.$bus.$emit("openWalletQrModal");
        }
    }
};
</script>
