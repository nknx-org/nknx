<template>
    <Card
        :col="
            $mq === 'xl' ? '4' : $mq === 'llg' ? '6' : $mq === 'lg' ? '6' : '12'
        "
        padding="none"
    >
        <div class="general-stats">
            <div class="general-stats__item">
                <div class="general-stats__title">{{ $page.props.networkStats.networkNodes | commaNumber}}</div>
                <div class="general-stats__subtitle">Current Network Nodes</div>
            </div>
            <template
                v-if="
                    open === true &&
                        $mq !== 'lg' &&
                        $mq !== 'llg' &&
                        $mq !== 'xl'
                "
            >
                <div class="general-stats__divider" />
                <div class="general-stats__item">
                    <div class="general-stats__data">
                        <div
                            v-for="item in items"
                            :key="item.title"
                            class="general-stats__data-item"
                        >
                            <span
                                class="fe general-stats__data-icon"
                                :class="item.icon"
                            />
                            <div class="general-stats__data-description">
                                <div class="general-stats__data-title">
                                    {{ item.data }}
                                </div>
                                <div class="general-stats__data-subtitle">
                                    {{ item.title }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </template>

            <template v-if="$mq !== 'xs' && $mq !== 'sm' && $mq !== 'md'">
                <div class="general-stats__divider" />
                <div class="general-stats__item">
                    <div class="general-stats__data">
                        <div
                            v-for="item in items"
                            :key="item.title"
                            class="general-stats__data-item"
                        >
                            <span
                                class="fe general-stats__data-icon"
                                :class="item.icon"
                            />
                            <div class="general-stats__data-description">
                                <div class="general-stats__data-title">
                                    {{ item.data }}
                                </div>
                                <div class="general-stats__data-subtitle">
                                    {{ item.title }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
            <div
                v-if="$mq === 'xs' || $mq === 'sm' || $mq === 'md'"
                :class="[
                    'general-stats__toggle',
                    open !== true ? 'fe fe-chevron-down' : 'fe fe-chevron-up'
                ]"
                @click="toggle"
            ></div>
        </div>
    </Card>
</template>

<script>
import Card from "@/Components/Global/Card";

export default {
    components: {
        Card
    },
    props:["blockchainData"],
    data: () => {
        return {
            open: false,
            items: {
                countries: {
                    icon: "fe-globe",
                    title: "Countries",
                    data: 0
                },
                providers: {
                    icon: "fe-database",
                    title: "Addresses",
                    data: 0
                },
                blocks: {
                    icon: "fe-box",
                    title: "Blocks",
                    data: 0
                },
                transactions: {
                    icon: "fe-shuffle",
                    title: "Transactions",
                    data: 0
                }
            }
        };
    },
    destroyed() {},
    mounted() {
        this.items.countries.data = this.$page.props.networkStats.countries;
        this.items.providers.data = this.blockchainData.addressCount;
        this.items.blocks.data = this.blockchainData.blockCount;
        this.items.transactions.data = this.blockchainData.txCount;
    },
    methods: {
        toggle() {
            this.open = !this.open;
        }
    }
};
</script>
