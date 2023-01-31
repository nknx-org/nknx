<template>
    <ModalCard
        v-show="showModal"
        title="Delete Wallet"
        subtitle="Wallet Tracker"
        descr="Here you can remove your NKN wallets"
        :show.sync="showModal"
    >
        <template v-slot:descr>
            Attention! You are going to remove a wallet from NKNx - are you
            sure? This change is persistent and cannot be undone. All
            history-data of this wallet will get removed, too. Following wallet
            will get deleted:
            <span class="font-bold">{{ activeWallet.address }}?</span>
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
                @click="removeWallet"
            >
                Confirm
            </Button>
        </template>
    </ModalCard>
</template>

<script>
import { mapState } from "vuex";

import FormMixin from "@/Mixins/FormMixin.js";

import Button from "@/Components/Global/Button";
import ModalContent from "@/Components/Global/ModalContent";
import ModalCard from "@/Components/Global/ModalCard";

export default {
    mixins: [FormMixin],
    components: {
        Button,
        ModalContent,
        ModalCard
    },
    data() {
        return {
            form: this.$inertia.form()
        };
    },
    computed: {
        ...mapState({
            activeWallet: state => state.wallets.activeWallet
        })
    },
    mounted() {},
    methods: {
        removeWallet() {
            this.form.delete(route("wallets.destroy", this.activeWallet.id), {
                errorBag: "WalletDestroyBag",
                preserveScroll: true,
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
