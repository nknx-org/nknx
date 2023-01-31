<template>
    <div class="wallet-side">
        <div class="wallet-side__top">
            <div class="wallet-side__header">
                <div class="wallet-side__title">My Wallets</div>
                <Pagination
                    class="wallet-side__pagination"
                    :routeData.sync="routeData"
                    :paginationData.sync="paginationData"
                    routeName="wallets.show"
                    :pageData="wallets"
                />
                <div
                    class="wallet-side__new fe fe-plus"
                    @click="openAddWalletsModal"
                ></div>
            </div>
            <WalletPreview
                v-for="item in wallets.data"
                :key="item.id"
                :wallet="item"
            />
        </div>
        <div class="wallet-side__footer"></div>
    </div>
</template>

<script>
import { mapState } from "vuex";

import Pagination from "@/Components/Global/Pagination";
import WalletPreview from "@/Components/Global/WalletPreview";

export default {
    props: ["wallets"],
    components: { Pagination, WalletPreview },
    data: () => {
        return {};
    },
    watch: {},
    computed: {
        ...mapState({
            wallet: state => state.wallets.activeWallet
        }),
        routeData() {
            return {
                id: this.wallet.id
            };
        },
        paginationData() {
            return {
                search: "",
                per_page: 5
            };
        }
    },
    destroyed() {},
    created() {},
    mounted: function() {},
    methods: {
        openAddWalletsModal() {
            this.$bus.$emit("openAddWalletsModal");
        }
    }
};
</script>
