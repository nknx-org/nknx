<template>
    <Card class="balance-card" col="12" :custom="true" title="Balance History">
        <WalletBalanceHistoryChart
            class="balance-card__chart"
            style="margin-top:24px; height: 190px; position: relative; left: -22px;"
        />
    </Card>
</template>

<script>
import Card from "@/Components/Global/Card";
import WalletBalanceHistoryChart from "@/Components/Charts/WalletBalanceHistoryChart";
import HiddenContent from "@/Components/Global/HiddenContent";

export default {
    components: {
        Card,
        WalletBalanceHistoryChart,
        HiddenContent
    },
    data: () => {
        return {};
    },
    computed: {
        hiddenButton() {
            const { $mq } = this;

            if ($mq === "xs" || $mq === "sm" || $mq === "md") {
                return false;
            } else {
                return true;
            }
        }
    },
    watch: {},
    destroyed() {},
    mounted() {},
    methods: {}
};
</script>
