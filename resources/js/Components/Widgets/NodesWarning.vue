<template>
    <div class="nodes-warning">
        <span class="nodes-warning__icon fe fe-alert-triangle"></span>
        <div class="nodes-warning__right">
            <div class="nodes-warning__title">
                <span class="nodes-warning__number">{{
                    generateIdsAmount
                }}</span>{{generateIdsAmount >1 ? 'nodes' : 'node'}} {{generateIdsAmount >1 ? 'require' : 'requires'}} mainnet tokens for ID generation
            </div>
            <div class="nodes-warning__text">
                Since the beginning of May 2021 the NKN network requires new nodes to pay a fee for participating. NKNx can handle the required
                <span class="font-bold">10 mainnet NKN</span> token fee and make sure your node starts mining. Just select the nodes you want to generate an ID and chose "Generate ID" in the multiselect bar. If you want to know more about our service, price calculations and the process itself you can read more about it
                <a target="_blank" href="https://medium.com/nknx/introducing-the-nknx-node-id-generation-service-17b6fc970d66" class="text_link">here</a>.
            </div>
        </div>
    </div>
</template>

<script>
export default {
    components: {},
    props: {
        generateIdsAmount: {
            type: Number,
            default: 0,
            required: true
        }
    },
    data: () => {
        return {};
    },
    computed: {},
    watch: {},
    mounted: function() {},
    destroyed() {},
    methods: {}
};
</script>
