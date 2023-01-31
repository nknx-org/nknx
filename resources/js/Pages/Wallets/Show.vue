<template>
    <app-layout>
        <div class="page__wallet-tracker">
            <ContentWrapper class="page__wallet-tracker-wrapper">
                <Grid>
                    <WalletPanel />
                    <WalletBalanceHistory />
                    <WalletTransactions
                        v-if="$mq !== 'md' && $mq !== 'sm' && $mq !== 'xs'"
                        :transactions="transactions"
                    />
                    <MobileWalletTransactions
                        :transactions="transactions"
                        v-else
                    />
                </Grid>
            </ContentWrapper>
            <WalletSide
                v-if="$mq !== 'md' && $mq !== 'sm' && $mq !== 'xs'"
                :wallets="wallets"
            />
        </div>
    </app-layout>
</template>

<script>
import { mapMutations } from "vuex";

import AppLayout from "@/Layouts/AppLayout";
import ContentWrapper from "@/Components/Global/ContentWrapper";
import Grid from "@/Components/Global/Grid";
import WalletSide from "@/Components/Widgets/WalletSide";
import WalletPanel from "@/Components/Widgets/WalletPanel";
import WalletBalanceHistory from "@/Components/Widgets/WalletBalanceHistory";
import WalletTransactions from "@/Components/Widgets/WalletTransactions";
import MobileWalletTransactions from "@/Components/Widgets/MobileWalletTransactions";

export default {
    props: ["wallet", "wallets", "transactions"],
    components: {
        AppLayout,
        ContentWrapper,
        Grid,
        WalletSide,
        WalletPanel,
        WalletBalanceHistory,
        WalletTransactions,
        MobileWalletTransactions
    },
    data() {
        return {
            tx: {}
        };
    },
    mounted() {
        this.setActiveWallet(this.wallet);
    },
    methods: {
        ...mapMutations({
            setActiveWallet: "wallets/setActiveWallet"
        })
    }
};
</script>
