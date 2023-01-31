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
            title="Password Confirmation"
        >
            <ModalContent>
                <template slot="body">
                    <div class="col_6 mb-4 text-sm text-gray-600">
                        This is a secure area of the application. Please confirm
                        your password before continuing.
                    </div>
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
                        Confirm
                    </Button>
                    <Button
                        tag="inertia-link"
                        :href="route('dashboard')"
                        size="large"
                        theme="text"
                    >
                        Back to Dashboard
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
                password: ""
            })
        };
    },
    computed: {
        disabled() {
            return !this.form.password.length;
        }
    },
    mounted() {},
    methods: {
        submit() {
            this.form.post(this.route("password.confirm"), {
                onSuccess: () => {
                    this.success = true;
                }
            });
        }
    }
};
</script>
