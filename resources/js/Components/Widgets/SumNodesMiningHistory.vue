<template>
    <Card
        class="balance-card"
        :col="
            $mq === 'xl'
                ? '8'
                : $mq === 'llg'
                ? '12'
                : $mq === 'lg'
                ? '12'
                : '12'
        "
        :custom="true"
        title="Mining History (blocks)"
    >
        <div class="balance-card__header">
            <div class="stats-item__wrapper stats-item__wrapper_full">
                <div class="stats-item">
                    <div class="stats-item__title">Total blocks</div>
                    <div class="stats-item__value">
                        {{ totalMinedBlocks | commaNumber }}
                    </div>
                </div>
                <div class="stats-item">
                    <div class="stats-item__title">Total reward</div>
                    <div class="stats-item__value">
                        ${{
                            (
                                (totalMinedAmount * $page.props.prices.usd) /
                                100000000
                            ).toFixed(2)
                        }}
                        <span class="stats-item__index">USD</span>
                    </div>
                </div>
                <div class="stats-item">
                    <div class="stats-item__title">
                        Avg reward
                    </div>
                    <div class="stats-item__value">
                        {{ (avgMinedAmount / 100000000).toFixed(2) }}
                        <span class="stats-item__index">NKN</span>
                    </div>
                </div>
                <div class="stats-item">
                    <div class="stats-item__title">
                        Est. earnings
                    </div>
                    <div class="stats-item__value">
                        ${{ estimatedReward.toFixed(2) }}
                        <span class="stats-item__index">USD</span>
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

        <SumNodesMiningHistoryChart
            :snapshots="snapshots"
            class="balance-card__chart_full"
        />
    </Card>
</template>

<script>
import Card from "@/Components/Global/Card";
import SumNodesMiningHistoryChart from "@/Components/Charts/SumNodesMiningHistoryChart";
import HiddenContent from "@/Components/Global/HiddenContent";

export default {
    props: ["snapshots", "nodecount"],
    components: {
        Card,
        SumNodesMiningHistoryChart,
        HiddenContent
    },
    data: () => {
        return {
            activeTimeFrame: "month",
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
        estimatedReward() {
            const blockReward = 11.41552511;
            const blockPerMinute = 3; //In seconds

            let blocksPerTimeframe = 0;

            switch (this.activeTimeFrame) {
                case "day":
                    blocksPerTimeframe = blockPerMinute * 60 * 24;
                    break;
                case "week":
                    blocksPerTimeframe = blockPerMinute * 60 * 24 * 7;
                    break;
                case "month":
                    blocksPerTimeframe = blockPerMinute * 60 * 24 * 30.42;
                    break;
            }

            return (
                (blocksPerTimeframe *
                    blockReward *
                    this.$page.props.prices.usd *
                    this.nodecount) /
                this.$page.props.networkStats.networkNodes
            );
        },
        totalMinedAmount() {
            return (
                this._.sumBy(this.snapshots, x =>
                    this._.toNumber(x.sum_mined_amount)
                ) ?? 0
            );
        },
        avgMinedAmount() {
            return (
                this._.meanBy(this.snapshots, x =>
                    this._.toNumber(x.sum_mined_amount)
                ) ?? 0
            );
        },
        totalMinedBlocks() {
            return (
                this._.sumBy(this.snapshots, x =>
                    this._.toNumber(x.sum_mined)
                ) ?? 0
            );
        },
        avgMinedBlocks() {
            return (
                this._.meanBy(this.snapshots, x =>
                    this._.toNumber(x.sum_mined)
                ) ?? 0
            );
        }
    },
    watch: {},
    destroyed() {},
    mounted() {},
    methods: {
        toggleTimeframe(format) {
            this.activeTimeFrame = format;
            this.$inertia.get(
                route("nodes.index", { aggregate: format }),
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
