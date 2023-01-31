<template>
    <app-layout>
        <ContentWrapper>
            <h1 class="page__title">Wallets Tracker</h1>
            <Grid>
                <SumWalletsBalanceHistory :snapshots="sumWalletSnapshots" />
                <div class="page__wallet-tracker-heading">
                    <h3 class="page__wallet-tracker-title">
                        My Wallets ({{ wallets.total }})
                    </h3>
                    <div
                        v-if="$mq !== 'md' && $mq !== 'sm' && $mq !== 'xs'"
                        class="page-navigation page__wallet-tracker-navigation"
                    >
                        <Pagination
                            class="page-navigation__pagination"
                            :paginationData.sync="paginationData"
                            routeName="wallets.index"
                            :pageData="wallets"
                        />
                    </div>
                    <Divider type="horizontal" />
                    <Button
                        v-if="wallets.data.length > 0"
                        theme="danger"
                        icon="trash"
                        width="unset"
                        size="large"
                        @click="openRemoveAllWalletsModal"
                    >
                        Delete All Wallets
                    </Button>
                </div>

                <NewWalletCard @click.native="openAddWalletsModal" />
                <template v-if="$mq === 'md' || $mq === 'sm' || $mq === 'xs'">
                    <div class="page__wallet-tracker-heading">
                        <Divider type="horizontal" />
                        <div
                            class="page-navigation page__wallet-tracker-navigation"
                        >
                            <Pagination
                                class="page-navigation__pagination"
                                :paginationData.sync="paginationData"
                                routeName="wallets.index"
                                :pageData="wallets"
                            />
                        </div>
                        <Divider type="horizontal" />
                    </div>
                </template>

                <template v-if="$mq !== 'md' && $mq !== 'sm' && $mq !== 'xs'">
                    <template v-if="!loading">
                        <WalletCard
                            v-for="wallet in wallets.data"
                            :key="wallet.id"
                            :wallet="wallet"
                        />
                    </template>
                    <template v-else>
                        <WalletCardLoader
                            v-for="(walletLoader, index) in walletLoaders"
                            :key="index"
                        />
                    </template>
                </template>

                <template v-else>
                    <div class="page__wallet-tracker-scroll">
                        <template v-if="!loading">
                            <WalletCard
                                v-for="wallet in wallets.data"
                                :key="wallet.id"
                                :wallet="wallet"
                            />
                        </template>
                        <template v-else>
                            <WalletCardLoader />
                        </template>
                    </div>
                </template>
            </Grid>
        </ContentWrapper>
    </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";
import ContentWrapper from "@/Components/Global/ContentWrapper";
import Grid from "@/Components/Global/Grid";
import Button from "@/Components/Global/Button";
import Pagination from "@/Components/Global/Pagination";
import WalletCard from "@/Components/Global/WalletCard";
import NewWalletCard from "@/Components/Global/NewWalletCard";
import WalletCardLoader from "@/Components/Loaders/WalletCardLoader";
import Divider from "@/Components/Global/Divider";
import SumWalletsBalanceHistory from "@/Components/Widgets/SumWalletsBalanceHistory";

export default {
    props: ["wallets", "sumWalletSnapshots"],
    components: {
        AppLayout,
        ContentWrapper,
        Grid,
        Button,
        Pagination,
        WalletCard,
        NewWalletCard,
        WalletCardLoader,
        SumWalletsBalanceHistory,
        Divider
    },
    data() {
        return {
            paginationData: {
                search: "",
                per_page: 10
            },
            loading: false,
            form: this.$inertia.form(
                {
                    address: "",
                    label: ""
                },
                {
                    key: "WalletAdder"
                }
            )
        };
    },
    mounted() {},
    methods: {
        openAddWalletsModal() {
            this.$bus.$emit("openAddWalletsModal");
        },
        openRemoveAllWalletsModal() {
            this.$bus.$emit("removeAllWalletsModal");
        }
    }
};
</script>
