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
            title="Sign In"
        >
            <form @submit.prevent="submit">
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
                                    id="email"
                                    required="required"
                                    autofocus="autofocus"
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
                                    required="required"
                                    id="password"
                                    :errorMsg="form.errors.password"
                                    @focus="clearErrors"
                                />
                                <inertia-link
                                    :href="route('password.request')"
                                    style="font-size: 0.8em;padding-left: 5px;line-height: 3em;"
                                >
                                    Forgot your password?
                                </inertia-link>
                            </ControlWrapper>

                            <Checkbox
                                class="col_6"
                                v-model="form.remember"
                                :value="form.remember"
                                title="Remember me"
                            />
                        </template>
                        <template slot="footer">
                            <Button
                                size="large"
                                width="full"
                                theme="primary"
                                :disabled="form.processing"
                                :success.sync="success"
                                :loading="form.processing"
                                @click="submit"
                            >
                                Log In
                            </Button>
                            <Button
                                tag="inertia-link"
                                :href="route('register')"
                                size="large"
                                theme="text"
                            >
                                Don't have an account?
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
import Card from "@/Components/Global/Card";
import Button from "@/Components/Global/Button";
import ModalContent from "@/Components/Global/ModalContent";
import ControlWrapper from "@/Components/Global/ControlWrapper";
import Input from "@/Components/Global/Input";
import Checkbox from "@/Components/Global/Checkbox";

export default {
    mixins: [FormMixin],
    components: {
        Card,
        Button,
        ModalContent,
        ControlWrapper,
        Input,
        Checkbox,
        LoginLayout
    },
    data: function() {
        return {
            form: this.$inertia.form({
                email: "",
                password: "",
                remember: false
            })
        };
    },
    computed: {
        disabled() {
            if (this.form.email.length > 0 && this.form.password.length > 0) {
                return false;
            } else {
                return true;
            }
        }
    },
    mounted() {},
    methods: {
        submit() {
            this.form
                .transform(data => ({
                    ...data,
                    remember: this.form.remember ? "on" : ""
                }))
                .post(this.route("login"), {
                    onSuccess: () => {
                        this.success = true;
                    },
                    onFinish: () => this.form.reset("password")
                });
        }
    }
};
</script>
