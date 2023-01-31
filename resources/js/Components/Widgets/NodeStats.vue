<template>
    <div class="node-stats">
        <div class="node-stats__item">
            <div
                class="node-stats__icon node-stats__icon_color_purple fe fe-layers"
            ></div>
            <div class="node-stats__data">
                <div class="node-stats__title">Monitored Nodes</div>
                <div class="node-stats__value">{{ totalNodes }}</div>
                <div class="node-stats__tooltip">
                    {{ $page.props.networkStats.networkNodes }} in Network
                </div>
            </div>
        </div>

        <!-- <div class="node-stats__item">
            <div
                class="node-stats__icon node-stats__icon_color_green fe fe-codepen"
            ></div>
            <div class="node-stats__data">
                <div class="node-stats__title">Mined today</div>
                <div class="node-stats__value">
                    {{ Number(reward24).toFixed(2) | commaNumber }}
                    <span class="node-stats__currency">NKN</span>
                </div>
                <div class="node-stats__tooltip"></div>
            </div>
        </div>

        <div class="node-stats__item">
            <div
                class="node-stats__icon node-stats__icon_color_red fe fe-credit-card"
            ></div>
            <div class="node-stats__data">
                <div class="node-stats__title">Total Mined last 3 months</div>
                <div class="node-stats__value">
                    {{ Number(total).toFixed(2) | commaNumber }}
                    <span class="node-stats__currency">NKN</span>
                </div>
            </div>
        </div> -->

        <div class="node-stats__item">
            <div
                class="node-stats__icon node-stats__icon_color_orange fe fe-dollar-sign"
            ></div>
            <div class="node-stats__data">
                <div class="node-stats__title">Estimated monthly earnings</div>
                <div class="node-stats__value">
                    ${{ (estimatedReward * totalNodes).toFixed(2) }}
                    <span class="node-stats__currency">USD</span>
                </div>
                <div class="node-stats__tooltip">
                    Estimate node/month = {{ estimatedReward.toFixed(2) }}$
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    components: {},
    props: {
        totalNodes: {
            type: Number,
            default: 0
        }
    },

    data: () => {
        return {};
    },
    computed: {
        estimatedReward() {
            const blockReward = 11.41552511;
            const blockPerMinute = 3; //In seconds
            const blocksPerMonth = blockPerMinute * 60 * 24 * 30.42;

            return (
                (blocksPerMonth * blockReward * this.$page.props.prices.usd) /
                this.$page.props.networkStats.networkNodes
            );
        }
    },
    destroyed() {},
    mounted: function() {},
    methods: {}
};
</script>
