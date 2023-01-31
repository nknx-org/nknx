<template>
    <Card
        col="12"
        padding="none"
        title="Transactions"
        :counter="`${transactions.total} in total`"
    >
        <template v-slot:headerControls>
            <Pagination
                :routeData.sync="routeData"
                :paginationData.sync="paginationData"
                routeName="wallets.show"
                :pageData="transactions"
                pageName="tx_page"
            />
            <Divider />
            <a
                :href="`https://nscan.io/addresses/${activeWallet.address}`"
                target="_blank"
                class="text_link"
                style="margin-left: 16px;"
                >View in Explorer</a
            >
        </template>
        <div class="overflow-x">
            <table class="table">
                <thead class="table__header">
                    <tr class="table__row">
                        <th class="table__title" style="width: 10%;">Block</th>
                        <th class="table__title">Hash</th>
                        <th class="table__title">Type</th>
                        <th class="table__title">Created</th>
                    </tr>
                </thead>
                <tbody class="table__body">
                    <tr
                        v-for="tx in transactions.data"
                        :key="tx.id"
                        class="table__row"
                    >
                        <td class="table__item">
                            <a
                                :href="
                                    `https://nscan.io/blocks/${tx.block_height}`
                                "
                                target="_blank"
                                class="text_link"
                                >{{ tx.block_height }}</a
                            >
                        </td>
                        <td class="table__item">
                            <a
                                :href="
                                    `https://nscan.io/transactions/${tx.hash}`
                                "
                                target="_blank"
                                class="text_link"
                                >{{ tx.hash }}</a
                            >
                        </td>
                        <td class="table__item">
                            <TransactionTypeTitle :type="tx.txType" />
                        </td>
                        <td class="table__item">
                            {{ $moment(tx.created_at + "Z").fromNow() }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </Card>
</template>

<script>
import { mapState } from "vuex";

import Card from "@/Components/Global/Card";
import Pagination from "@/Components/Global/Pagination";
import TransactionTypeTitle from "@/Components/Global/TransactionTypeTitle";
import Divider from "@/Components/Global/Divider";

export default {
    components: {
        Card,
        Pagination,
        TransactionTypeTitle,
        Divider
    },
    props: ["transactions"],
    data: () => {
        return {
            loading: false,
            paginationData: {
                search: "",
                per_page: 10
            }
        };
    },
    computed: {
        ...mapState({
            activeWallet: state => state.wallets.activeWallet
        }),
        routeData() {
            return {
                id: this.activeWallet.id
            };
        }
    },
    watch: {},
    mounted() {},
    methods: {}
};
</script>
