<template>
    <ModalCard
        v-show="showModal"
        title="Delete All Wallets"
        subtitle="Wallet Tracker"
        descr="Here you can delete all your wallets from nknx tracker"
        :show.sync="showModal"
    >
        <template v-slot:descr>
            Attention! All your wallets are going to be removed from NKNx - are
            you sure? This change is persistent and cannot be undone. All
            history-data will get removed, too.
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
    computed: {},
    mounted() {},
    methods: {
        removeWallet() {
            this.form.delete(route("wallets.destroyAll"), {
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
