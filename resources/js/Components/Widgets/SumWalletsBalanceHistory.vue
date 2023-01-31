<template>
    <Card class="balance-card" col="12" :custom="true" title="Balance History">
        <div class="balance-card__header">
            <div class="stats-item__wrapper stats-item__wrapper_full">
                <div class="stats-item">
                    <div class="stats-item__title">Total Balance</div>
                    <div class="stats-item__value">
                        {{ totalBalance | commaNumber }}
                        <span class="stats-item__index">NKN</span>
                    </div>
                </div>
            </div>

            <div class="balance-card__actions">
                <div
                    class="toggle"
                    :class="
                        activeTimeFrame === item.value ? 'toggle_active' : false
                    "
                    v-for="item in timeFrames"
                    :key="item.value"
                    @click="toggleTimeframe(item.value)"
                >
                    {{ item.title }}
                </div>
            </div>
        </div>

        <SumWalletsBalanceHistoryChart
            :snapshots="snapshots"
            class="balance-card__chart"
            style="margin-top:24px; height: 190px; position: relative; left: -22px;"
        />
    </Card>
</template>

<script>
import Card from "@/Components/Global/Card";
import SumWalletsBalanceHistoryChart from "@/Components/Charts/SumWalletsBalanceHistoryChart";
import HiddenContent from "@/Components/Global/HiddenContent";

export default {
    props: ["snapshots"],
    components: {
        Card,
        SumWalletsBalanceHistoryChart,
        HiddenContent
    },
    data: () => {
        return {
            activeTimeFrame: "day",
            timeFrames: [
                {
                    title: "Day",
                    value: "day"
                },
                {
                    title: "Week",
                    value: "week"
                },
                {
                    title: "Month",
                    value: "month"
                }
            ]
        };
    },
    computed: {
        totalBalance() {
            return this.snapshots.length
                ? this.snapshots[this.snapshots.length - 1].balance
                : 0;
        }
    },
    watch: {},
    destroyed() {},
    mounted() {},
    methods: {
        toggleTimeframe(format) {
            this.activeTimeFrame = format;
            this.$inertia.get(
                route("wallets.index", { aggregate: format }),
                {},
                {
                    preserveScroll: true,
                    preserveState: true
                }
            );
        }
    }
};
</script>
