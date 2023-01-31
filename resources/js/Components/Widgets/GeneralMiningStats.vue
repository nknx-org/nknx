<template>
    <Card :col="
        $mq === 'xl' ? '4' : $mq === 'llg' ? '6' : $mq === 'lg' ? '6' : '12'
    " padding="none">
        <div class="general-stats general-stats_gradient">
            <div class="general-stats__item">
                <div class="general-stats__title">
                    {{
                    (
    (userPerformanceData.activeNodes /
        $page.props.networkStats.networkNodes) *
    100
).toFixed(2)
                    }}%
                </div>
                <div class="general-stats__subtitle">Your Network Control</div>
            </div>
            <div class="general-stats__divider" />
            <div class="general-stats__item">
                <div class="general-stats__data">
                    <div v-for="item in items" :key="item.title" class="general-stats__data-item">
                        <span class="fe general-stats__data-icon" :class="item.icon" />
                        <div class="general-stats__data-description">
                            <div class="general-stats__data-title">
                                {{ item.data }}
                                <span v-if="item.title !== 'Active Nodes'"
                                    class="general-stats__data-symbol">Blocks</span>
                            </div>
                            <div class="general-stats__data-subtitle">
                                {{ item.title }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <Button tag="inertia-link" :href="route('nodes.index')" class="general-stats__btn" type="router"
                theme="success">View My Nodes</Button>
        </div>
    </Card>
</template>

<script>
import Card from "@/Components/Global/Card";
import Button from "@/Components/Global/Button";

export default {
    components: {
        Card,
        Button
    },
    props: ["userPerformanceData", "nodeMinedHistory"],
    data: () => {
        return {
            open: false,
            items: {
                activeNodes: {
                    icon: "fe-layers",
                    title: "Active Nodes",
                    data: 0
                },
                reward24: {
                    icon: "fe-bar-chart-2",
                    title: "Mined today",
                    data: 0
                },
                minedTotal: {
                    icon: "fe-bar-chart",
                    title: "Mined Total",
                    data: 0
                },
                minedAverage: {
                    icon: "fe-crosshair",
                    title: "Mined per Day",
                    data: 0
                }
            }
        };
    },
    destroyed() { },
    mounted() {
        this.items.activeNodes.data = this.userPerformanceData.activeNodes;

        if (this.nodeMinedHistory.length) {
            let days = 0;
            this.nodeMinedHistory.forEach(node => {
                this.items.minedTotal.data =
                    this.items.minedTotal.data + Number(node.sum);
                days++;
            });
            this.items.minedAverage.data = (
                this.items.minedTotal.data / days
            ).toFixed(2);
            this.items.reward24.data = this.nodeMinedHistory[
                this.nodeMinedHistory.length - 1
            ].sum;
        }
    },
    methods: {
        toggle() {
            this.open = !this.open;
        }
    }
};
</script>
