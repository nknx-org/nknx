<template>
    <Card :col="$mq === 'xl' ? '8' : '12'">
        <div class="market-stats">
            <div class="market-stats__data">
                <div class="market-stats__heading">
                    <h3 class="market-stats__title">
                        NKN Market Statistics
                    </h3>
                </div>
                <template v-if="$mq !== 'xs' && $mq !== 'sm' && $mq !== 'md'">
                    <div class="market-stats__numbers">
                        <div
                            v-for="item in items"
                            :key="item.title"
                            class="market-stats__numbers-item"
                        >
                            <div class="market-stats__numbers-name">
                                {{ item.title }}
                            </div>
                            <div
                                class="market-stats__numbers-value"
                                :class="item.class"
                            >
                                <span
                                    v-if="item.icon"
                                    class="fe market-stats__numbers-icon"
                                    :class="item.icon"
                                />
                                {{ item.data }}
                            </div>
                        </div>
                    </div>
                </template>
                <template v-else>
                    <template v-if="!full">
                        <div
                            v-if="previewItems.length > 0"
                            class="market-stats__numbers"
                        >
                            <div
                                v-for="item in previewItems"
                                :key="item.title"
                                class="market-stats__numbers-item"
                            >
                                <div class="market-stats__numbers-name">
                                    {{ item.title }}
                                </div>
                                <div
                                    class="market-stats__numbers-value"
                                    :class="item.class"
                                >
                                    <span
                                        v-if="item.icon"
                                        class="fe market-stats__numbers-icon"
                                        :class="item.icon"
                                    />
                                    {{ item.data }}
                                </div>
                            </div>
                        </div>
                    </template>
                    <template v-else>
                        <div class="market-stats__numbers">
                            <div
                                v-for="item in items"
                                :key="item.title"
                                class="market-stats__numbers-item"
                            >
                                <div class="market-stats__numbers-name">
                                    {{ item.title }}
                                </div>
                                <div
                                    class="market-stats__numbers-value"
                                    :class="item.class"
                                >
                                    <span
                                        v-if="item.icon"
                                        class="fe market-stats__numbers-icon"
                                        :class="item.icon"
                                    />
                                    {{ item.data }}
                                </div>
                            </div>
                        </div>
                    </template>
                    <div class="market-stats__toggle">
                        <div class="market-stats__toggle-divider"></div>
                        <div
                            :class="[
                                'market-stats__toggle-btn',
                                full === true
                                    ? 'fe fe-chevron-up'
                                    : 'fe fe-chevron-down'
                            ]"
                            @click="toggle"
                        ></div>
                        <div class="market-stats__toggle-divider"></div>
                    </div>
                </template>
            </div>
            <div class="market-stats__chart">
                <PriceChart :historyData="historyData"/>
            </div>
        </div>
    </Card>
</template>

<script>
import Card from "@/Components/Global/Card";
import PriceChart from "@/Components/Charts/PriceChart";

export default {
    components: {
        Card,
        PriceChart
    },
    props:["historyData"],
    data: () => {
        return {
            previewItems: [],
            full: false,
            items: {
                price: {
                    title: "Price",
                    data: 123
                },
                change24h: {
                    title: "Change 24 Hours",
                    data: 123
                },
                marketcap: {
                    title: "Marketcap",
                    data: 123
                },
                dailyVolume: {
                    title: "Daily Volume",
                    data: 123
                },
                nknUsd: {
                    title: "NKN/USD",
                    data: 123
                },
                nknEth: {
                    title: "NKN/ETH",
                    data: 123
                }
            }
        };
    },
    destroyed() {},
    mounted() {
        this.items.price.data = "$"+this.$page.props.prices.usd
        this.items.change24h.data = this.$page.props.prices.usd_24h_change.toFixed(2) + "%"
        this.items.marketcap.data = "$"+this.numberWithCommas(this.historyData.market_caps[this.historyData.market_caps.length-1][1])
        this.items.dailyVolume.data = "$"+this.numberWithCommas(this.historyData.total_volumes[this.historyData.total_volumes.length-1][1])
        this.items.nknUsd.data = this.$page.props.prices.usd
        this.items.nknEth.data = this.$page.props.prices.eth
        if (this.$page.props.prices.usd_24h_change.toFixed(2) > 0) {
          this.items.change24h.class = 'market-stats__numbers-value_positive'
          this.items.change24h.icon = 'fe-trending-up'
        } else {
          this.items.change24h.class = 'market-stats__numbers-value_negative'
          this.items.change24h.icon = 'fe-trending-down'
        }
    },
    methods: {
        toggle() {
            this.full = !this.full;
        },
        numberWithCommas(x) {
            x = x.toFixed(0);
            return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        }
    }
};
</script>
