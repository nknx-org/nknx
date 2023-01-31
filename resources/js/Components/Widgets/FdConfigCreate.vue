<template>
    <Card
        :col="
            $mq === 'xl' ? '4' : $mq === 'llg' ? '6' : $mq === 'lg' ? '6' : '12'
        "
        headerBorder
        title="Create FD Configuration"
    >
        <ModalContent>
            <template slot="body">
                <div class="col_6 max-w-xl text-sm text-gray-600">
                    Feel free to read the guide on
                    <a
                        class="text_link"
                        target="_blank"
                        href="https://medium.com/nknx/how-to-deploy-a-nkn-node-to-your-favorite-vps-with-nknx-fastdeploy-within-seconds-e38405443828"
                        >Medium</a
                    >
                </div>
                <ControlWrapper
                    title="Label"
                    col="6"
                    showError
                    :errorMsg="form.errors.label"
                >
                    <Input
                        v-model="form.label"
                        placeholder="Configuration label"
                        :errorMsg="form.errors.label"
                        @focus="clearErrors"
                    />
                </ControlWrapper>
                <ControlWrapper
                    title="Beneficiary Address"
                    col="6"
                    showError
                    :errorMsg="form.errors.beneficiary_addr"
                >
                    <Input
                        v-model="form.beneficiary_addr"
                        placeholder="Your nkn wallet address"
                        :errorMsg="form.errors.beneficiary_addr"
                        @focus="clearErrors"
                    />
                </ControlWrapper>
                <ControlWrapper title="Deployment Options" col="6">
                    <Checkbox
                        v-model="form.disable_ufw"
                        :value="form.disable_ufw"
                        title="Disable UFW?"
                    />
                    <ControlWrapper
                        title="Sync Modes"
                        col="6"
                        style="margin-bottom: 16px;"
                    >
                        <Radio
                            v-for="option in syncOptions"
                            :key="option.title"
                            class="radio_column"
                            :name="option.value"
                            :label="option.value"
                            :labelText="option.title"
                            showText
                            :value="form.sync_mode"
                            @change="changeSyncOption"
                        />
                    </ControlWrapper>
                </ControlWrapper>
            </template>
            <template slot="footer">
                <Button
                    size="large"
                    theme="primary-ghost"
                    :disabled="disabled"
                    :success.sync="success"
                    :loading="form.processing"
                    @click="submit"
                >
                    Create
                </Button>
            </template>
        </ModalContent>
    </Card>
</template>

<script>
import FormMixin from "@/Mixins/FormMixin.js";

import Card from "@/Components/Global/Card";
import Button from "@/Components/Global/Button";
import ModalContent from "@/Components/Global/ModalContent";
import ControlWrapper from "@/Components/Global/ControlWrapper";
import Input from "@/Components/Global/Input";
import Checkbox from "@/Components/Global/Checkbox";
import Radio from "@/Components/Global/Radio";

export default {
    mixins: [FormMixin],
    components: {
        Card,
        Button,
        ModalContent,
        ControlWrapper,
        Input,
        Checkbox,
        Radio
    },
    data: function() {
        return {
            form: this.$inertia.form({
                label: "",
                beneficiary_addr: "",
                sync_mode: "fast_sync",
                disable_ufw: false
            }),
            syncOptions: [
                {
                    title: "Use Fast Sync (Native fast ChainDB syncing)",
                    value: "fast_sync"
                },
                {
                    title: "Use Lite Sync (Run on ≈4GB ChainDB)",
                    value: "light_sync"
                },
                {
                    title: "Use Snapshot (Foreign fast ChainDB syncing) ",
                    value: "download_chain"
                }
            ]
        };
    },
    computed: {
        disabled() {
            if (
                this.form.label.length > 0 &&
                this.$options.filters.isNknAddress(this.form.beneficiary_addr)
            ) {
                return false;
            } else {
                return true;
            }
        }
    },
    mounted() {},
    methods: {
        submit() {
            this.form.post(route("fdConfigurations.store"), {
                preserveScroll: true,
                onSuccess: () => {
                    this.success = true;
                },
                onFinish: () => {
                    this.form.reset();
                }
            });
        },
        changeSyncOption(newValue) {
            this.form.sync_mode = newValue;
        }
    }
};
</script>
