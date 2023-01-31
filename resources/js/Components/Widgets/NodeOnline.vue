<template>
    <div class="node-online">
        <span
            :class="[
                'node-online__status',
                !offlineNodes
                    ? 'node-online__status_positive'
                    : 'node-online__status_negative'
            ]"
        ></span>
        <span v-if="!offlineNodes" class="node-online__title">
            All Nodes are mining!
        </span>
        <span v-else class="node-online__title"
            >{{ offlineNodes }} of {{ filters.ALL }} are not mining</span
        >
    </div>
</template>

<script>
export default {
    components: {},
    props: {
        filters: {
            type: Object,
            default: () => {},
            required: true
        }
    },
    data: () => {
        return {};
    },
    computed: {
        offlineNodes() {
            let offlineNodesCount = 0;

            Object.keys(this.filters).forEach(key => {
                if (
                    this.filters[key] > 0 &&
                    key !== "ALL" &&
                    key !== "PERSIST_FINISHED"
                ) {
                    offlineNodesCount = offlineNodesCount + this.filters[key];
                }
            });

            return offlineNodesCount;
        }
    },
    watch: {},
    mounted: function() {},
    destroyed() {},
    methods: {}
};
</script>
