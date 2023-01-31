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
            title="Verify your eMail"
        >
            <ModalContent>
                <template slot="body">
                    <div class="col_6 mb-4 text-sm text-gray-600">
                        Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn't receive the email, we will gladly send you another.

                    </div>

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
                        Resend Verification Email
                    </Button>
                    <Button
                        size="large"
                        theme="text"
                        @click="logout"
                    >
                        Log Out
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
    props: {
        status: String
    },
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
            form: this.$inertia.form()
        };
    },
    computed: {
    },
    methods: {
        submit() {
            this.form.post(this.route('verification.send'), {
                onSuccess: () => {
                    this.success = true;

                    this.$notify({
                        group: "notifications",
                        text: "A new verification link has been sent to the email address you provided during registration.",
                        type: "success"
                    });
                }
            })
        },
        logout(){
            this.$inertia.post(route("logout"));
        }
    }
};
</script>




