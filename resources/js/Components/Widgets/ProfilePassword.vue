<template>
    <Card
        :col="
            $mq === 'xl' ? '4' : $mq === 'llg' ? '6' : $mq === 'lg' ? '6' : '12'
        "
        headerBorder
        title="Update Password"
    >
        <ModalContent>
            <template slot="body">
                <ControlWrapper
                    title="Current Password"
                    col="6"
                    showError
                    :errorMsg="form.errors.current_password"
                >
                    <Input
                        v-model="form.current_password"
                        placeholder="Enter current password"
                        :errorMsg="form.errors.current_password"
                        @focus="clearErrors"
                        type="password"
                    />
                </ControlWrapper>
                <ControlWrapper
                    title="New Password"
                    col="6"
                    :errorMsg="form.errors.password"
                    showError
                >
                    <Input
                        v-model="form.password"
                        :errorMsg="form.errors.password"
                        placeholder="Enter new password"
                        @focus="clearErrors"
                        type="password"
                    />
                </ControlWrapper>
                <ControlWrapper
                    title="Confirm Password"
                    col="6"
                    :errorMsg="form.errors.password_confirmation"
                    showError
                >
                    <Input
                        v-model="form.password_confirmation"
                        :errorMsg="form.errors.password_confirmation"
                        placeholder="Enter password confirmation"
                        @focus="clearErrors"
                        type="password"
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
                    @click="updatePassword"
                >
                    Update
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

export default {
    props: ["user"],
    mixins: [FormMixin],
    components: {
        Card,
        Button,
        ModalContent,
        ControlWrapper,
        Input
    },
    data: function() {
        return {
            form: this.$inertia.form({
                current_password: "",
                password: "",
                password_confirmation: ""
            }),
            success: false
        };
    },
    computed: {
        disabled() {
            if (
                this.form.current_password.length > 0 &&
                this.form.password.length > 0 &&
                this.form.password === this.form.password_confirmation
            ) {
                return false;
            } else {
                return true;
            }
        }
    },
    mounted() {},
    methods: {
        updatePassword() {
            this.form.put(route("user-password.update"), {
                errorBag: "updatePassword",
                preserveScroll: true,
                onSuccess: () => {
                    this.success = true;
                    this.form.reset();
                },
                onError: () => {
                    if (this.form.errors.password) {
                        this.form.reset("password", "password_confirmation");
                    }

                    if (this.form.errors.current_password) {
                        this.form.reset("current_password");
                    }
                }
            });
        }
    }
};
</script>
