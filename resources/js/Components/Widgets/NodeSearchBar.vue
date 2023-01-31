<template>
    <div class="node-search-bar__mobile">
        <div class="node-search-bar">
            <div class="node-search-bar__search">
                <span class="node-search-bar__icon fe fe-search"></span>
                <input
                    v-model.trim="searchInput"
                    @input="updateSearch(searchInput)"
                    class="node-search-bar__control"
                    type="text"
                    placeholder="Search all Nodes"
                />
            </div>
        </div>

        <div class="node-search-bar__mobile-pagination">
            <span class="node-search-bar__mobile-descr"
                >Showing {{ nodes.from }} to {{ nodes.to }} of
                {{ nodes.total }}</span
            >
            <Pagination
                :paginationData.sync="nodesConfig"
                routeName="nodes.index"
                :pageData="nodes"
            />
        </div>
    </div>
</template>

<script>
import { mapState, mapMutations } from "vuex";

import Pagination from "@/Components/Global/Pagination";

export default {
    components: { Pagination },
    props: ["nodes"],
    data: function() {
        return {
            searchInput: ""
        };
    },
    computed: {
        ...mapState({
            nodesConfig: state => state.nodes.nodesConfig
        })
    },
    watch: {
        "nodesConfig.search": function() {
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
            setSearch: "nodes/setSearch"
        }),
        updateSearch: _.debounce(function(str) {
            this.setSearch(str);
        }, 1500)
    }
};
</script>
