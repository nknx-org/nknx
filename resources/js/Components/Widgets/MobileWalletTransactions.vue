<template>
    <Card
        col="12"
        padding="none"
        title="Transactions"
        :counter="`${transactions.total} in total`"
    >
        <div class="mobile-wallet-tx">
            <div
                v-for="(tx, i) in transactions.data"
                :key="i"
                class="mobile-wallet-tx__item"
            >
                <div class="mobile-wallet-tx__title">Block</div>
                <div class="mobile-wallet-tx__value text_wrap_none">
                    <a
                        :href="`https://nscan.io/blocks/${tx.block_height}`"
                        target="_blank"
                        class="text_link"
                        >{{ tx.block_height }}</a
                    >
                </div>
                <div class="mobile-wallet-tx__title">Hash</div>
                <div class="mobile-wallet-tx__value text_wrap_none">
                    <a
                        :href="`https://nscan.io/transactions/${tx.hash}`"
                        target="_blank"
                        class="text_link"
                        >{{ tx.hash }}</a
                    >
                </div>
                <div class="mobile-wallet-tx__title">Type</div>
                <div class="mobile-wallet-tx__value text_wrap_none">
                    <TransactionTypeTitle :type="tx.txType" />
                </div>
                <div class="mobile-wallet-tx__title">Created</div>
                <div class="mobile-wallet-tx__value">
                    {{ $moment(tx.created_at + "Z").fromNow() }}
                </div>
            </div>
        </div>
        <div class="mobile-wallet-tx__footer">
            <a
                :href="`https://nscan.io/addresses/${activeWallet.address}`"
                target="_blank"
                class="text_link"
                >View in Explorer</a
            >
        </div>
    </Card>
</template>

<script>
import { mapState } from "vuex";

import Card from "@/Components/Global/Card";
import TransactionTypeTitle from "@/Components/Global/TransactionTypeTitle";

export default {
    components: {
        Card,
        TransactionTypeTitle
    },
    props: ["transactions"],
    data: () => {
        return {
            current_page: 1,
            from: 0,
            to: 0,
            loading: false
        };
    },
    computed: {
        ...mapState({
            activeWallet: state => state.wallets.activeWallet
        })
    },
    watch: {},
    mounted() {},
    methods: {}
};
</script>
