<template>
    <div class="node-manager">
        <template v-if="$mq !== 'md' && $mq !== 'sm' && $mq !== 'xs'">
            <div class="node-manager__topbar" :class="selected.length ? `node-manager__topbar_active` : null">
                <div class="node-manager__topbar-left">
                    {{ selected.length }} nodes selected
                </div>
                <div class="node-manager__topbar-right">
                    <div v-show="amountOfGenerateIds" class="node-manager__topbar-item" @click="showPayment = true">
                        Generate ID
                        <svg-vue class="node-manager__topbar-icon" icon="target" />
                    </div>
                    <div class="node-manager__topbar-item" @click="showMultipleRemove = true">
                        Delete
                        <svg-vue class="node-manager__topbar-icon" icon="trash" />
                    </div>
                </div>
            </div>
            <div class="node-manager__head">
                <div class="node-manager__select node-manager__select_top">
                    <div class="node-manager__select-title">Node status:</div>
                    <Select class="node-manager__select-controller" :value.sync="userFilter" item-text="title"
                        :items="filterPickers" @change="selectFilter" />
                </div>

                <div class="node-manager__head-input">
                    <input type="text" class="node-manager__head-input-controller" placeholder="Search all nodes"
                        v-model.trim="searchInput" @input="updateSearch(searchInput)" />
                    <div class="node-manager__head-input-search">
                        <svg-vue icon="search" />
                    </div>
                </div>
                <div class="node-manager__head-button" @click="$listeners.addNodes">
                    Add Nodes <svg-vue icon="plus" />
                </div>
            </div>
            <div class="node-manager__wrapper">
                <div class="node-manager__placeholder" v-if="!nodes.length">
                    <svg-vue icon="table-placeholder" class="node-manager__placeholder-icon" />
                    <div class="node-manager__placeholder-title">
                        There is no data is available yet
                    </div>
                    <div class="node-manager__placeholder-subtitle">
                        You can add nodes by clicking the button in the upper
                        right corner of the card
                    </div>
                </div>
                <table v-else>
                    <thead>
                        <th>
                            <Checkbox class="node-manager__checkbox" v-model="selectAll" :value="selectAll" />
                        </th>
                        <th v-for="heading in headings" :key="heading.title" :class="
                            nodesConfig.sort === heading.value &&
                                heading.value.length
                                ? 'node-manager__sort_active'
                                : null
                        " @click="
    heading.value.length > 0
        ? setSort(heading.value)
        : null
">
                            <span class="node-manager__sort-title">
                                {{ heading.title }}
                                <span :class="[
                                    'node-manager__sort-icon fe',
                                    nodesConfig.order === 'desc'
                                        ? 'fe-chevron-down'
                                        : 'fe-chevron-up'
                                ]"></span>
                            </span>
                        </th>
                    </thead>
                    <tbody>

                        <tr v-for="node in nodes" :key="node.id" :class="
                            node.syncState === 'OFFLINE' || node.syncState === 'INVALID_IPV6'
                                ? 'node-manager_state_offline'
                                : 'node-manager_state'
                        ">
                            <td>
                                <Checkbox class="node-manager__checkbox" v-model="selected" :value="node" />
                            </td>
                            <td class="text_color_primary">{{ node.label }}</td>
                            <td>{{ node.addr }}</td>
                            <td v-if="node.syncState == 'SYNC_STARTED' || node.syncState == 'WAIT_FOR_SYNCING'" v-text="
                                node.version !== null ? node.height + ' (' + parseFloat(node.height / $page.props.blockchainSummary.blockCount *100).toFixed(2) + '%)' : 'n/a'
                            "></td>
                            <td v-else v-text="
                                node.version !== null ? node.height : 'n/a'
                            "></td>
                            <td class="text_wrap_none" v-text="
                                node.version !== null ? node.version : 'n/a'
                            "></td>
                            <td v-text="
                                node.version !== null
                                    ? node.relayMessageCount
                                    : 'n/a'
                            "></td>
                            <td v-text="
                                node.version !== null
                                    ? node.relayPerHour
                                    : 'n/a'
                            "></td>
                            <td>
                                <template>
                                    {{
                                        node.snapshots.length
                                            ? node.snapshots[
                                                node.snapshots.length - 1
                                            ].mined
                                            : 0
                                    }}
                                </template>
                            </td>
                            <td>
                                <NodeStatus :status="node.syncState" :walletAddress="node.walletAddress" />
                            </td>
                            <td>
                                <v-popover @click.native.stop="setActiveNode(node)" offset="4" :popover="{
                                    defaultPlacement: 'bottom'
                                }">
                                    <div class="popover-more popover-more_horizontal tooltip-target">
                                        <svg-vue icon="more-horizontal"
                                            class="popover-more-icon popover-more-icon_horizontal" />
                                    </div>
                                    <template slot="popover">
                                        <div class="popover_actions">
                                            <div class="popover__actions">
                                                <div v-if="
                                                    node.syncState ===
                                                    'GENERATE_ID'
                                                " class="popover__actions-item" @click="
    openGenerateIdModal(
        node
    )
">
                                                    Generate ID
                                                </div>
                                                <div class="popover__actions-item" @click="
                                                    openEditNodeModal(node)
                                                ">
                                                    Edit
                                                </div>
                                                <div class="popover__actions-item text_color_danger" @click="
                                                    openRemoveNodeModal(
                                                        node
                                                    )
                                                ">
                                                    Remove
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                </v-popover>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div v-if="centralMonitoring" class="node-manager__footer">
                <div class="node-manager__select node-manager__select_bottom">
                    <div class="node-manager__select-title">
                        Nodes per page:
                    </div>
                    <Select class="node-manager__select-controller node-manager__footer-select"
                        :value.sync="userPerPage" :items="perPagePickers" @change="selectPerPage" position="top" />
                </div>
                <div class="node-manager__footer-info">
                    Showing {{ pageData.from }}-{{ pageData.to }} of
                    {{ pageData.total }}
                </div>
                <div class="node-manager__select"
                    style="justify-content: flex-end; grid-template-columns: max-content 60px;">
                    <div class="node-manager__select-title">
                        Current page:
                    </div>
                    <input class="node-manager__footer-input" placeholder="Jump to page" type="number"
                        v-model.trim="setPageInput" />
                </div>
                <Pagination class="node-manager__footer-pagination" paginationIcon="pagination-right"
                    paginationType="fill" :paginationData.sync="nodesConfig" routeName="nodes.index"
                    :pageData="pageData" />
            </div>
        </template>
        <template v-else>
            <NodeCardMobile class="node-manager__mobile-card" v-for="node in nodes" :key="node.addr" :node="node" />
        </template>
        <ModalCard v-show="showRemove" :show.sync="showRemove" title="Delete Node" subtitle="Node Manager"
            descr="You are going to remove node from nknx.">
            <template v-slot:descr>
                Attention! You are going to remove a node from NKNx - are you
                sure? Following node will get deleted:
                <b class="font-semibold">{{ activeNode.addr }}</b>?
            </template>

            <template v-slot:footer>
                <Button size="large" theme="text" @click="showRemove = false">
                    Close
                </Button>
                <Button size="large" theme="primary" :success.sync="success" :loading="deleteNodeForm.processing"
                    @click="deleteNode">
                    Confirm
                </Button>
            </template>
        </ModalCard>

        <ModalCard v-show="showMultipleRemove" :show.sync="showMultipleRemove" title="Delete Nodes"
            subtitle="Node Manager" descr="You are going to remove multiple nodes from nknx.">
            <template v-slot:descr>
                Attention! You are going to remove multiple nodes from NKNx -
                are you sure? Following nodes will get deleted:
                <b class="font-semibold">{{
                    selected.map(x => x.addr).join(", ")
                }}</b>?
            </template>

            <template v-slot:footer>
                <Button size="large" theme="text" @click="showMultipleRemove = false">
                    Close
                </Button>
                <Button size="large" theme="primary" :success.sync="success"
                    :loading="deleteMultipleNodesForm.processing" @click="deleteMultipleNodes">
                    Confirm
                </Button>
            </template>
        </ModalCard>

        <ModalCard v-show="showEdit" :show.sync="showEdit" :title="`Edit Node ${activeNode.addr}`"
            subtitle="Node Manager" descr="You are going to edit your wallet">
            <template v-slot:body>
                <ControlWrapper title="Label" col="6" :errorMsg="editNodeForm.errors.label" showError>
                    <Input v-model="editNodeForm.label" :errorMsg="editNodeForm.errors.label" placeholder="My node #123"
                        @focus="clearErrors" />
                </ControlWrapper>
            </template>

            <template v-slot:footer>
                <Button size="large" theme="text" @click="showEdit = false">
                    Close
                </Button>
                <Button size="large" theme="primary" :success.sync="success" :loading="editNodeForm.processing"
                    @click="editNode">
                    Confirm
                </Button>
            </template>
        </ModalCard>

        <ModalCard v-show="showPayment" :show.sync="showPayment" title="NKNx ID generation service"
            subtitle="Node Manager" width="920px" :overflow="false">
            <template v-slot:body>
                <Tabs :tabs="tabs" :activeTab.sync="activeTab">
                    <template slot="tabsContent">
                        <Tab title="NKNx Pay">
                            <div class="node-manager__payment">
                                <div>
                                    <div class="node-manager__payment-summary">
                                        <div class="text-base font-bold mb-4">
                                            Payment info
                                        </div>
                                        <div class="flex justify-between mb-2">
                                            <div>
                                                Number of IDs to generate
                                            </div>
                                            <div class="text-right">
                                                {{ amountOfGenerateIds }}
                                            </div>
                                        </div>
                                        <div class="flex justify-between mb-2">
                                            <div>
                                                Base price
                                            </div>
                                            <div class="text-right">
                                                10.01 NKN ~ ${{
                                                    basePrice.toFixed(2)
                                                }}
                                            </div>
                                        </div>
                                        <div class="flex justify-between mb-2" style="vertical-align: middle;">
                                            <div>
                                                Transaction Fee
                                            </div>
                                            <div class="text-right">
                                                {{ nknxFeesPercent }}% ~ ${{
                                                (
    (nknxFeesPercent /
        100) *
    basePrice
).toFixed(2)
                                                }}
                                            </div>
                                        </div>

                                        <Divider type="horizontal" style="margin:0;" class="mt-4 mb-4" />
                                        <div class="text-base flex justify-between font-bold">
                                            <div>Total</div>
                                            <div class="text-right">
                                                ${{ totalPaymentAmount }}
                                            </div>
                                        </div>
                                    </div>
                                    <Warning class="mt-3">Notice: NKN price will update in
                                        {{ updateCounter }} sec</Warning>
                                </div>
                                <div class="nodes-warning col_12 mt-4">
                                    <span class="nodes-warning__icon fe fe-alert-triangle"></span>
                                    <div class="nodes-warning__right">
                                        <div class="nodes-warning__title">
                                            Warning: The Node ID Generation
                                            Service will immediately start after
                                            <span class="font-bold">2 network confirmations</span>
                                        </div>
                                        <div class="nodes-warning__text">
                                            Please make sure that you send your
                                            crypto payments with the according
                                            fees that fit your demand. <br />Our
                                            crypto payment system will
                                            automatically tell you when a
                                            transaction was successful.
                                            Additionally we will send you a
                                            receipt of payment via email when
                                            the required confirmations are
                                            reached and the Node ID Generation
                                            Service started.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </Tab>
                        <Tab title="Manual" class="col_6">
                            <div class="text-sm col_6 mb-3">
                                For a successful ID generation, you need to send
                                <b class="text_weight_bold">10 mainnet NKN tokens</b>
                                to the nodes wallet address.
                            </div>
                            <table class="col_6">
                                <thead>
                                    <th>
                                        Name
                                    </th>
                                    <th>
                                        IP Address
                                    </th>
                                    <th>
                                        Status
                                    </th>
                                    <th>
                                        Payment Address
                                    </th>
                                </thead>
                                <tbody>
                                    <tr v-for="generateNode in generateIdNodes" :key="generateNode.id">
                                        <td class="text_color_primary">
                                            {{ generateNode.label }}
                                        </td>

                                        <td>
                                            {{ generateNode.addr }}
                                        </td>

                                        <td>
                                            <NodeStatus :status="generateNode.syncState" />
                                        </td>

                                        <td v-clipboard:copy="
                                            generateNode.walletAddress
                                        " @click="copyNotify" class="copy-text">
                                            <svg-vue style="position: relative;top: 4px;margin-right: 2px;"
                                                class="copy__icon" icon="copy"></svg-vue>
                                            {{ generateNode.walletAddress }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </Tab>
                    </template>
                </Tabs>
            </template>

            <template v-slot:footer>
                <Button size="large" theme="text" @click="showPayment = false">
                    Close
                </Button>
                <Button v-show="activeTab === 'NKNx Pay'" size="large" theme="primary" :success.sync="paySuccess"
                    :loading="payLoading" :disabled="false" @click="purchase">
                    Pay and Generate ID
                </Button>
            </template>
        </ModalCard>
    </div>
</template>

<script>
import FormMixin from "@/Mixins/FormMixin.js";
import Countries from "@/Mixins/Countries.js";

import { mapState, mapMutations } from "vuex";
import { mixin as clickaway } from "vue-clickaway";
import { StripeElements, StripeElement } from "vue-stripe-elements-plus";

import NodeStatus from "@/Components/Widgets/NodeStatus";
import NodeCardMobile from "@/Components/Widgets/NodeCardMobile";

import ModalContent from "@/Components/Global/ModalContent";
import ControlWrapper from "@/Components/Global/ControlWrapper";
import ModalCard from "@/Components/Global/ModalCard";
import Button from "@/Components/Global/Button";
import Input from "@/Components/Global/Input";
import Radio from "@/Components/Global/Radio";
import Select from "@/Components/Global/Select";
import Divider from "@/Components/Global/Divider";
import Warning from "@/Components/Global/Warning";
import Checkbox from "@/Components/Global/Checkbox";
import Pagination from "@/Components/Global/Pagination";
import Tabs from "@/Components/Global/Tabs";
import Tab from "@/Components/Global/Tab";

export default {
    components: {
        NodeStatus,
        ModalContent,
        ControlWrapper,
        ModalCard,
        Button,
        Input,
        StripeElements,
        StripeElement,
        Radio,
        Select,
        Divider,
        Warning,
        Checkbox,
        Pagination,
        NodeCardMobile,
        Tabs,
        Tab
    },
    mixins: [clickaway, FormMixin, Countries],
    props: ["nodes", "filters", "pageData", "centralMonitoring","blockchainSummary"],
    data: function () {
        return {
            tabs: ["NKNx Pay", "Manual"],
            activeTab: "NKNx Pay",
            searchInput: "",
            userPerPage: 10,
            perPagePickers: [10, 50, 100, 250, 500],
            userFilter: {
                title: "ALL (0)",
                value: "ALL"
            },
            headings: [
                { value: "label", title: "Name" },
                { value: "addr", title: "IP Address" },
                { value: "height", title: "Latest Block" },
                { value: "version", title: "Version" },
                { value: "relayMessageCount", title: "# Relayed" },
                { value: "relayPerHour", title: "Relay/hour" },
                { value: "", title: "# Mined Today" },
                { value: "syncState", title: "Status" },
                { value: "", title: "" }
            ],
            activeNode: {},
            selected: [],
            showRemove: false,
            showMultipleRemove: false,
            deleteNodeForm: this.$inertia.form(),
            deleteMultipleNodesForm: this.$inertia.form(),
            editNodeForm: this.$inertia.form({
                label: ""
            }),
            showEdit: false,
            showPayment: false,
            updateCounter: 30,
            nknPrice: 0,
            payLoading: false,
            paySuccess: false,
            paid: false
        };
    },
    computed: {
        ...mapState({
            nodesConfig: state => state.nodes.nodesConfig
        }),
        setPageInput: {
            get() {
                return this.nodesConfig.page;
            },
            set(newVal) {
                this.setPage(newVal);
            }
        },
        selectAll: {
            get() {
                return this.selected.length === this.nodes.length;
            },
            set(newVal) {
                this.selected = [];

                if (newVal) {
                    this._.each(this.nodes, x => {
                        this.selected.push(x);
                    });
                }
            }
        },
        generateIdNodes() {
            return this.selected.filter(x => x.syncState === "GENERATE_ID");
        },
        amountOfGenerateIds() {
            return this.generateIdNodes.length;
        },
        filterPickers() {
            const pickers = [];
            this._.forIn(this.filters, (k, v) => {
                pickers.push({
                    title: `${v} (${k}) `,
                    value: v
                });
            });

            return pickers;
        },
        nknxFeesPercent() {
            return 4;
        },
        basePrice() {
            return this.nknPrice * 10 * this.amountOfGenerateIds;
        },
        totalPaymentAmount() {
            return (
                Math.round(
                    (this.basePrice +
                        (this.basePrice * this.nknxFeesPercent) / 100) *
                    100
                ) / 100
            );
        }
    },
    watch: {
        filters: {
            deep: true,
            handler() {
                this.setUserFilter(this.userFilter.value);
            }
        },
        "nodesConfig.sort": function () {
            this.fetchNodes();
        },
        "nodesConfig.page": function () {
            this.fetchNodes();
        },
        "nodesConfig.order": function () {
            this.fetchNodes();
        },
        "nodesConfig.search": function () {
            this.$inertia.get(
                this.route("nodes.index", this.nodesConfig),
                {},
                { preserveScroll: true, preserveState: true }
            );
        },
        "nodesConfig.syncState": function () {
            this.$inertia.get(
                this.route("nodes.index", this.nodesConfig),
                {},
                { preserveScroll: true, preserveState: true }
            );
        },
        "nodesConfig.per_page": function () {
            this.$inertia.get(
                this.route("nodes.index", this.nodesConfig),
                {},
                { preserveScroll: true, preserveState: true }
            );
        }
    },
    mounted: function () {
        this.$bus.$on("openEditNodeModal", this.openEditNodeModal);
        this.$bus.$on("openRemoveNodeModal", this.openRemoveNodeModal);
        this.$bus.$on("openGenerateIdModal", this.openGenerateIdModal);

        this.updatePrice();

        this.$nextTick(() => {
            this.setUserFilter("ALL");
            this.setSyncState(this.userFilter.value);
        });

        this.counterInterval = setInterval(() => {
            if (this.updateCounter > 0) {
                this.updateCounter -= 1;
            } else {
                this.updateCounter = 30;
                this.updatePrice();
            }
        }, 1000);
    },
    beforeDestroy() {
        this.$bus.$off("openEditNodeModal", this.openEditNodeModal);
        this.$bus.$off("openRemoveNodeModal", this.openRemoveNodeModal);
        this.$bus.$off("openGenerateIdModal", this.openGenerateIdModal);
    },
    destroyed() {
        clearInterval(this.counterInterval);
    },
    methods: {
        ...mapMutations({
            setSearch: "nodes/setSearch",
            setSort: "nodes/setSort",
            setPage: "nodes/setPage",
            setSyncState: "nodes/setSyncState",
            setPerPage: "nodes/setPerPage"
        }),
        updateSearch: _.debounce(function (str) {
            this.setSearch(str);
        }, 1500),
        setUserFilter(name) {
            this.userFilter = this.filterPickers.find(
                filter => filter.value === name
            );
        },
        copyNotify() {
            this.$notify({
                group: "notifications",
                text: "Payment address copied to clipboard",
                type: "success"
            });
        },
        openRemoveNodeModal(node) {
            this.setActiveNode(node);
            this.showRemove = true;
        },
        openGenerateIdModal(node) {
            this.selected = [node];
            this.showPayment = true;
        },
        setActiveNode(node) {
            this.activeNode = node;
        },
        selectFilter(filter) {
            this.setSyncState(filter.value);
        },
        selectPerPage(num) {
            this.setPerPage(num);
        },
        updatePrice() {
            this.axios
                .get(
                    "https://api.coingecko.com/api/v3/simple/token_price/ethereum?contract_addresses=0x5cf04716ba20127f1e2297addcf4b5035000c9eb&vs_currencies=usd"
                )
                .then(
                    resp =>
                    (this.nknPrice =
                        resp.data[
                            "0x5cf04716ba20127f1e2297addcf4b5035000c9eb"
                        ].usd)
                )
                .catch(err => console.log(err));
        },
        purchase() {
            this.payLoading = true;
            this.axios
                .post("/purchase", {
                    nkn_price: this.nknPrice,
                    node_ids: this.generateIdNodes.map(x => x.id)
                })
                .then(resp => {
                    this.payLoading = false;
                    this.showPayment = false;

                    window.btcpay.showInvoice(resp.data);
                    window.btcpay.onModalReceiveMessage(resp => {
                        if (resp.data.status === "paid") {
                            this.paid = true;

                            this._.delay(() => {
                                this.$inertia.reload();
                            }, 2000);
                        }
                    });
                    window.btcpay.onModalWillLeave(() => {
                        this.selected = [];

                        if (this.paid) {
                            this.$notify({
                                group: "notifications",
                                text:
                                    "Thank you for your payment - Node ID generation will start after 2 tx confirmations!",
                                type: "success"
                            });
                        } else {
                            this.$notify({
                                group: "notifications",
                                text: "Your payment was canceled!",
                                type: "error"
                            });
                        }
                    });
                })
                .catch(err => {
                    this.payLoading = false;
                    this.$notify({
                        group: "notifications",
                        text: "Error on fetching invoice",
                        type: "error"
                    });
                });
        },
        fetchNodes() {
            this.$inertia.get(
                this.route("nodes.index", this.nodesConfig),
                {},
                { preserveScroll: true, preserveState: true }
            );
        },
        openEditNodeModal(node) {
            this.activeNode = node;
            this.editNodeForm.label = node.label;
            this.showEdit = true;
        },
        deleteNode() {
            this.deleteNodeForm.delete(
                route("nodes.destroy", this.activeNode),
                {
                    preserveScroll: true,
                    preserveState: true,
                    onSuccess: () => {
                        this.success = true;
                        this._.delay(() => {
                            this.showRemove = false;
                            this.activeNode = {};
                        }, 1000);
                    }
                }
            );
        },
        deleteMultipleNodes() {
            this.deleteMultipleNodesForm
                .transform(() => ({
                    ips: this.selected.map(x => x.id)
                }))
                .delete(route("nodes.destroyAll", this.activeNode), {
                    preserveScroll: true,
                    preserveState: true,
                    onSuccess: () => {
                        this.success = true;
                        this._.delay(() => {
                            this.showMultipleRemove = false;
                            this.selected = [];
                        }, 1000);
                    }
                });
        },
        editNode() {
            this.editNodeForm.put(route("nodes.update", this.activeNode), {
                preserveScroll: true,
                preserveState: true,
                onSuccess: () => {
                    this._.delay(() => {
                        this.showEdit = false;
                        this.activeNode = {};
                    }, 1000);
                }
            });
        }
    }
};
</script>
