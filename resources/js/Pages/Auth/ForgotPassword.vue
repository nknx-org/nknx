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
            title="Restore Password"
        >
            <ModalContent>
                <template slot="body">
                    <div class="col_6 mb-4 text-sm text-gray-600">
                        Forgot your password? No problem. Just let us know your
                        email address and we will email you a password reset
                        link that will allow you to choose a new one.
                    </div>
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
                        Restore
                    </Button>
                    <Button
                        tag="inertia-link"
                        :href="route('login')"
                        size="large"
                        theme="text"
                    >
                        Back to Log In
                    </Button>
                </template>
            </ModalContent>
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

export default {
    mixins: [FormMixin],
    components: {
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
                email: ""
            })
        };
    },
    computed: {
        disabled() {
            return !this.form.email.length;
        }
    },
    mounted() {},
    methods: {
        submit() {
            this.form.post(this.route("password.email"), {
                onSuccess: () => {
                    this.success = true;

                    this.$notify({
                        group: "notifications",
                        text: "We have emailed your password reset link!",
                        type: "success"
                    });
                }
            });
        }
    }
};
</script>
