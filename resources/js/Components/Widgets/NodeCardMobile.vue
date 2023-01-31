<template>
    <Card col="12" :overflow="false">
        <div class="node-card-mobile">
            <div class="node-card-mobile__header">
                <div class="node-card-mobile__label">
                    <span v-if="node.label"> {{ node.label }} </span>
                    <span v-else>Node #{{ node.id }}</span>
                </div>
                <v-popover
                    offset="4"
                    :popover="{
                        defaultPlacement: 'bottom'
                    }"
                >
                    <div class="popover-more tooltip-target">
                        <svg-vue icon="more" class="popover-more-icon" />
                    </div>
                    <template slot="popover">
                        <div class="popover_actions">
                            <div class="popover__actions">
                                <div
                                    v-if="node.syncState === 'GENERATE_ID'"
                                    class="popover__actions-item"
                                    @click="
                                        $bus.emit('openGenerateIdModal', node)
                                    "
                                >
                                    Generate ID
                                </div>
                                <div
                                    class="popover__actions-item"
                                    @click="
                                        $bus.emit('openEditNodeModal', node)
                                    "
                                >
                                    Edit
                                </div>
                                <div
                                    class="popover__actions-item text_color_danger"
                                    @click="
                                        $bus.emit('openRemoveNodeModal', node)
                                    "
                                >
                                    Remove
                                </div>
                            </div>
                        </div>
                    </template>
                </v-popover>
            </div>
            <div class="node-card-mobile__body">
                <div class="node-card-mobile__addr">{{ node.addr }}</div>
                <div
                    v-clipboard:copy="node.addr"
                    class="node-card-mobile__copy"
                    @click="copyAlert"
                >
                    Copy
                </div>
            </div>
            <div class="node-card-mobile__footer">
                <NodeStatus :status="node.syncState" />
                <div class="node-card-mobile__stats">
                    <span
                        class="node-card-mobile__icon fe fe-git-branch"
                    ></span>
                    <span v-if="node.version !== null">{{
                        node.version | nodeVersion
                    }}</span>
                    <span v-else>n/a</span>
                </div>
                <div class="node-card-mobile__stats">
                    <span class="node-card-mobile__icon fe fe-gift"></span>
                    <span v-if="node.snapshots.length">{{
                        node.snapshots[0].mined
                    }}</span>
                    <span v-else>n/a</span>
                </div>
            </div>
        </div>
    </Card>
</template>

<script>
import { mapGetters } from "vuex";
import { mixin as clickaway } from "vue-clickaway";

import NodeStatus from "@/Components/Widgets/NodeStatus";
import Card from "@/Components/Global/Card.vue";

export default {
    components: { NodeStatus, Card },
    mixins: [clickaway],
    props: {
        node: {
            type: Object,
            default: () => {}
        }
    },
    data: () => {
        return {
            headings: [
                { value: "addr", title: "ipAddress" },
                { value: "node_user.label", title: "name" },
                { value: "", title: "status" },
                { value: "", title: "latestBlock" },
                { value: "", title: "currentVersion" },
                { value: "blocksMined", title: "blocksMined" },
                { value: "relayMessageCount", title: "relayedMessages" },
                { value: "", title: "miningHistory" },
                { value: "", title: "actions" }
            ],
            selected: [],
            isAll: false,
            active: "relayMessageCount",
            order: false,
            isActions: false,
            loaderCount: 10
        };
    },
    computed: {
        ...mapGetters({
            userConfig: "userNodes/getUserConfig"
        })
    },
    mounted: function() {},
    methods: {
        closeActionsModal() {
            this.isActions = false;
        },
        openDeleteNodeModal(node) {
            this.$store.dispatch("activeNode/updateActiveNode", node);
            this.$store.dispatch("modals/updateDeleteNodeModalVisible", true);
            setTimeout(this.closeActionsModal, 1);
        },
        openEditNodeModal(node) {
            this.$store.dispatch("activeNode/updateActiveNode", node);
            this.$store.dispatch("modals/updateEditNodeModalVisible", true);
            setTimeout(this.closeActionsModal, 1);
        },
        copyAlert() {
            this.$store.dispatch("snackbar/updateSnack", {
                snack: "nodeIpCopyAlert",
                color: "alert",
                timeout: true
            });
        }
    }
};
</script>
