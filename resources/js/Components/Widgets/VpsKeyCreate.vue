<template>
    <Card :col="
        $mq === 'xl' ? '4' : $mq === 'llg' ? '6' : $mq === 'lg' ? '6' : '12'
    " headerBorder title="New VPS Key">
        <ModalContent>
            <template slot="body">
                <div class="col_6 max-w-xl text-sm text-gray-600">
                    VPS Keys allows NKNx deploy your nodes automatically via
                    FastDeploy.
                </div>
                <ControlWrapper title="Profile List" col="6" showError :errorMsg="form.errors.provider">
                    <Select :value.sync="form.provider" :items="providerPickers" />
                </ControlWrapper>
                <ControlWrapper title="Profile Name" col="6" showError :errorMsg="form.errors.profile_name">
                    <Input v-model="form.profile_name" placeholder="Personal" :errorMsg="form.errors.profile_name"
                        @focus="clearErrors" />
                </ControlWrapper>
                <ControlWrapper title="API Token" col="6" showError :errorMsg="form.errors.api_token">
                    <Input v-model="form.api_token" :placeholder="`Enter your ${form.provider} API Token`"
                        :errorMsg="form.errors.api_token" @focus="clearErrors" />
                </ControlWrapper>
                <ControlWrapper v-show="form.provider === 'AWS'" title="API Secret" col="6" showError
                    :errorMsg="form.errors.api_secret">
                    <Input v-model="form.api_secret" :placeholder="
                        `Enter your ${form.provider} API secret key`
                    " :errorMsg="form.errors.api_secret" @focus="clearErrors" />
                </ControlWrapper>

            </template>
            <template slot="footer">
                <Button size="large" theme="primary-ghost" :disabled="disabled" :success.sync="success"
                    :loading="form.processing" @click="submit">
                    Add
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
import ModalCard from "@/Components/Global/ModalCard";
import Select from "@/Components/Global/Select";

export default {
    props: [],
    mixins: [FormMixin],
    components: {
        Card,
        Button,
        ModalContent,
        ControlWrapper,
        Input,
        Checkbox,
        ModalCard,
        Select
    },
    data: function () {
        return {
            providerPickers: ["DigitalOcean", "AWS", "Vultr", "Hetzner"],
            regionPickers: [
                "eu-central-1",
                "us-east-1",
                "us-east-2",
                "eu-north-1",
                "ap-south-1",
                "eu-west-3",
                "eu-west-2",
                "eu-west-1",
                "ap-northeast-2",
                "ap-northeast-1",
                "sa-east-1",
                "ca-central-1",
                "ap-southeast-1",
                "ap-southeast-2",
                "us-west-1",
                "us-west-2"
            ],
            form: this.$inertia.form({
                provider: "DigitalOcean",
                profile_name: "",
                api_token: "",
                api_secret: "",
                region: ""
            })
        };
    },
    computed: {
        disabled() {
            const awsValid =
                this.form.provider === "AWS"
                    ? this.form.api_secret.length > 0 : true;

            if (
                this.form.provider.length > 0 &&
                this.form.profile_name.length > 0 &&
                this.form.api_token.length > 0 &&
                awsValid
            ) {
                return false;
            } else {
                return true;
            }
        }
    },
    mounted() {
        console.log(route());
    },
    methods: {
        submit() {
            this.form
                .transform(data => ({
                    ...data,
                    api_secret: data.provider === "AWS" ? data.api_secret : ""
                }))
                .post(route("vpsKeys.store"), {
                    preserveScroll: true,
                    onSuccess: () => { },
                    onFinish: () => {
                        this.form.reset();
                    }
                });
        }
    }
};
</script>
