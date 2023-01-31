<template>
    <app-layout>
        <ContentWrapper>
            <div class="page__node-manager-heading">
                <div class="page__node-manager-left">
                    <h1>My Nodes ({{ userFilters.ALL }})</h1>
                    <NodeOnline :filters="userFilters" />
                </div>
                <div class="page__node-manager-right">
                    <Button
                        v-if="$mq === 'md' || $mq === 'sm' || $mq === 'xs'"
                        class="page__node-manager-btn"
                        size="large"
                        theme="primary"
                        icon="AddIcon"
                        @click="openModal"
                    >
                        Add Nodes
                    </Button>

                    <Button
                        v-else
                        class="page__node-manager-btn"
                        size="large"
                        theme="white"
                        icon="flow-chart"
                        @click="showSummary = !showSummary"
                    >
                        <span
                            v-text="
                                showSummary ? 'Hide summary' : 'Show summary'
                            "
                        />
                    </Button>
                </div>
            </div>
            <Grid>
                <NodesInfoCard v-show="showSummary" :states="userFilters" />
                <SumNodesMiningHistory
                    v-show="showSummary"
                    :snapshots="sumNodeSnapshots"
                    :nodecount="userFilters.ALL"
                />
                <NodeSearchBar
                    v-if="$mq === 'md' || $mq === 'sm' || $mq === 'xs'"
                    :nodes="nodes"
                />
                <NodesWarning
                    v-if="userFilters.GENERATE_ID > 0"
                    class="col_12"
                    :generateIdsAmount="userFilters.GENERATE_ID"
                />
                <NodeManager
                    :nodes="nodes.data"
                    :pageData="nodes"
                    :filters="userFilters"
                    :centralMonitoring="centralMonitoring"
                    @addNodes="openModal"
                />
            </Grid>
        </ContentWrapper>

        <ModalCard
            v-show="showModal"
            title="Add Nodes"
            subtitle="Node Manager"
            descr="Here you can add your NKN nodes"
            :show.sync="showModal"
            width="720px"
        >
            <template v-slot:body>
                <Tabs :tabs="tabs">
                    <template slot="tabsContent">
                        <Tab title="Single">
                            <ControlWrapper
                                title="IP Address"
                                col="3"
                                :errorMsg="form.errors.ip"
                                showError
                            >
                                <Input
                                    v-model.trim="form.ip"
                                    :errorMsg="form.errors.ip"
                                    placeholder="Enter your node's ip here"
                                    @focus="clearErrors"
                                />
                            </ControlWrapper>
                            <ControlWrapper
                                title="Node Label"
                                col="3"
                                :errorMsg="form.errors.label"
                                showError
                            >
                                <Input
                                    v-model.trim="form.label"
                                    :errorMsg="form.errors.label"
                                    placeholder="Enter your NKN node label"
                                    @focus="clearErrors"
                                />
                            </ControlWrapper>
                        </Tab>
                        <Tab ref="multipleNodes" title="Multiple">
                            <ControlWrapper
                                title="IP Addresses (comma separated)"
                                col="6"
                                :errorMsg="form.errors.ip"
                                showError
                            >
                                <Textarea
                                    v-model.trim="form.ip"
                                    :errorMsg="form.errors.ip"
                                    placeholder="Enter your NKN wallet addresses"
                                    @focus="clearErrors"
                                />
                            </ControlWrapper>
                            <ControlWrapper
                                title="Name for Nodes (optional)"
                                col="6"
                                :errorMsg="form.errors.label"
                                showError
                            >
                                <Input
                                    v-model.trim="form.label"
                                    :errorMsg="form.errors.label"
                                    placeholder="Enter your NKN node label"
                                    @focus="clearErrors"
                                />
                            </ControlWrapper>
                        </Tab>
                    </template>
                </Tabs>
            </template>

            <template v-slot:footer>
                <Button size="large" theme="text" @click="closeModal">
                    Cancel
                </Button>
                <Button
                    size="large"
                    theme="primary"
                    :success.sync="success"
                    :loading="form.processing"
                    @click="saveNodes"
                >
                    Add
                </Button>
            </template>
        </ModalCard>
    </app-layout>
</template>

<script>
import FormMixin from "@/Mixins/FormMixin.js";
import { mixin as clickaway } from "vue-clickaway";

import AppLayout from "@/Layouts/AppLayout";
import ContentWrapper from "@/Components/Global/ContentWrapper";
import Grid from "@/Components/Global/Grid";
import Button from "@/Components/Global/Button";
import NodeOnline from "@/Components/Widgets/NodeOnline";
import NodeStats from "@/Components/Widgets/NodeStats";
import NodeFilter from "@/Components/Widgets/NodeFilter";
import NodeSearchBar from "@/Components/Widgets/NodeSearchBar";
import NodeManager from "@/Components/Widgets/NodeManager";
import ModalContent from "@/Components/Global/ModalContent";
import ModalCard from "@/Components/Global/ModalCard";
import Tabs from "@/Components/Global/Tabs";
import Tab from "@/Components/Global/Tab";
import ControlWrapper from "@/Components/Global/ControlWrapper";
import Input from "@/Components/Global/Input";
import Textarea from "@/Components/Global/Textarea";
import NodesWarning from "@/Components/Widgets/NodesWarning";
import NodesInfoCard from "@/Components/Widgets/NodesInfoCard";
import SumNodesMiningHistory from "@/Components/Widgets/SumNodesMiningHistory";

export default {
    props: ["nodes", "sums", "sumNodeSnapshots"],
    components: {
        NodeOnline,
        AppLayout,
        NodeStats,
        NodeFilter,
        NodeSearchBar,
        ContentWrapper,
        Grid,
        Button,
        NodeManager,
        ModalContent,
        ModalCard,
        Tabs,
        Tab,
        Input,
        Textarea,
        ControlWrapper,
        NodesWarning,
        NodesInfoCard,
        SumNodesMiningHistory
    },
    mixins: [clickaway, FormMixin],
    data() {
        return {
            showAddNodes: false,
            tabs: ["Single", "Multiple"],
            form: this.$inertia.form(
                {
                    ip: "",
                    label: ""
                },
                {
                    key: "NodeAdder"
                }
            ),
            isActions: false,
            success: false,
            showSummary: true
        };
    },
    computed: {
        centralMonitoring() {
            return true;
        },
        userFilters() {
            return {
                ALL: this.sums.sumall,
                PERSIST_FINISHED: this.sums.sumpersistent,
                PRUNING_DB: this.sums.sumpruning,
                GENERATE_ID: this.sums.sumgenerating,
                WAIT_FOR_SYNCING: this.sums.sumsyncing,
                SYNC_STARTED: this.sums.sumstarted,
                SYNC_FINISHED: this.sums.sumfinished,
                OFFLINE: this.sums.sumoffline
            };
        }
    },
    watch: {},
    created() {},
    mounted() {
        this.showAddNodes = true;
    },
    methods: {
        saveNodes() {
            this.form
                .transform(data => ({
                    ...data,
                    ip: this.form.ip.split(" ").join("")
                }))
                .post(route("nodes.store"), {
                    errorBag: "NodeAddBag",
                    preserveScroll: true,
                    onError: errors => {
                        this.form.ip = errors.failed;
                    },
                    onSuccess: () => {
                        this.success = true;

                        this._.delay(() => {
                            this.closeModal();
                        }, 1000);
                    }
                });
        }
    }
};
</script>
