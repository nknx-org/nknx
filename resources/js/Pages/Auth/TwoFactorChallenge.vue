<template>
    <LoginLayout>
        <Card
            class="login__form"
            :col="
                $mq === 'xl'
                    ? '4'
                    : $mq === 'llg'
                    ? '6'
                    : $mq === 'lg'
                    ? '6'
                    : '12'
            "
            headerBorder
            title="Two Factor Auth"
        >
            <form @submit.prevent="submit">
                <ModalContent>
                    <template slot="body">
                        <div class="col_6 mb-4 text-sm text-gray-600">
                            {{ descr }}
                        </div>
                        <ControlWrapper
                            v-if="!recovery"
                            title="2FA Code"
                            col="6"
                            showError
                            :errorMsg="form.errors.code"
                        >
                            <Input
                                v-model="form.code"
                                :errorMsg="form.errors.code"
                                placeholder="Enter your 2FA digit code"
                                @focus="clearErrors"
                            />
                        </ControlWrapper>
                        <ControlWrapper
                            v-else
                            title="Recovery Code"
                            col="6"
                            showError
                            :errorMsg="form.errors.recovery_code"
                        >
                            <Input
                                v-model="form.recovery_code"
                                :errorMsg="form.errors.recovery_code"
                                placeholder="Enter your 2FA recovery phrase"
                                @focus="clearErrors"
                            />
                        </ControlWrapper>
                    </template>
                    <template slot="footer">
                        <Button
                            size="large"
                            width="full"
                            theme="primary"
                            :disabled="disabled"
                            :success.sync="success"
                            :loading="form.processing"
                            @click="submit"
                        >
                            Log In
                        </Button>
                        <Button
                            size="large"
                            theme="text"
                            @click="toggleRecovery"
                            v-text="
                                !recovery
                                    ? 'Use a recovery code'
                                    : 'Use an authentication code'
                            "
                        >
                        </Button>
                    </template>
                </ModalContent>
            </form>
        </Card>
    </LoginLayout>
</template>

<script>
import FormMixin from "@/Mixins/FormMixin.js";

import LoginLayout from "@/Layouts/LoginLayout";

import ContentWrapper from "@/Components/Global/ContentWrapper";
import Card from "@/Components/Global/Card";
import Button from "@/Components/Global/Button";
import ModalContent from "@/Components/Global/ModalContent";
import ControlWrapper from "@/Components/Global/ControlWrapper";
import Input from "@/Components/Global/Input";

export default {
    mixins: [FormMixin],
    components: {
        ContentWrapper,
        Card,
        Button,
        ModalContent,
        ControlWrapper,
        Input,
        LoginLayout
    },
    data: function() {
        return {
            recovery: false,
            form: this.$inertia.form({
                code: "",
                recovery_code: ""
            })
        };
    },
    computed: {
        disabled() {
            if (this.recovery) {
                return !this.form.recovery_code.length;
            } else {
                return !this.form.code.length;
            }
        },
        descr() {
            if (this.recovery) {
                return "Please confirm access to your account by entering one of your emergency recovery codes.";
            } else {
                return "Please confirm access to your account by entering the authentication code provided by your authenticator application.";
            }
        }
    },
    mounted() {},
    methods: {
        toggleRecovery() {
            this.recovery ^= true;

            this.$nextTick(() => {
                if (this.recovery) {
                    this.form.code = "";
                } else {
                    this.form.recovery_code = "";
                }
            });
        },
        submit() {
            this.form.post(this.route("two-factor.login"), {
                onSuccess: () => {
                    this.success = true;
                }
            });
        }
    }
};
</script>
