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
            title="Reset Password"
        >
            <ModalContent>
                <template slot="body">
                    <ControlWrapper
                        title="Email"
                        col="6"
                        showError
                        :errorMsg="form.errors.email"
                    >
                        <Input
                            v-model="form.email"
                            placeholder="user@mail.com"
                            type="email"
                            :errorMsg="form.errors.email"
                            @focus="clearErrors"
                        />
                    </ControlWrapper>
                    <ControlWrapper
                        title="Password"
                        col="6"
                        showError
                        :errorMsg="form.errors.password"
                    >
                        <Input
                            v-model="form.password"
                            type="password"
                            :errorMsg="form.errors.password"
                            @focus="clearErrors"
                        />
                    </ControlWrapper>
                    <ControlWrapper
                        title="Password Confirmation"
                        col="6"
                        showError
                        :errorMsg="form.errors.password_confirmation"
                    >
                        <Input
                            v-model="form.password_confirmation"
                            type="password"
                            :errorMsg="form.errors.password_confirmation"
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
                        Reset
                    </Button>
                </template>
            </ModalContent>
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
    props: {
        email: String,
        token: String
    },
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
            form: this.$inertia.form({
                token: this.token,
                email: this.email,
                password: "",
                password_confirmation: ""
            })
        };
    },
    computed: {
        disabled() {
            if (
                this.form.email.length > 0 &&
                this.form.password.length > 0 &&
                this.form.password_confirmation.length > 0
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
            this.form.post(this.route("password.update"), {
                onSuccess: () => {
                    this.success = true;
                },
                onFinish: () => {
                    this.form.reset("password");
                    this.form.reset("password_confirmation");
                }
            });
        }
    }
};
</script>
