<template>
    <div>
        <Headerbar v-if="$mq === 'xs' || $mq === 'sm' || $mq === 'md'" />
        <Sidebar />
        <div class="content">
            <slot></slot>
        </div>
        <Snackbar />
        <AddWalletsModal openModalEvent="openAddWalletsModal" />
        <LogoutSessionsModal openModalEvent="openLogoutSessionsModal" />
        <RemoveWalletModal openModalEvent="removeWalletModal" />
        <RemoveAllWalletsModal openModalEvent="removeAllWalletsModal" />
        <WalletQrModal openModalEvent="openWalletQrModal" />
        <TwoFactorAuthModal openModalEvent="openTwoFactorAuthModal" />
        <DeleteAccountModal openModalEvent="openDeleteAccountConfirmModal" />
        <FastDeployModal openModalEvent="openFastDeployModal" />
        <CustomFastDeployModal openModalEvent="openCustomFastDeployModal" />
    </div>
</template>

<script>
import { mapGetters } from "vuex";

import Headerbar from "@/Components/Global/Headerbar";
import Sidebar from "@/Components/Global/Sidebar";
import Snackbar from "@/Components/Global/Snackbar";
import AddWalletsModal from "@/Components/Modals/AddWalletsModal";
import LogoutSessionsModal from "@/Components/Modals/LogoutSessionsModal";
import RemoveWalletModal from "@/Components/Modals/RemoveWalletModal";
import RemoveAllWalletsModal from "@/Components/Modals/RemoveAllWalletsModal";
import WalletQrModal from "@/Components/Modals/WalletQrModal";
import TwoFactorAuthModal from "@/Components/Modals/TwoFactorAuthModal";
import DeleteAccountModal from "@/Components/Modals/DeleteAccountModal";
import FastDeployModal from "@/Components/Modals/FastDeployModal";
import CustomFastDeployModal from "@/Components/Modals/CustomFastDeployModal";

export default {
    components: {
        Headerbar,
        Sidebar,
        Snackbar,
        AddWalletsModal,
        LogoutSessionsModal,
        RemoveWalletModal,
        RemoveAllWalletsModal,
        WalletQrModal,
        TwoFactorAuthModal,
        DeleteAccountModal,
        FastDeployModal,
        CustomFastDeployModal
    },

    data: () => ({}),
    computed: {
        ...mapGetters({
            scrollOverflow: "scrollOverflow/get"
        }),
        isHidden() {
            return route().current() === "profile.show";
        }
    },
    watch: {
        scrollOverflow() {
            document.documentElement.classList.toggle("no-scroll");
        },
        "$page.props.flash": {
            deep: true,
            handler(newVal, oldVal) {
                if (newVal.error === null && newVal.success === null) return;

                this.$notify({
                    group: "notifications",
                    text: newVal.error ?? newVal.success,
                    type: newVal.error ? "error" : "success"
                });
            }
        }
    },
    methods: {},
    mounted() {}
};
</script>
