<template>
    <Card
        :col="
            $mq === 'xl' ? '4' : $mq === 'llg' ? '6' : $mq === 'lg' ? '6' : '12'
        "
        headerBorder
        title="Your Profile Data"
    >
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
                        placeholder="my user name"
                        :errorMsg="form.errors.name"
                        @focus="clearErrors"
                    />
                </ControlWrapper>
                <ControlWrapper title="E-mail" col="6" showError>
                    <Input
                        v-model="form.email"
                        :errorMsg="form.errors.email"
                        placeholder="my user name"
                        type="email"
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
                    @click="saveData"
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
                _method: "PUT",
                name: this.user.name,
                email: this.user.email
            })
        };
    },
    computed: {
        disabled() {
            if (
                this.form.name.length > 0 &&
                this.form.email.length > 0 &&
                this.form.processing === false
            ) {
                return false;
            } else {
                return true;
            }
        }
    },
    mounted() {},
    methods: {
        saveData() {
            this.form.post(route("user-profile-information.update"), {
                errorBag: "updateProfileInformation",
                preserveScroll: true,
                onSuccess: () => (this.success = true)
            });
        }
    }
};
</script>
