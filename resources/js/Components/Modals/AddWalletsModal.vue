<template>
    <ModalCard
        v-show="showModal"
        title="Add Wallet"
        subtitle="Wallet Tracker"
        descr="Here you can add your NKN wallets"
        :show.sync="showModal"
    >
        <template v-slot:body>
            <Tabs :tabs="tabs">
                <template slot="tabsContent">
                    <Tab title="Single">
                        <ControlWrapper
                            title="Wallet Address"
                            col="3"
                            :errorMsg="form.errors.address"
                            showError
                        >
                            <Input
                                v-model.trim="form.address"
                                :errorMsg="form.errors.address"
                                placeholder="Enter your NKN wallet address"
                                @focus="clearErrors"
                            />
                        </ControlWrapper>
                        <ControlWrapper
                            title="Wallet Label"
                            col="3"
                            :errorMsg="form.errors.label"
                            showError
                        >
                            <Input
                                v-model.trim="form.label"
                                :errorMsg="form.errors.label"
                                placeholder="Enter your NKN wallet label"
                                @focus="clearErrors"
                            />
                        </ControlWrapper>
                    </Tab>
                    <Tab ref="multipleWallets" title="Multiple">
                        <ControlWrapper
                            title="Wallet Addresses (comma separated)"
                            col="6"
                            :errorMsg="form.errors.address"
                            showError
                        >
                            <Textarea
                                v-model.trim="form.address"
                                :errorMsg="form.errors.address"
                                placeholder="Enter your NKN wallet addresses"
                                @focus="clearErrors"
                            />
                        </ControlWrapper>
                        <ControlWrapper
                            title="Name for Wallets (optional)"
                            col="6"
                            :errorMsg="form.errors.label"
                            showError
                        >
                            <Input
                                v-model.trim="form.label"
                                :errorMsg="form.errors.label"
                                placeholder="Enter your NKN wallets label"
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
                @click="saveWallets"
            >
                Add
            </Button>
        </template>
    </ModalCard>
</template>

<script>
import FormMixin from "@/Mixins/FormMixin.js";

import Button from "@/Components/Global/Button";
import ModalContent from "@/Components/Global/ModalContent";
import ModalCard from "@/Components/Global/ModalCard";
import ControlWrapper from "@/Components/Global/ControlWrapper";
import Input from "@/Components/Global/Input";
import Textarea from "@/Components/Global/Textarea";
import Tabs from "@/Components/Global/Tabs";
import Tab from "@/Components/Global/Tab";

export default {
    mixins: [FormMixin],
    components: {
        Button,
        ModalContent,
        ModalCard,
        ControlWrapper,
        Input,
        Textarea,
        Tabs,
        Tab
    },
    data() {
        return {
            tabs: ["Single", "Multiple"],
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
    computed: {},
    methods: {
        saveWallets() {
            this.form.post(route("wallets.store"), {
                errorBag: "WalletAddBag",
                preserveScroll: true,
                onError: errors => {
                    this.form.address = errors.failed;
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
