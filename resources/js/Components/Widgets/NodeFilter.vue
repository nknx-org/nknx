<template>
    <ul class="node-filter">
        <li
            v-for="(filterCount, filterName) in filters"
            :key="filterName"
            :class="[
                'node-filter__item',
                filterName === nodesConfig.syncState
                    ? 'node-filter__item_active'
                    : null
            ]"
            @click="setSyncState(filterName)"
        >
            {{ filterName }} ({{ filterCount }})
        </li>
        <li class="node-filter__marker"></li>
    </ul>
</template>

<script>
import { mapState, mapMutations } from "vuex";

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
        ...mapState({
            nodesConfig: state => state.nodes.nodesConfig
        })
    },
    watch: {
        "nodesConfig.syncState": function() {
            this.$inertia.get(
                this.route("nodes.index", this.nodesConfig),
                {},
                { preserveScroll: true, preserveState: true }
            );
        }
    },
    destroyed() {},
    mounted: function() {},
    methods: {
        ...mapMutations({
            setSyncState: "nodes/setSyncState"
        })
    }
};
</script>
