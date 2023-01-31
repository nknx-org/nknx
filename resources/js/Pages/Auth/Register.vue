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
            title="Create your NKNx account"
        >
            <form @submit.prevent="submit">
                <ModalContent>
                    <template slot="body">
                        <ControlWrapper
                            title="Username"
                            col="6"
                            showError
                            :errorMsg="form.errors.name"
                        >
                            <Input
                                v-model="form.name"
                                placeholder="aleks123"
                                :errorMsg="form.errors.name"
                                @focus="clearErrors"
                            />
                        </ControlWrapper>
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
                        <Checkbox
                            class="col_6"
                            v-model="form.terms"
                            :value="form.terms"
                        >
                            <template slot="title">
                                I agree to the
                                <inertia-link
                                    href="/terms-of-service"
                                    class="text_link"
                                    >Terms of Service</inertia-link
                                >
                                and
                                <inertia-link
                                    href="/privacy-policy"
                                    class="text_link"
                                    >Privacy Policy</inertia-link
                                >
                            </template>
                        </Checkbox>
                    </template>
                    <template slot="footer">
                        <Button
                            size="large"
                            theme="primary"
                            width="full"
                            :disabled="disabled"
                            :success.sync="success"
                            :loading="form.processing"
                            @click="submit"
                        >
                            Register
                        </Button>
                        <Button
                            tag="inertia-link"
                            :href="route('login')"
                            size="large"
                            theme="text"
                        >
                            Already have an account?
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
import Checkbox from "@/Components/Global/Checkbox";

export default {
    mixins: [FormMixin],
    components: {
        ContentWrapper,
        Card,
        Button,
        ModalContent,
        ControlWrapper,
        Input,
        LoginLayout,
        Checkbox
    },
    data: function() {
        return {
            form: this.$inertia.form({
                name: "",
                email: "",
                password: "",
                password_confirmation: "",
                terms: false
            })
        };
    },
    computed: {
        disabled() {
            if (
                this.form.email.length > 0 &&
                this.form.password.length > 0 &&
                this.form.name.length > 0 &&
                this.form.password_confirmation.length > 0 &&
                this.form.terms === true
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
            this.form.post(this.route("register"), {
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
