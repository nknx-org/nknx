<template>
    <Card
        :col="
            $mq === 'xl' ? '4' : $mq === 'llg' ? '6' : $mq === 'lg' ? '6' : '12'
        "
        :custom="true"
        title="Node Status"
        class="nodes-info-card"
    >
        <NodeStatusChart class="nodes-info-card__chart" :states="states" />
        <div class="nodes-info-card__footer">
            <div class="stats-item__wrapper">
                <div class="stats-item">
                    <div class="stats-item__title">Network nodes</div>
                    <div class="stats-item__value">
                        {{
                            $page.props.networkStats.networkNodes | commaNumber
                        }}
                    </div>
                </div>

                <div class="stats-item">
                    <div class="stats-item__title">Network version</div>
                    <div class="stats-item__value">
                        {{
                            $page.props.networkStatus.networkVersion
                                | nodeVersion
                        }}
                    </div>
                </div>
            </div>
        </div>
    </Card>
</template>

<script>
import Card from "@/Components/Global/Card";
import NodeStatusChart from "@/Components/Charts/NodeStatusChart";

export default {
    props: ["states"],
    components: {
        Card,
        NodeStatusChart
    },
    data: function() {
        return {};
    },
    computed: {},
    mounted() {},
    methods: {}
};
</script>
