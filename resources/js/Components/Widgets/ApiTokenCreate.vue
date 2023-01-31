<template>
    <Card
        :col="
            $mq === 'xl' ? '4' : $mq === 'llg' ? '6' : $mq === 'lg' ? '6' : '12'
        "
        headerBorder
        title="Create API Token"
    >
        <ModalContent>
            <template slot="body">
                <div class="col_6 max-w-xl text-sm text-gray-600">
                    API tokens allow third-party services to authenticate with
                    our application on your behalf.
                </div>
                <ControlWrapper
                    title="Name"
                    col="6"
                    showError
                    :errorMsg="form.errors.name"
                >
                    <Input
                        v-model="form.name"
                        placeholder="My API token #1"
                        :errorMsg="form.errors.name"
                        @focus="clearErrors"
                    />
                </ControlWrapper>
                <ControlWrapper title="Permissions" col="6" type="row">
                    <Checkbox
                        v-for="permission in availablePermissions"
                        :key="permission"
                        v-model="form.permissions"
                        :value="permission"
                        :title="permission"
                    />
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

        <ModalCard
            v-show="displayToken"
            :show.sync="displayToken"
            title="API Token"
            subtitle="User Profile"
            descr="API tokens allow third-party services to authenticate with our
                application on your behalf."
        >
            <template v-slot:body>
                <div class="col_6">
                    <div class="text-sm text-gray-600">
                        <p class="font-semibold">
                            Please copy your new API token. For your security,
                            it won't be shown again.
                        </p>
                    </div>

                    <div
                        class="mt-4 bg-gray-100 px-4 py-2 rounded font-mono text-sm text-gray-500 copy"
                        v-if="$page.props.jetstream.flash.token"
                        v-clipboard:copy="$page.props.jetstream.flash.token"
                    >
                        {{ $page.props.jetstream.flash.token }}
                        <svg-vue class="copy__icon" icon="copy"></svg-vue>
                    </div>
                </div>
            </template>

            <template v-slot:footer>
                <Button size="large" theme="text" @click="displayToken = false">
                    Close
                </Button>
            </template>
        </ModalCard>
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

export default {
    props: ["defaultPermissions", "availablePermissions"],
    mixins: [FormMixin],
    components: {
        Card,
        Button,
        ModalContent,
        ControlWrapper,
        Input,
        Checkbox,
        ModalCard
    },
    data: function() {
        return {
            form: this.$inertia.form({
                name: "",
                permissions: this.defaultPermissions
            }),
            displayToken: false
        };
    },
    computed: {
        disabled() {
            if (
                this.form.name.length > 0 &&
                this.form.processing === false &&
                this.form.permissions.length > 0
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
            this.form.post(route("api-tokens.store"), {
                preserveScroll: true,
                onSuccess: () => {
                    this.success = true;
                    this.displayToken = true;
                },
                onFinish: () => {
                    this.form.reset();
                }
            });
        }
    }
};
</script>
